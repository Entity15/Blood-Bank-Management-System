<?php
include 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
$result = mysqli_query($conn,"SELECT h.*, c.Start_Date, c.End_Date FROM hospital h LEFT JOIN contract c ON h.Hospital_ID=c.Hospital_ID ORDER BY h.Name");
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Hospitals</title><link rel="stylesheet" href="style.css"></head><body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-header"><h1>Hospitals</h1><a href="add_hospital.php" class="btn btn-primary">+ Add Hospital</a></div>
    <table><thead><tr><th>ID</th><th>Name</th><th>Contact</th><th>City</th><th>State</th><th>PIN</th><th>Contract Start</th><th>Contract End</th></tr></thead>
    <tbody>
    <?php $n=0; while($r=mysqli_fetch_assoc($result)): $n++;
        $active = $r['End_Date'] && strtotime($r['End_Date']) >= time();
    ?>
    <tr>
        <td><?php echo $r['Hospital_ID']; ?></td>
        <td><?php echo htmlspecialchars($r['Name']); ?></td>
        <td><?php echo htmlspecialchars($r['Contact']); ?></td>
        <td><?php echo htmlspecialchars($r['City']); ?></td>
        <td><?php echo htmlspecialchars($r['State']); ?></td>
        <td><?php echo htmlspecialchars($r['PIN_Code']); ?></td>
        <td><?php echo $r['Start_Date'] ?? '—'; ?></td>
        <td><?php echo $r['End_Date'] ? $r['End_Date'].' '.($active?'<span class="stag s-ok">Active</span>':'<span class="stag s-critical">Expired</span>') : '—'; ?></td>
    </tr>
    <?php endwhile; if($n===0): ?><tr><td colspan="8" class="empty">No hospitals registered.</td></tr><?php endif; ?>
    </tbody></table>
</div></body></html>
