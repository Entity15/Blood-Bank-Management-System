<?php
require_once 'config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM Donor";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Donors</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<?php include 'navbar.php'; ?>

<div class="container">

<h2>Donor List</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Age</th>
        <th>Phone</th>
        <th>Address</th>
        <th>Units</th>
        <th>Actions</th>
    </tr>

    <?php while($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo $row['Donor_ID']; ?></td>
        <td><?php echo $row['Name']; ?></td>
        <td><?php echo $row['Age']; ?></td>
        <td><?php echo $row['Phone']; ?></td>
        <td><?php echo $row['Address']; ?></td>
        <td><?php echo $row['Units_Donated']; ?></td>
        <td>
            <a class="action-btn" href="edit_donor.php?id=<?php echo $row['Donor_ID']; ?>">Edit</a>
            <a class="action-btn" href="delete_donor.php?id=<?php echo $row['Donor_ID']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
        </td>
    </tr>
    <?php } ?>

</table>

</div>

</body>
</html>