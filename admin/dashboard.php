<?php
require_once 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }

$donors    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM donor"))[0];
$patients  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM patient"))[0];
$hospitals = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM hospital"))[0];
$staff     = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM staff"))[0];
$pending   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM request WHERE Status='Pending'"))[0];
$approved  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM request WHERE Status='Approved'"))[0];
$donations = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM donation"))[0];
$expiring  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM blood WHERE Expiry_Date <= DATE_ADD(NOW(), INTERVAL 7 DAY) AND Expiry_Date >= NOW()"))[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Dashboard – Blood Bank</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-header">
        <div><h1>Dashboard</h1><p>Welcome back, <strong><?php echo htmlspecialchars($_SESSION['admin']); ?></strong></p></div>
    </div>
    <div class="stats-grid">
        <a href="view_donors.php" class="stat-card"><div class="stat-icon">👥</div><div class="stat-val"><?php echo $donors; ?></div><div class="stat-lbl">Donors</div></a>
        <a href="view_donations.php" class="stat-card"><div class="stat-icon">💉</div><div class="stat-val"><?php echo $donations; ?></div><div class="stat-lbl">Donations</div></a>
        <a href="view_patients.php" class="stat-card"><div class="stat-icon">🩺</div><div class="stat-val"><?php echo $patients; ?></div><div class="stat-lbl">Patient Records</div></a>
        <a href="view_hospitals.php" class="stat-card"><div class="stat-icon">🏥</div><div class="stat-val"><?php echo $hospitals; ?></div><div class="stat-lbl">Hospitals</div></a>
        <a href="view_staff.php" class="stat-card"><div class="stat-icon">🧑‍⚕️</div><div class="stat-val"><?php echo $staff; ?></div><div class="stat-lbl">Staff</div></a>
        <a href="view_requests.php" class="stat-card <?php echo $pending>0?'stat-warn':''; ?>"><div class="stat-icon">📋</div><div class="stat-val"><?php echo $pending; ?></div><div class="stat-lbl">Pending Requests</div></a>
        <a href="view_requests.php" class="stat-card"><div class="stat-icon">✅</div><div class="stat-val"><?php echo $approved; ?></div><div class="stat-lbl">Approved Requests</div></a>
        <a href="view_stock.php" class="stat-card <?php echo $expiring>0?'stat-danger':''; ?>"><div class="stat-icon">⚠️</div><div class="stat-val"><?php echo $expiring; ?></div><div class="stat-lbl">Expiring (7 days)</div></a>
    </div>
    <h2 class="section-title">Quick Actions</h2>
    <div class="action-grid">
        <a href="add_donor.php"    class="action-card"><span class="ac-icon">👥</span><span>Add Donor</span></a>
        <a href="add_donation.php" class="action-card"><span class="ac-icon">💉</span><span>Record Donation</span></a>
        <a href="add_patient.php"  class="action-card"><span class="ac-icon">🩺</span><span>Add Patient</span></a>
        <a href="add_hospital.php" class="action-card"><span class="ac-icon">🏥</span><span>Add Hospital</span></a>
        <a href="add_request.php"  class="action-card"><span class="ac-icon">📋</span><span>New Request</span></a>
        <a href="add_staff.php"    class="action-card"><span class="ac-icon">🧑‍⚕️</span><span>Add Staff</span></a>
    </div>
</div>
</body></html>
