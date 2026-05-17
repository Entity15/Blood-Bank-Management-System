<?php
require_once 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }

// Stats
$total_donors    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM donor"))[0];
$total_patients  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM patient"))[0];
$total_hospitals = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM hospital"))[0];
$pending_req     = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM blood_request WHERE Status='Pending'"))[0];
$total_units     = mysqli_fetch_row(mysqli_query($conn, "SELECT COALESCE(SUM(Units_Available),0) FROM blood_stock"))[0];
$expiring_soon   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM blood WHERE Expiry_Date <= DATE_ADD(NOW(), INTERVAL 7 DAY) AND Expiry_Date >= NOW()"))[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard – Blood Bank</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h1>Dashboard</h1>
        <p>Welcome back, <strong><?php echo htmlspecialchars($_SESSION['admin']); ?></strong>!</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-value"><?php echo $total_donors; ?></div>
            <div class="stat-label">Donors</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🏥</div>
            <div class="stat-value"><?php echo $total_hospitals; ?></div>
            <div class="stat-label">Hospitals</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🩺</div>
            <div class="stat-value"><?php echo $total_patients; ?></div>
            <div class="stat-label">Patients</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🩸</div>
            <div class="stat-value"><?php echo $total_units; ?></div>
            <div class="stat-label">Units in Stock</div>
        </div>
        <div class="stat-card <?php echo $pending_req > 0 ? 'stat-warning' : ''; ?>">
            <div class="stat-icon">📋</div>
            <div class="stat-value"><?php echo $pending_req; ?></div>
            <div class="stat-label">Pending Requests</div>
        </div>
        <div class="stat-card <?php echo $expiring_soon > 0 ? 'stat-danger' : ''; ?>">
            <div class="stat-icon">⚠️</div>
            <div class="stat-value"><?php echo $expiring_soon; ?></div>
            <div class="stat-label">Expiring (7 days)</div>
        </div>
    </div>

    <div class="quick-actions">
        <h2>Quick Actions</h2>
        <div class="action-grid">
            <a href="add_donor.php" class="action-card">
                <span class="action-icon">➕</span>
                <span>Add Donor</span>
            </a>
            <a href="add_donation.php" class="action-card">
                <span class="action-icon">💉</span>
                <span>Record Donation</span>
            </a>
            <a href="add_patient.php" class="action-card">
                <span class="action-icon">🩺</span>
                <span>Add Patient</span>
            </a>
            <a href="add_hospital.php" class="action-card">
                <span class="action-icon">🏥</span>
                <span>Add Hospital</span>
            </a>
            <a href="add_request.php" class="action-card">
                <span class="action-icon">📋</span>
                <span>New Blood Request</span>
            </a>
            <a href="view_requests.php" class="action-card">
                <span class="action-icon">✅</span>
                <span>Manage Requests</span>
            </a>
        </div>
    </div>
</div>
</body>
</html>
