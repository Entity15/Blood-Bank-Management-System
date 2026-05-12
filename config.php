<?php
$conn = mysqli_connect("localhost", "root", "", "blood_bank_management_system");

// Start session ONLY if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>