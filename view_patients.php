<?php
include 'config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM Patient");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Patients</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>   <!-- ✅ MOVE HERE -->

<div class="container">
<h2>Patient List</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Age</th>
        <th>Blood Group</th>
        <th>Disease</th>
    </tr>

    <?php while($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo $row['Patient_ID']; ?></td>
        <td><?php echo $row['Name']; ?></td>
        <td><?php echo $row['Age']; ?></td>
        <td><?php echo $row['Blood_Group']; ?></td>
        <td><?php echo $row['Disease']; ?></td>
    </tr>
    <?php } ?>

</table>
</div>

</body>
</html>