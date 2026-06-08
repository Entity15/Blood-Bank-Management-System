<?php
require_once 'config.php';
require_user_login();
$u   = current_user();
$uid = $u['id'];

$error=$success='';

// Load full record
$row = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT u.*,h.Name AS HospName FROM user u LEFT JOIN hospital h ON u.Hospital_ID=h.Hospital_ID WHERE u.User_ID=$uid"));

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name  = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']??''));
    $bg    = mysqli_real_escape_string($conn, $_POST['blood_group']??'');
    $addr  = mysqli_real_escape_string($conn, trim($_POST['address']??''));
    $hid   = (int)($_POST['hospital_id']??0);

    // Password change
    $pass_sql='';
    if (!empty($_POST['new_password'])) {
        if ($_POST['new_password'] !== $_POST['confirm_password']) {
            $error="Passwords do not match.";
        } elseif (strlen($_POST['new_password']) < 6) {
            $error="Password must be at least 6 characters.";
        } else {
            $np  = mysqli_real_escape_string($conn,$_POST['new_password']);
            $cur = mysqli_real_escape_string($conn,$_POST['current_password']??'');
            if ($row['Password'] !== $cur) {
                $error="Current password is incorrect.";
            } else {
                $pass_sql=", Password='$np'";
            }
        }
    }

    if (!$error) {
        $hval = $hid ? $hid : 'NULL';
        $bgval = $bg ? "'$bg'" : 'NULL';
        mysqli_query($conn,
            "UPDATE user SET Full_Name='$name',Phone='$phone',Blood_Group=$bgval,Address='$addr',Hospital_ID=$hval $pass_sql WHERE User_ID=$uid");
        $_SESSION['user_name'] = $name;
        $_SESSION['user_bg']   = $bg;
        $success = "Profile updated successfully.";
        $row = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT u.*,h.Name AS HospName FROM user u LEFT JOIN hospital h ON u.Hospital_ID=h.Hospital_ID WHERE u.User_ID=$uid"));
    }
}

$hospitals = mysqli_query($conn,"SELECT Hospital_ID,Name FROM hospital ORDER BY Name");

// Stats
$my_requests = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM request WHERE User_ID=$uid"))[0];
$my_approved = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM request WHERE User_ID=$uid AND Status='Approved'"))[0];
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>My Profile – LifeBank</title>
<link rel="stylesheet" href="user_style.css">
</head><body>
<?php include 'navbar.php'; ?>
<div class="page-hero">
    <div class="page-hero-inner">
        <h1>My Profile</h1>
        <p>View and update your account information.</p>
    </div>
</div>
<div class="page-wrap">
    <?php if($error):   ?><div class="alert alert-err"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if($success): ?><div class="alert alert-ok"><?= $success ?></div><?php endif; ?>

    <div class="profile-grid">
        <!-- Sidebar card -->
        <div>
            <div class="profile-card">
                <div class="profile-avatar"><?= strtoupper(substr($row['Full_Name'],0,1)) ?></div>
                <div class="profile-name"><?= htmlspecialchars($row['Full_Name']) ?></div>
                <div class="profile-email"><?= htmlspecialchars($row['Email']) ?></div>
                <?php if($row['Blood_Group']): ?>
                <div class="profile-bg-badge"><?= $row['Blood_Group'] ?></div>
                <?php endif; ?>
                <div class="profile-meta">
                    <?php if($row['Phone']): ?><span>📞 <?= htmlspecialchars($row['Phone']) ?></span><?php endif; ?>
                    <?php if($row['Address']): ?><span>📍 <?= htmlspecialchars($row['Address']) ?></span><?php endif; ?>
                    <?php if($row['HospName']): ?><span>🏥 <?= htmlspecialchars($row['HospName']) ?></span><?php endif; ?>
                    <span>📅 Member since <?= $row['Date_Registered']??'—' ?></span>
                </div>
                <hr class="divider">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;text-align:center;">
                    <div>
                        <div style="font-family:'Lora',serif;font-size:1.5rem;font-weight:700;color:var(--teal);"><?= $my_requests ?></div>
                        <div style="font-size:.7rem;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);">Requests</div>
                    </div>
                    <div>
                        <div style="font-family:'Lora',serif;font-size:1.5rem;font-weight:700;color:var(--emerald);"><?= $my_approved ?></div>
                        <div style="font-size:.7rem;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);">Approved</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit form -->
        <div>
            <div class="form-card" style="max-width:100%;">
                <div class="sec-eyebrow">Account</div>
                <h2 class="sec-title" style="margin-bottom:20px;">Edit Details</h2>
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Full Name <span class="req">*</span></label>
                            <input type="text" name="full_name" class="form-input" required value="<?= htmlspecialchars($row['Full_Name']) ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-input" value="<?= htmlspecialchars($row['Email']) ?>" disabled style="opacity:.6;cursor:not-allowed;">
                            <p class="form-hint">Email cannot be changed.</p>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-input" value="<?= htmlspecialchars($row['Phone']??'') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Blood Group</label>
                            <select name="blood_group" class="form-select">
                                <option value="">— Unknown —</option>
                                <?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $g):
                                    $sel=($row['Blood_Group']===$g)?'selected':''; ?>
                                <option <?= $sel ?>><?= $g ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-input" value="<?= htmlspecialchars($row['Address']??'') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Affiliated Hospital</label>
                        <select name="hospital_id" class="form-select">
                            <option value="0">— None —</option>
                            <?php while($h=mysqli_fetch_assoc($hospitals)):
                                $sel=($row['Hospital_ID']==$h['Hospital_ID'])?'selected':''; ?>
                            <option value="<?= $h['Hospital_ID'] ?>" <?= $sel ?>><?= htmlspecialchars($h['Name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <hr class="divider">
                    <div class="sec-eyebrow">Security</div>
                    <h3 style="font-family:'Lora',serif;font-size:1.1rem;font-weight:700;color:var(--ink);margin:8px 0 16px;">Change Password</h3>
                    <p class="form-hint" style="margin-bottom:16px;">Leave blank to keep your current password.</p>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-input" autocomplete="current-password">
                        </div>
                        <div class="form-group"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-input" autocomplete="new-password" minlength="6">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-input" autocomplete="new-password">
                        </div>
                    </div>

                    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:8px;">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="dashboard.php" class="btn btn-ghost">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<footer style="background:var(--ink);color:rgba(255,255,255,.4);text-align:center;padding:22px;font-size:.78rem;margin-top:48px;">
    <strong style="color:rgba(255,255,255,.7);">LifeBank</strong> · User Portal · North East University Bangladesh, CSE Dept
</footer>
</body></html>
