<?php
include 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
$s = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where = $s ? "WHERE Name LIKE '%$s%' OR Phone LIKE '%$s%' OR Blood_Group LIKE '%$s%'" : '';
$result = mysqli_query($conn, "SELECT * FROM donor $where ORDER BY Name");
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Donors – Blood Bank</title><link rel="stylesheet" href="style.css"></head><body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-header"><h1>Donors</h1><a href="add_donor.php" class="btn btn-primary">+ Add Donor</a></div>
    <form method="GET" class="search-bar">
        <input type="text" name="search" placeholder="Search by name, phone, blood group…" value="<?php echo htmlspecialchars($s); ?>">
        <button type="submit" class="btn btn-secondary">Search</button>
        <?php if ($s): ?><a href="view_donors.php" class="btn btn-secondary">Clear</a><?php endif; ?>
    </form>
    <table><thead><tr><th>ID</th><th>Name</th><th>Age</th><th>Blood Group</th><th>Phone</th><th>City</th><th>Date Joined</th><th>Actions</th></tr></thead>
    <tbody>
    <?php $n=0; while($r=mysqli_fetch_assoc($result)): $n++; ?>
    <tr>
        <td><?php echo $r['Donor_ID']; ?></td>
        <td><?php echo htmlspecialchars($r['Name']); ?></td>
        <td><?php echo $r['Age']; ?></td>
        <td><span class="blood-badge"><?php echo $r['Blood_Group']; ?></span></td>
        <td><?php echo htmlspecialchars($r['Phone']); ?></td>
        <td><?php echo htmlspecialchars($r['City']); ?></td>
        <td><?php echo $r['Date_Joined']; ?></td>
        <td>
            <a class="abtn edit" href="edit_donor.php?id=<?php echo $r['Donor_ID']; ?>">Edit</a>
            <a class="abtn del" href="delete_donor.php?id=<?php echo $r['Donor_ID']; ?>" onclick="return confirm('Delete this donor?');">Delete</a>
        </td>
    </tr>
    <?php endwhile; if($n===0): ?><tr><td colspan="8" class="empty">No donors found.</td></tr><?php endif; ?>
    </tbody></table>
</div></body></html>
