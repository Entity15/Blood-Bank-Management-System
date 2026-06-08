<?php
require_once 'config.php';
if (isset($_SESSION['user_id'])) { header("Location: dashboard.php"); exit(); }

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim(mysqli_real_escape_string($conn, $_POST['full_name']));
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $pass  = mysqli_real_escape_string($conn, $_POST['password']);
    $pass2 = $_POST['password2'];
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
    $bg    = mysqli_real_escape_string($conn, $_POST['blood_group'] ?? '');
    $addr  = mysqli_real_escape_string($conn, trim($_POST['address'] ?? ''));
    $hid   = (int)($_POST['hospital_id'] ?? 0);

    if (!$name || !$email || !$pass)            $error = "Name, email and password are required.";
    elseif ($_POST['password'] !== $pass2)      $error = "Passwords do not match.";
    elseif (strlen($_POST['password']) < 6)     $error = "Password must be at least 6 characters.";
    else {
        $exists = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM user WHERE Email='$email'"))[0];
        if ($exists) {
            $error = "An account with that email already exists.";
        } else {
            $today  = date('Y-m-d');
            $hval   = $hid ? $hid : 'NULL';
            $bgval  = $bg  ? "'$bg'" : 'NULL';
            $r = mysqli_query($conn,
                "INSERT INTO user (Full_Name,Email,Password,Phone,Blood_Group,Address,Hospital_ID,Date_Registered)
                 VALUES ('$name','$email','$pass','$phone',$bgval,'$addr',$hval,'$today')");
            if ($r) {
                $success = "Account created! You can now log in.";
            } else {
                $error = "Registration failed: " . mysqli_error($conn);
            }
        }
    }
}

$hospitals = mysqli_query($conn, "SELECT Hospital_ID, Name FROM hospital ORDER BY Name");
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Register – LifeBank User Portal</title>
<link rel="stylesheet" href="user_style.css">
</head><body class="auth-body">
<div class="auth-box" style="max-width:520px;padding:44px 40px;">
    <div class="auth-logo">🩸</div>
    <h1>Create Account</h1>
    <p class="auth-sub">LifeBank User Portal · NEUB</p>

    <?php if ($success): ?><div class="alert alert-ok"><?= $success ?> <a href="../login.php" style="font-weight:700;color:inherit;">Login →</a></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-err"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <?php if (!$success): ?>
    <form method="POST" style="text-align:left;">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Full Name <span class="req">*</span></label>
                <input type="text" name="full_name" class="form-input" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Email <span class="req">*</span></label>
                <input type="email" name="email" class="form-input" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Password <span class="req">*</span></label>
                <input type="password" name="password" class="form-input" required minlength="6">
            </div>
            <div class="form-group">
                <label class="form-label">Confirm Password <span class="req">*</span></label>
                <input type="password" name="password2" class="form-input" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-input" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Your Blood Group</label>
                <select name="blood_group" class="form-select">
                    <option value="">— Not sure —</option>
                    <?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $g):
                        $sel = (($_POST['blood_group'] ?? '') === $g) ? 'selected' : ''; ?>
                    <option <?= $sel ?>><?= $g ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Affiliated Hospital <span class="form-hint">(optional)</span></label>
            <select name="hospital_id" class="form-select">
                <option value="0">— None / Not affiliated —</option>
                <?php while($h = mysqli_fetch_assoc($hospitals)):
                    $sel = (($_POST['hospital_id'] ?? 0) == $h['Hospital_ID']) ? 'selected' : ''; ?>
                <option value="<?= $h['Hospital_ID'] ?>" <?= $sel ?>><?= htmlspecialchars($h['Name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-input" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
        </div>
        <button type="submit" class="btn btn-primary btn-full">Create Account</button>
    </form>
    <?php endif; ?>

    <div class="auth-switch">Already have an account? <a href="../login.php">Log in</a></div>
    <div class="auth-switch" style="margin-top:10px;"><a href="../visitor/index.php" style="color:var(--muted);">← Back to public site</a></div>
</div>
</body></html>
