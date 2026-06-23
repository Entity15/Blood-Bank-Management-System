<?php
// ── Unified Login — routes to admin panel or user portal based on credentials
if (session_status() === PHP_SESSION_NONE) session_start();

// Already logged in?
if (isset($_SESSION['admin']))   { header("Location: admin/dashboard.php");  exit(); }
if (isset($_SESSION['user_id'])) { header("Location: user/dashboard.php");   exit(); }

$conn  = mysqli_connect("localhost","root","","blood_bank_management_system");
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = mysqli_real_escape_string($conn, trim($_POST['identifier']));
    $password   = mysqli_real_escape_string($conn, $_POST['password']);

    // 1️⃣ Check admin table (username match)
    $adminRow = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT * FROM admin WHERE Username='$identifier' AND Password='$password' LIMIT 1"));

    if ($adminRow) {
        $_SESSION['admin'] = $adminRow['Username'];
        header("Location: admin/dashboard.php"); exit();
    }

    // 2️⃣ Check user table (email match)
    $userRow = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT u.*, h.Name AS HospName FROM user u
         LEFT JOIN hospital h ON u.Hospital_ID=h.Hospital_ID
         WHERE u.Email='$identifier' AND u.Password='$password' LIMIT 1"));

    if ($userRow) {
        $_SESSION['user_id']      = $userRow['User_ID'];
        $_SESSION['user_name']    = $userRow['Full_Name'];
        $_SESSION['user_email']   = $userRow['Email'];
        $_SESSION['user_bg']      = $userRow['Blood_Group'];
        $_SESSION['user_hosp_id'] = $userRow['Hospital_ID'];
        $_SESSION['user_hosp']    = $userRow['HospName'];
        header("Location: user/dashboard.php"); exit();
    }

    $error = "Invalid credentials. Try your admin username or your registered email.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Login – Blood Bank</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Lora:wght@700&display=swap');
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:linear-gradient(145deg,#0a3a3a 0%,#0f5c5c 50%,#c0392b 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
.card{background:#fff;border-radius:20px;padding:52px 44px;width:100%;max-width:440px;box-shadow:0 24px 64px rgba(0,0,0,.32);text-align:center}
.logo{font-size:3rem;margin-bottom:10px;display:block;animation:hbeat .9s ease-in-out infinite alternate}
@keyframes hbeat{from{transform:scale(1)}to{transform:scale(1.15)}}
h1{font-family:'Lora',serif;font-size:1.9rem;font-weight:700;color:#0f5c5c;margin-bottom:4px}
.sub{color:#9ca3af;font-size:.875rem;margin-bottom:28px}
.hint-strip{display:flex;gap:10px;margin-bottom:24px}
.hint{flex:1;background:#f0f9f9;border:1px solid #b2d8d8;border-radius:10px;padding:10px 12px;font-size:.78rem;color:#0f5c5c;text-align:left;line-height:1.5}
.hint strong{display:block;font-weight:700;margin-bottom:2px}
.alert{padding:11px 14px;border-radius:8px;background:#fdf0f0;color:#b02a2a;border-left:4px solid #b02a2a;font-size:.875rem;font-weight:500;margin-bottom:18px;text-align:left}
label{display:block;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#6b7280;margin-bottom:5px;text-align:left}
input{width:100%;padding:11px 14px;border:1.5px solid #e1e4e8;border-radius:8px;font-family:inherit;font-size:.9rem;color:#1f2937;outline:none;transition:border-color .18s,box-shadow .18s;margin-bottom:18px}
input:focus{border-color:#0f5c5c;box-shadow:0 0 0 3px rgba(15,92,92,.12)}
.form-group{text-align:left;margin-bottom:18px}
.form-group input{margin-bottom:0}
button{width:100%;padding:13px;background:#0f5c5c;color:#fff;border:none;border-radius:8px;font-family:inherit;font-size:.95rem;font-weight:700;cursor:pointer;transition:filter .15s}
button:hover{filter:brightness(1.1)}
.back{display:block;margin-top:20px;font-size:.85rem;color:#9ca3af;text-decoration:none}
.back:hover{color:#0f5c5c}
.divider{display:flex;align-items:center;gap:12px;margin:20px 0;color:#9ca3af;font-size:.8rem}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:#e1e4e8}
.reg-link{font-size:.875rem;color:#9ca3af}
.reg-link a{color:#0f5c5c;font-weight:700}
</style>
</head>
<body>
<div class="card">
    <span class="logo">🩸</span>
    <h1>Blood Bank</h1>
    <p class="sub">Management System · NEUB</p>

    <div class="hint-strip">
        <div class="hint"><strong>👤 User Portal</strong>Log in with your registered email address</div>
        <div class="hint"><strong>🔒 Admin Panel</strong>Log in with your admin username</div>
    </div>

    <?php if ($error): ?>
    <div class="alert"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Username or Email</label>
            <input type="text" name="identifier" required autocomplete="username"
                   value="<?php echo htmlspecialchars($_POST['identifier'] ?? ''); ?>"
                   placeholder="admin username or user@email.com">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
        </div>
        <button type="submit">Login →</button>
    </form>

    <div class="divider">new here?</div>
    <p class="reg-link">Don't have a user account? <a href="user/register.php">Register</a></p>
    <a href="visitor/index.php" class="back">← Back to public site</a>
</div>
</body>
</html>
