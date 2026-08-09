<?php
require_once 'config.php';
require_user_login();
$u   = current_user();
$uid = $u['id'];

$rid = (int)($_GET['id'] ?? $_POST['request_id'] ?? 0);

// Load the request and make sure it belongs to this user
$req = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT * FROM request WHERE Request_ID=$rid AND User_ID=$uid"));

if (!$req) {
    header("Location: my_requests.php"); exit();
}

// Only a Pending request can still be edited — once an admin has approved
// or rejected it, the record is locked.
if ($req['Status'] !== 'Pending') {
    $_SESSION['flash_err'] = "This request has already been reviewed and can no longer be edited.";
    header("Location: my_requests.php"); exit();
}

$error = '';

// Blood stock availability (same logic as new_request.php)
$stock = [];
$stock_res = mysqli_query($conn, "
    SELECT b.Blood_Group,
           COALESCE(SUM(b.Units),0)
           - COALESCE((SELECT SUM(dtr.Units_Provided)
                        FROM donation_to_request dtr
                        JOIN donation d2 ON dtr.Donation_ID=d2.Donation_ID
                        JOIN blood b2    ON d2.Blood_ID=b2.Blood_ID
                        WHERE b2.Blood_Group=b.Blood_Group),0) AS Available
    FROM blood b WHERE b.Expiry_Date >= CURDATE()
    GROUP BY b.Blood_Group");
while ($r = mysqli_fetch_assoc($stock_res)) $stock[$r['Blood_Group']] = max(0,(int)$r['Available']);

$hospitals = mysqli_query($conn, "
    SELECT h.Hospital_ID, h.Name, h.City FROM hospital h
    LEFT JOIN contract c ON h.Hospital_ID=c.Hospital_ID
    WHERE c.End_Date >= CURDATE() OR c.End_Date IS NULL
    GROUP BY h.Hospital_ID ORDER BY h.Name");
$patients = mysqli_query($conn, "SELECT Patient_Disease_ID, Name, Disease_Name FROM patient ORDER BY Name, Disease_Name");
$pcount   = mysqli_num_rows($patients);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hid  = (int)$_POST['hospital_id'];
    $pid  = (int)$_POST['patient_id'];
    $bg   = mysqli_real_escape_string($conn, $_POST['blood_group']);
    $u_n  = (int)$_POST['units'];
    $dt   = mysqli_real_escape_string($conn, $_POST['request_date']);

    if (!$hid || !$pid || !$bg || !$u_n || !$dt) {
        $error = "All required fields must be filled in.";
    } elseif ($u_n < 1 || $u_n > 20) {
        $error = "Units must be between 1 and 20.";
    } else {
        $avail = $stock[$bg] ?? 0;
        if ($avail === 0) {
            $error = "Sorry, there is currently no $bg blood in stock. Your request cannot be updated to this group right now.";
        } else {
            // Re-confirm it's still Pending and still ours right before writing (avoid a race
            // where an admin approved/rejected it between page load and submit).
            $still = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT Status FROM request WHERE Request_ID=$rid AND User_ID=$uid"));
            if (!$still || $still['Status'] !== 'Pending') {
                $_SESSION['flash_err'] = "This request was just reviewed by an admin and can no longer be edited.";
                header("Location: my_requests.php"); exit();
            }
            mysqli_query($conn, "
                UPDATE request
                SET Patient_ID=$pid, Hospital_ID=$hid, Blood_Group='$bg', Units=$u_n, Request_Date='$dt'
                WHERE Request_ID=$rid AND User_ID=$uid AND Status='Pending'");
            $_SESSION['flash_ok'] = "Your request has been updated.";
            header("Location: my_requests.php"); exit();
        }
    }

    // Keep the request array in sync with the failed submission so the form re-shows what was typed
    $req = array_merge($req, [
        'Hospital_ID' => $hid, 'Patient_ID' => $pid, 'Blood_Group' => $bg,
        'Units' => $u_n, 'Request_Date' => $dt,
    ]);

    // Reload selects after failed POST
    $hospitals = mysqli_query($conn, "
        SELECT h.Hospital_ID, h.Name, h.City FROM hospital h
        LEFT JOIN contract c ON h.Hospital_ID=c.Hospital_ID
        WHERE c.End_Date >= CURDATE() OR c.End_Date IS NULL
        GROUP BY h.Hospital_ID ORDER BY h.Name");
    $patients = mysqli_query($conn, "SELECT Patient_Disease_ID, Name, Disease_Name FROM patient ORDER BY Name, Disease_Name");
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Edit Blood Request – LifeBank</title>
<link rel="stylesheet" href="user_style.css">
</head><body>
<?php include 'navbar.php'; ?>

<div class="page-hero">
    <div class="page-hero-inner">
        <h1>Edit Blood Request</h1>
        <p>You can update this request until an admin reviews it. Once it's approved or rejected, it's locked.</p>
    </div>
</div>

<div class="page-wrap">
    <div style="display:flex;gap:28px;align-items:start;flex-wrap:wrap;">

        <!-- FORM -->
        <div style="flex:1;min-width:300px;">
            <?php if ($error): ?>
            <div class="alert alert-err"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="form-card">
                <form method="POST">
                    <input type="hidden" name="request_id" value="<?= $req['Request_ID'] ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Hospital <span class="req">*</span></label>
                            <select name="hospital_id" class="form-select" required>
                                <option value="">— Select Hospital —</option>
                                <?php while ($h = mysqli_fetch_assoc($hospitals)):
                                    $sel = ($req['Hospital_ID'] == $h['Hospital_ID']) ? 'selected' : ''; ?>
                                <option value="<?= $h['Hospital_ID'] ?>" <?= $sel ?>>
                                    <?= htmlspecialchars($h['Name']) ?><?= $h['City'] ? ' · '.$h['City'] : '' ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Patient Record <span class="req">*</span></label>
                            <select name="patient_id" class="form-select" required>
                                <option value="">— Select Patient —</option>
                                <?php while ($p = mysqli_fetch_assoc($patients)):
                                    $sel = ($req['Patient_ID'] == $p['Patient_Disease_ID']) ? 'selected' : ''; ?>
                                <option value="<?= $p['Patient_Disease_ID'] ?>" <?= $sel ?>>
                                    <?= htmlspecialchars($p['Name']) ?> — <?= htmlspecialchars($p['Disease_Name']) ?>
                                </option>
                                <?php endwhile; ?>
                                <?php if ($pcount === 0): ?>
                                <option disabled>No patients registered yet</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Blood Group Required <span class="req">*</span></label>
                            <select name="blood_group" class="form-select" required>
                                <option value="">— Select —</option>
                                <?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $g):
                                    $sel = ($req['Blood_Group'] === $g) ? 'selected' : '';
                                ?>
                                <option value="<?= $g ?>" <?= $sel ?>><?= $g ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="form-hint">Requests for groups currently out of stock will be blocked on submission — check the live stock panel to the right.</p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Units Required <span class="req">*</span></label>
                            <input type="number" name="units" class="form-input" min="1" max="20" required
                                   value="<?= htmlspecialchars($req['Units']) ?>">
                            <p class="form-hint">Maximum 20 units per request.</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Request Date <span class="req">*</span></label>
                        <input type="date" name="request_date" class="form-input"
                               value="<?= htmlspecialchars($req['Request_Date']) ?>" required>
                    </div>
                    <div style="display:flex;gap:12px;flex-wrap:wrap;">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="my_requests.php" class="btn btn-ghost">Cancel Editing</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- STOCK SIDEBAR -->
        <div style="width:240px;flex-shrink:0;">
            <div class="sec-eyebrow">Live Stock</div>
            <h3 style="font-family:'Lora',serif;font-size:1.1rem;font-weight:700;color:var(--ink);margin-bottom:14px;">Available Right Now</h3>
            <div style="display:flex;flex-direction:column;gap:8px;">
                <?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $g):
                    $avail = $stock[$g] ?? 0;
                    $cls   = $avail === 0 ? 'critical' : ($avail < 5 ? 'low' : 'ok');
                    $lbl   = $avail === 0 ? 'Out of stock' : ($avail < 5 ? "$avail units – Low" : "$avail units");
                ?>
                <div style="display:flex;align-items:center;justify-content:space-between;
                            background:var(--white);border:1px solid var(--ivory-2);
                            border-radius:8px;padding:9px 12px;box-shadow:var(--shadow-xs);">
                    <span style="font-family:'DM Mono',monospace;font-weight:500;color:var(--teal);"><?= $g ?></span>
                    <span class="pill <?= $cls ?>"><?= $lbl ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>

<footer style="background:var(--ink);color:rgba(255,255,255,.4);text-align:center;padding:22px;font-size:.78rem;margin-top:48px;">
    <strong style="color:rgba(255,255,255,.7);">LifeBank</strong> · User Portal · North East University Bangladesh, CSE Dept
</footer>
</body></html>
