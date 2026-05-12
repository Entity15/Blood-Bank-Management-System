<div class="navbar">
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="view_donors.php">Donors</a>
        <a href="view_patients.php">Patients</a>
        <a href="view_hospitals.php">Hospitals</a>
        <a href="view_requests.php">Requests</a>
    </div>

    <div>
        <span>Welcome, <?php echo $_SESSION['admin']; ?></span>
        <a href="logout.php">Logout</a>
    </div>
</div>