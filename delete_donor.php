<?php
include 'config.php';

$id = $_GET['id'];

$sql = "DELETE FROM Donor WHERE Donor_ID=$id";

if (mysqli_query($conn, $sql)) {
    header("Location: view_donors.php");
} else {
    echo "Error: " . mysqli_error($conn);
}
?>