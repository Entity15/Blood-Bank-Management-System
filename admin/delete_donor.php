<?php
include 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
$id=(int)$_GET['id'];
mysqli_query($conn,"DELETE FROM donor WHERE Donor_ID=$id");
header("Location: view_donors.php"); exit();
?>
