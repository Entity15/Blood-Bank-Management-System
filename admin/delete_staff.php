<?php
include 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: view_staff.php"); exit(); }
csrf_check();
$id=(int)$_POST['id'];
mysqli_query($conn,"DELETE FROM staff WHERE Staff_ID=$id");
header("Location: view_staff.php"); exit();
?>
