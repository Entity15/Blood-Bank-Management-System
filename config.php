<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$conn = mysqli_connect("localhost", "root", "", "blood_bank_management_system");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
