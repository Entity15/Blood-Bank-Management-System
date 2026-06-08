<?php
require_once 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
// Compute stock: total donated minus total fulfilled via donation_to_request
$result = mysqli_query($conn,"
    SELECT b.Blood_Group,
           COALESCE(SUM(b.Units),0) AS Total_Donated,
           COALESCE((SELECT SUM(dtr.Units_Provided) FROM donation_to_request dtr JOIN donation d2 ON dtr.Donation_ID=d2.Donation_ID JOIN blood b2 ON d2.Blood_ID=b2.Blood_ID WHERE b2.Blood_Group=b.Blood_Group),0) AS Total_Fulfilled
    FROM blood b
    WHERE b.Expiry_Date >= CURDATE()
    GROUP BY b.Blood_Group
    ORDER BY b.Blood_Group");
// Also show groups with 0
$all_groups=['A+'=>['donated'=>0,'fulfilled'=>0],'A-'=>['donated'=>0,'fulfilled'=>0],'B+'=>['donated'=>0,'fulfilled'=>0],'B-'=>['donated'=>0,'fulfilled'=>0],'AB+'=>['donated'=>0,'fulfilled'=>0],'AB-'=>['donated'=>0,'fulfilled'=>0],'O+'=>['donated'=>0,'fulfilled'=>0],'O-'=>['donated'=>0,'fulfilled'=>0]];
while($r=mysqli_fetch_assoc($result)){
    if(isset($all_groups[$r['Blood_Group']])){
        $all_groups[$r['Blood_Group']]=['donated'=>$r['Total_Donated'],'fulfilled'=>$r['Total_Fulfilled']];
    }
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Blood Stock</title><link rel="stylesheet" href="style.css"></head><body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-header"><h1>Blood Stock</h1><a href="add_donation.php" class="btn btn-primary">+ Add via Donation</a></div>
    <p style="font-size:.875rem;color:var(--gray-600);margin-bottom:16px;">Stock = non-expired donations minus fulfilled units. Updated automatically when requests are approved.</p>
    <table><thead><tr><th>Blood Group</th><th>Units Donated (valid)</th><th>Units Fulfilled</th><th>Units Available</th><th>Status</th></tr></thead>
    <tbody>
    <?php foreach($all_groups as $bg=>$v):
        $avail = $v['donated'] - $v['fulfilled'];
        $sc = $avail==0?'s-critical':($avail<5?'s-warn':'s-ok');
        $sl = $avail==0?'Critical':($avail<5?'Low':'OK');
    ?>
    <tr>
        <td><span class="blood-badge"><?php echo $bg; ?></span></td>
        <td><?php echo $v['donated']; ?></td>
        <td><?php echo $v['fulfilled']; ?></td>
        <td><strong><?php echo $avail; ?></strong></td>
        <td><span class="stag <?php echo $sc; ?>"><?php echo $sl; ?></span></td>
    </tr>
    <?php endforeach; ?>
    </tbody></table>
</div></body></html>
