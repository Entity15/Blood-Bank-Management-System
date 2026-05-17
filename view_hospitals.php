<?php
include 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
$result = mysqli_query($conn, "SELECT * FROM hospital ORDER BY Name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospitals – Blood Bank</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-header">
        <h1>Hospital List</h1>
        <a href="add_hospital.php" class="btn btn-primary">+ Add Hospital</a>
    </div>
    <table>
        <thead>
            <tr><th>ID</th><th>Name</th><th>Address</th></tr>
        </thead>
        <tbody>
        <?php $count=0; while($row = mysqli_fetch_assoc($result)): $count++; ?>
        <tr>
            <td><?php echo $row['Hospital_ID']; ?></td>
            <td><?php echo htmlspecialchars($row['Name']); ?></td>
            <td><?php echo htmlspecialchars($row['Address']); ?></td>
        </tr>
        <?php endwhile; ?>
        <?php if ($count===0): ?><tr><td colspan="3" class="empty-msg">No hospitals registered.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
