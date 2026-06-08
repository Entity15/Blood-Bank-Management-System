<?php
include 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
$id=(int)$_GET['id'];
mysqli_query($conn,"DELETE FROM staff WHERE Staff_ID=$id");
header("Location: view_staff.php"); exit();
?>
