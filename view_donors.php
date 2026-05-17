<?php
require_once 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }

$search = '';
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $result = mysqli_query($conn, "SELECT * FROM donor WHERE Name LIKE '%$search%' OR Phone LIKE '%$search%' ORDER BY Name");
} else {
    $result = mysqli_query($conn, "SELECT * FROM donor ORDER BY Name");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donors – Blood Bank</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-header">
        <h1>Donor List</h1>
        <a href="add_donor.php" class="btn btn-primary">+ Add Donor</a>
    </div>

    <form method="GET" class="search-form">
        <input type="text" name="search" placeholder="Search by name or phone…" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-secondary">Search</button>
        <?php if ($search): ?><a href="view_donors.php" class="btn btn-secondary">Clear</a><?php endif; ?>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th><th>Name</th><th>Age</th><th>Phone</th><th>Address</th><th>Units Donated</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $count = 0;
        while($row = mysqli_fetch_assoc($result)):
            $count++;
        ?>
        <tr>
            <td><?php echo $row['Donor_ID']; ?></td>
            <td><?php echo htmlspecialchars($row['Name']); ?></td>
            <td><?php echo $row['Age']; ?></td>
            <td><?php echo htmlspecialchars($row['Phone']); ?></td>
            <td><?php echo htmlspecialchars($row['Address']); ?></td>
            <td><span class="badge"><?php echo $row['Units_Donated']; ?> units</span></td>
            <td>
                <a class="action-btn edit" href="edit_donor.php?id=<?php echo $row['Donor_ID']; ?>">Edit</a>
                <a class="action-btn delete" href="delete_donor.php?id=<?php echo $row['Donor_ID']; ?>"
                   onclick="return confirm('Delete this donor?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
        <?php if ($count === 0): ?>
        <tr><td colspan="7" class="empty-msg">No donors found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
