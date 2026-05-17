<?php
require_once 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
$result = mysqli_query($conn, "SELECT * FROM blood_stock ORDER BY Blood_Group");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Stock – Blood Bank</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-header">
        <h1>Blood Stock</h1>
        <a href="add_donation.php" class="btn btn-primary">+ Add Stock via Donation</a>
    </div>
    <table>
        <thead>
            <tr><th>Blood Group</th><th>Units Available</th><th>Status</th></tr>
        </thead>
        <tbody>
        <?php while($row = mysqli_fetch_assoc($result)):
            $units = $row['Units_Available'];
            $status = $units == 0 ? 'Critical' : ($units < 5 ? 'Low' : 'OK');
            $statusClass = $units == 0 ? 'status-critical' : ($units < 5 ? 'status-low' : 'status-ok');
        ?>
        <tr>
            <td><span class="blood-badge"><?php echo $row['Blood_Group']; ?></span></td>
            <td><?php echo $units; ?> units</td>
            <td><span class="status-tag <?php echo $statusClass; ?>"><?php echo $status; ?></span></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
