<?php
include 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
$result = mysqli_query($conn,"SELECT * FROM staff ORDER BY Name");
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Staff</title><link rel="stylesheet" href="style.css"></head><body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-header"><h1>Staff</h1><a href="add_staff.php" class="btn btn-primary">+ Add Staff</a></div>
    <table><thead><tr><th>ID</th><th>Name</th><th>Role</th><th>Phone</th><th>Salary (BDT)</th><th>Actions</th></tr></thead>
    <tbody>
    <?php $n=0; while($r=mysqli_fetch_assoc($result)): $n++; ?>
    <tr>
        <td><?php echo $r['Staff_ID']; ?></td>
        <td><?php echo htmlspecialchars($r['Name']); ?></td>
        <td><?php echo htmlspecialchars($r['Role']); ?></td>
        <td><?php echo htmlspecialchars($r['Phone']); ?></td>
        <td><?php echo number_format($r['Salary'],2); ?></td>
        <td><a class="abtn del" href="delete_staff.php?id=<?php echo $r['Staff_ID']; ?>" onclick="return confirm('Delete this staff member?');">Delete</a></td>
    </tr>
    <?php endwhile; if($n===0): ?><tr><td colspan="6" class="empty">No staff found.</td></tr><?php endif; ?>
    </tbody></table>
</div></body></html>
