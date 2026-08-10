<?php
require_once 'config.php';
require_user_login();
$u   = current_user();
$uid = $u['id'];

$error = $success = '';

// Blood stock availability
$stock = [];
$stock_res = mysqli_query($conn, "
    SELECT b.Blood_Group,
           COALESCE(SUM(b.Units),0)
           - COALESCE((SELECT SUM(dtr.Units_Provided)
                        FROM donation_to_request dtr
                        JOIN donation d2 ON dtr.Donation_ID=d2.Donation_ID
                        JOIN blood b2    ON d2.Blood_ID=b2.Blood_ID
                        WHERE b2.Blood_Group=b.Blood_Group AND b2.Expiry_Date >= CURDATE()),0) AS Available
    FROM blood b WHERE b.Expiry_Date >= CURDATE()
    GROUP BY b.Blood_Group");
while ($r = mysqli_fetch_assoc($stock_res)) $stock[$r['Blood_Group']] = max(0,(int)$r['Available']);

// Load hospitals (only those with active contracts)
$hospitals = mysqli_query($conn, "
    SELECT h.Hospital_ID, h.Name, h.City
    FROM hospital h
    JOIN contract c ON h.Hospital_ID=c.Hospital_ID
    WHERE c.End_Date >= CURDATE()
    ORDER BY h.Name");

// If no active contract hospitals, fall back to all
$hcount = mysqli_num_rows($hospitals);
if ($hcount === 0) {
    $hospitals = mysqli_query($conn, "SELECT Hospital_ID, Name, City FROM hospital ORDER BY Name");
}

// Patients
$patients = mysqli_query($conn, "SELECT Patient_Disease_ID, Name, Disease_Name FROM patient ORDER BY Name, Disease_Name");
$pcount   = mysqli_num_rows($patients);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $hid = (int)$_POST['hospital_id'];
    $pid = (int)$_POST['patient_id'];
    $bg  = mysqli_real_escape_string($conn, $_POST['blood_group']);
    $u_n = (int)$_POST['units'];
    $dt  = mysqli_real_escape_string($conn, $_POST['request_date']);
    $note= mysqli_real_escape_string($conn, trim($_POST['notes'] ?? ''));

    if (!$hid || !$pid || !$bg || !$u_n || !$dt) {
        $error = "All required fields must be filled in.";
    } elseif ($u_n < 1 || $u_n > 20) {
        $error = "Units must be between 1 and 20.";
    } else {
        // Check stock
        $avail = $stock[$bg] ?? 0;
        if ($avail === 0) {
            $error = "Sorry, there is currently no $bg blood in stock. Your request cannot be submitted at this time.";
        } else {
            $r = mysqli_query($conn,
                "INSERT INTO request (Patient_ID, Hospital_ID, Blood_Group, Units, Request_Date, Status, User_ID)
                 VALUES ($pid, $hid, '$bg', $u_n, '$dt', 'Pending', $uid)");
            if ($r) {
                $success = "Your blood request has been submitted and is now pending admin review.";
            } else {
                // Try without User_ID in case the column doesn't exist yet
                $r2 = mysqli_query($conn,
                    "INSERT INTO request (Patient_ID, Hospital_ID, Blood_Group, Units, Request_Date, Status)
                     VALUES ($pid, $hid, '$bg', $u_n, '$dt', 'Pending')");
                if ($r2) {
                    $success = "Your blood request has been submitted and is now pending admin review.";
                } else {
                    $error = "Submission failed: " . mysqli_error($conn);
                }
            }
        }
    }
}

// Reload selects after POST
$hospitals = mysqli_query($conn, "
    SELECT h.Hospital_ID, h.Name, h.City FROM hospital h
    LEFT JOIN contract c ON h.Hospital_ID=c.Hospital_ID
    WHERE c.End_Date >= CURDATE() OR c.End_Date IS NULL
    GROUP BY h.Hospital_ID ORDER BY h.Name");
$patients  = mysqli_query($conn, "SELECT Patient_Disease_ID, Name, Disease_Name FROM patient ORDER BY Name, Disease_Name");
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>New Blood Request – LifeBank</title>
<link rel="stylesheet" href="user_style.css">
</head><body>
<?php include 'navbar.php'; ?>

<div class="page-hero">
    <div class="page-hero-inner">
        <h1>New Blood Request</h1>
        <p>Submit a request for blood on behalf of a registered patient. An admin will review and approve it.</p>
    </div>
</div>

<div class="page-wrap">
    <div style="display:flex;gap:28px;align-items:start;flex-wrap:wrap;">

        <!-- FORM -->
        <div style="flex:1;min-width:300px;">
            <?php if ($success): ?>
            <div class="alert alert-ok" style="font-size:1rem;">
                ✅ <?= $success ?><br>
                <div style="margin-top:10px;display:flex;gap:10px;">
                    <a href="my_requests.php" class="btn btn-primary" style="padding:8px 18px;font-size:.85rem;">View My Requests</a>
                    <a href="new_request.php" class="btn btn-ghost"  style="padding:8px 18px;font-size:.85rem;">Make Another</a>
                </div>
            </div>
            <?php endif; ?>
            <?php if ($error): ?>
            <div class="alert alert-err"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if (!$success): ?>
            <div class="form-card">
                <form method="POST"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Hospital <span class="req">*</span></label>
                            <select name="hospital_id" class="form-select" required>
                                <option value="">— Select Hospital —</option>
                                <?php while ($h = mysqli_fetch_assoc($hospitals)):
                                    $sel = (($_POST['hospital_id'] ?? 0) == $h['Hospital_ID']) ? 'selected' : ''; ?>
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
                                    $sel = (($_POST['patient_id'] ?? 0) == $p['Patient_Disease_ID']) ? 'selected' : ''; ?>
                                <option value="<?= $p['Patient_Disease_ID'] ?>" <?= $sel ?>>
                                    <?= htmlspecialchars($p['Name']) ?> — <?= htmlspecialchars($p['Disease_Name']) ?>
                                </option>
                                <?php endwhile; ?>
                                <?php if ($pcount === 0): ?>
                                <option disabled>No patients registered yet</option>
                                <?php endif; ?>
                            </select>
                            <p class="form-hint">Patient must be pre-registered by a blood bank administrator.</p>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Blood Group Required <span class="req">*</span></label>
                            <select name="blood_group" class="form-select" required id="bg-select">
                                <option value="">— Select —</option>
                                <?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $g):
                                    $sel = (($_POST['blood_group'] ?? '') === $g) ? 'selected' : '';
                                ?>
                                <option value="<?= $g ?>" <?= $sel ?>><?= $g ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="form-hint">Requests for groups currently out of stock will be blocked on submission — check the live stock panel to the right.</p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Units Required <span class="req">*</span></label>
                            <input type="number" name="units" class="form-input" min="1" max="20" required
                                   value="<?= htmlspecialchars($_POST['units'] ?? '') ?>">
                            <p class="form-hint">Maximum 20 units per request.</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Request Date <span class="req">*</span></label>
                        <input type="date" name="request_date" class="form-input"
                               value="<?= htmlspecialchars($_POST['request_date'] ?? date('Y-m-d')) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Additional Notes</label>
                        <textarea name="notes" class="form-textarea" placeholder="Any urgency details, doctor's name, etc."><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                    </div>
                    <div style="display:flex;gap:12px;flex-wrap:wrap;">
                        <button type="submit" class="btn btn-primary">Submit Request</button>
                        <a href="my_requests.php" class="btn btn-ghost">Cancel</a>
                    </div>
                </form>
            </div>
            <?php endif; ?>
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
            <p style="font-size:.75rem;color:var(--muted);margin-top:10px;">
                Updated in real-time from the blood bank database.
            </p>
        </div>

    </div>
</div>

<footer style="background:var(--ink);color:rgba(255,255,255,.4);text-align:center;padding:22px;font-size:.78rem;margin-top:48px;">
    <strong style="color:rgba(255,255,255,.7);">LifeBank</strong> · User Portal · North East University Bangladesh, CSE Dept
</footer>
</body></html>
