<?php
include 'config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT Donation.Donation_ID, Donor.Name, Blood.Blood_Group, Blood.Units, Donation.Date
        FROM Donation
        JOIN Donor ON Donation.Donor_ID = Donor.Donor_ID
        JOIN Blood ON Donation.Blood_ID = Blood.Blood_ID";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Donations</title>
</head>
<body>

<h2>Donation Records</h2>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Donor</th>
        <th>Blood Group</th>
        <th>Units</th>
        <th>Date</th>
    </tr>

    <?php while($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo $row['Donation_ID']; ?></td>
        <td><?php echo $row['Name']; ?></td>
        <td><?php echo $row['Blood_Group']; ?></td>
        <td><?php echo $row['Units']; ?></td>
        <td><?php echo $row['Date']; ?></td>
    </tr>
    <?php } ?>

</table>

</body>
</html>