<?php
include 'config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
?>
<?php
include 'config.php';

$sql = "SELECT * FROM Donor";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Donors</title>
    <th>Actions</th>
</head>
<body>

<h2>Donor List</h2>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Age</th>
        <th>Phone</th>
        <th>Address</th>
        <th>Units Donated</th>
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
        <a href="edit_donor.php?id=<?php echo $row['Donor_ID']; ?>">Edit</a> |
        <a href="delete_donor.php?id=<?php echo $row['Donor_ID']; ?>" 
           onclick="return confirm('Are you sure?');">Delete</a>
    </td>
    </tr>
    <?php } ?>

</table>

</body>
</html>