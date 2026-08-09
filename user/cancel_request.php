<?php
require_once 'config.php';
require_user_login();
$u   = current_user();
$uid = $u['id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['request_id'])) {
    header("Location: my_requests.php"); exit();
}

$rid = (int)$_POST['request_id'];

// Only allow cancelling a request that belongs to this user AND is still Pending.
$check = mysqli_query($conn,
    "SELECT Status FROM request WHERE Request_ID=$rid AND User_ID=$uid");
$row = $check ? mysqli_fetch_assoc($check) : null;

if ($row && $row['Status'] === 'Pending') {
    mysqli_query($conn, "UPDATE request SET Status='Cancelled' WHERE Request_ID=$rid AND User_ID=$uid");
    $_SESSION['flash_ok'] = "Your request has been cancelled.";
} else {
    $_SESSION['flash_err'] = "That request can no longer be cancelled — it may have already been reviewed by an admin.";
}

header("Location: my_requests.php");
exit();
