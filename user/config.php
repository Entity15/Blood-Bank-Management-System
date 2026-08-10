<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/db.php';

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

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
function csrf_token() { return $_SESSION['csrf_token']; }
function csrf_check() {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        http_response_code(403);
        die('Invalid or expired form submission. Please go back and try again.');
    }
}
?>
