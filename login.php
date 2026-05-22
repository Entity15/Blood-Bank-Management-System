<?php
include 'config.php';
$error = '';
if (isset($_POST['login'])) {
    $u = mysqli_real_escape_string($conn, $_POST['username']);
    $p = mysqli_real_escape_string($conn, $_POST['password']);
    $q = mysqli_query($conn, "SELECT * FROM admin WHERE Username='$u' AND Password='$p'");
    if (mysqli_num_rows($q) > 0) {
        $_SESSION['admin'] = $u;
        header("Location: dashboard.php"); exit();
    } else { $error = "Invalid username or password."; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Login – Blood Bank</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
<div class="login-box">
    <div class="login-logo">🩸</div>
    <h1>Blood Bank</h1>
    <p class="login-sub">Management System · NEUB</p>
    <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="POST">
        <div class="form-group"><label>Username</label><input type="text" name="username" required autocomplete="username"></div>
        <div class="form-group"><label>Password</label><input type="password" name="password" required autocomplete="current-password"></div>
        <button type="submit" name="login" class="btn btn-primary btn-full">Login</button>
    </form>
</div>
</body></html>
