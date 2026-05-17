<?php
include 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
$result = mysqli_query($conn, "SELECT * FROM patient ORDER BY Name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patients – Blood Bank</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-header">
        <h1>Patient List</h1>
        <a href="add_patient.php" class="btn btn-primary">+ Add Patient</a>
    </div>
    <table>
        <thead>
            <tr><th>ID</th><th>Name</th><th>Age</th><th>Blood Group</th><th>Disease</th></tr>
        </thead>
        <tbody>
        <?php $count=0; while($row = mysqli_fetch_assoc($result)): $count++; ?>
        <tr>
            <td><?php echo $row['Patient_ID']; ?></td>
            <td><?php echo htmlspecialchars($row['Name']); ?></td>
            <td><?php echo $row['Age']; ?></td>
            <td><span class="blood-badge"><?php echo htmlspecialchars($row['Blood_Group']); ?></span></td>
            <td><?php echo htmlspecialchars($row['Disease']); ?></td>
        </tr>
        <?php endwhile; ?>
        <?php if ($count===0): ?><tr><td colspan="5" class="empty-msg">No patients found.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
