<?php
include 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }

$sql = "SELECT d.Donation_ID, dn.Name AS DonorName, b.Blood_Group, b.Units, b.Expiry_Date, d.Date
        FROM donation d
        JOIN donor dn ON d.Donor_ID = dn.Donor_ID
        JOIN blood  b  ON d.Blood_ID  = b.Blood_ID
        ORDER BY d.Date DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donations – Blood Bank</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-header">
        <h1>Donation Records</h1>
        <a href="add_donation.php" class="btn btn-primary">+ Record Donation</a>
    </div>
    <table>
        <thead>
            <tr><th>ID</th><th>Donor</th><th>Blood Group</th><th>Units</th><th>Donation Date</th><th>Expiry Date</th></tr>
        </thead>
        <tbody>
        <?php $count=0; while($row = mysqli_fetch_assoc($result)): $count++;
            $expired = strtotime($row['Expiry_Date']) < time();
        ?>
        <tr class="<?php echo $expired ? 'row-expired' : ''; ?>">
            <td><?php echo $row['Donation_ID']; ?></td>
            <td><?php echo htmlspecialchars($row['DonorName']); ?></td>
            <td><span class="blood-badge"><?php echo htmlspecialchars($row['Blood_Group']); ?></span></td>
            <td><?php echo $row['Units']; ?></td>
            <td><?php echo $row['Date']; ?></td>
            <td><?php echo $row['Expiry_Date']; ?><?php if($expired) echo ' <span class="tag-expired">Expired</span>'; ?></td>
        </tr>
        <?php endwhile; ?>
        <?php if ($count===0): ?><tr><td colspan="6" class="empty-msg">No donations recorded yet.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
