<?php
include 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
$result = mysqli_query($conn,"SELECT d.Donation_ID, dn.Name AS DonorName, dn.Blood_Group AS DonorBG, b.Blood_Group, d.Units, d.Donation_Date, d.Expiry_Date FROM donation d JOIN donor dn ON d.Donor_ID=dn.Donor_ID JOIN blood b ON d.Blood_ID=b.Blood_ID ORDER BY d.Donation_Date DESC");
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Donations</title><link rel="stylesheet" href="style.css"></head><body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-header"><h1>Donation Records</h1><a href="add_donation.php" class="btn btn-primary">+ Record Donation</a></div>
    <table><thead><tr><th>ID</th><th>Donor</th><th>Blood Group</th><th>Units</th><th>Donation Date</th><th>Expiry Date</th><th>Status</th></tr></thead>
    <tbody>
    <?php $n=0; while($r=mysqli_fetch_assoc($result)): $n++;
        $expired = strtotime($r['Expiry_Date']) < time();
        $soon    = !$expired && strtotime($r['Expiry_Date']) <= strtotime('+7 days');
    ?>
    <tr class="<?php echo $expired?'row-expired':($soon?'row-expiring':''); ?>">
        <td><?php echo $r['Donation_ID']; ?></td>
        <td><?php echo htmlspecialchars($r['DonorName']); ?></td>
        <td><span class="blood-badge"><?php echo $r['Blood_Group']; ?></span></td>
        <td><?php echo $r['Units']; ?></td>
        <td><?php echo $r['Donation_Date']; ?></td>
        <td><?php echo $r['Expiry_Date']; ?></td>
        <td><?php if($expired): ?><span class="stag s-critical">Expired</span><?php elseif($soon): ?><span class="stag s-warn">Expiring Soon</span><?php else: ?><span class="stag s-ok">Valid</span><?php endif; ?></td>
    </tr>
    <?php endwhile; if($n===0): ?><tr><td colspan="7" class="empty">No donations yet.</td></tr><?php endif; ?>
    </tbody></table>
</div></body></html>
