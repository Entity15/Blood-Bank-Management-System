<?php
// Single source of truth for the database connection.
// All config.php files (admin/, user/, visitor/) and the root login.php
// include this instead of hardcoding credentials separately.
if (!isset($conn)) {
    $conn = mysqli_connect("localhost", "root", "", "blood_bank_management_system");
    if (!$conn) die("Database connection failed: " . mysqli_connect_error());
}
?>
