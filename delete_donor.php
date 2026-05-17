<?php
include 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }

$id = (int)$_GET['id'];

if (mysqli_query($conn, "DELETE FROM donor WHERE Donor_ID=$id")) {
    header("Location: view_donors.php?msg=Donor+deleted");
} else {
    header("Location: view_donors.php?err=" . urlencode(mysqli_error($conn)));
}
exit();
?>
