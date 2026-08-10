<?php
include 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: view_donors.php"); exit(); }
csrf_check();
$id=(int)$_POST['id'];
mysqli_query($conn,"DELETE FROM donor WHERE Donor_ID=$id");
header("Location: view_donors.php"); exit();
?>
