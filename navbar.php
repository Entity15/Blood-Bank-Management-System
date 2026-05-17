<?php if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); } ?>
<nav class="navbar">
    <div class="navbar-brand">
        <span class="navbar-icon">🩸</span>
        <span>Blood Bank</span>
    </div>
    <div class="navbar-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="view_donors.php">Donors</a>
        <a href="view_patients.php">Patients</a>
        <a href="view_hospitals.php">Hospitals</a>
        <a href="view_stock.php">Stock</a>
        <a href="view_donations.php">Donations</a>
        <a href="view_requests.php">Requests</a>
    </div>
    <div class="navbar-user">
        <span>👤 <?php echo htmlspecialchars($_SESSION['admin']); ?></span>
        <a href="logout.php" class="btn-logout">Logout</a>
    </div>
</nav>
