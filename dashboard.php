<?php
require_once 'config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<?php include 'navbar.php'; ?>

<div class="container">

<h1>Blood Bank Management System</h1>

<p>Welcome, <?php echo $_SESSION['admin']; ?>!</p>

<ul>
    <li><a href="add_donor.php">Add Donor</a></li>
    <li><a href="view_donors.php">View Donors</a></li>

    <li><a href="add_patient.php">Add Patient</a></li>
    <li><a href="view_patients.php">View Patients</a></li>

    <li><a href="add_hospital.php">Add Hospital</a></li>
    <li><a href="view_hospitals.php">View Hospitals</a></li>

    <li><a href="create_request.php">Create Request</a></li>
    <li><a href="view_requests.php">Manage Requests</a></li>

    <li><a href="logout.php">Logout</a></li>
</ul>

</div>

</body>
</html>