<?php
include 'config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM Hospital");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Hospitals</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>   <!-- ✅ MOVE HERE -->

<div class="container">
<h2>Hospital List</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Address</th>
    </tr>

    <?php while($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo $row['Hospital_ID']; ?></td>
        <td><?php echo $row['Name']; ?></td>
        <td><?php echo $row['Address']; ?></td>
    </tr>
    <?php } ?>

</table>
</div>

</body>
</html>