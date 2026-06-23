<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$conn = mysqli_connect("localhost","root","","blood_bank_management_system");
if (!$conn) die("Database connection failed: " . mysqli_connect_error());

function require_user_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php"); exit();
    }
}

function current_user() {
    return [
        'id'          => $_SESSION['user_id']      ?? null,
        'name'        => $_SESSION['user_name']    ?? '',
        'email'       => $_SESSION['user_email']   ?? '',
        'blood_group' => $_SESSION['user_bg']      ?? '',
        'hospital_id' => $_SESSION['user_hosp_id'] ?? null,
        'hospital'    => $_SESSION['user_hosp']    ?? '',
    ];
}
?>
