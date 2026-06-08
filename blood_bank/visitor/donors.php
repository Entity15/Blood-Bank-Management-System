<?php
require_once 'config.php';

$s = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where = $s ? "WHERE Name LIKE '%$s%' OR Blood_Group LIKE '%$s%' OR City LIKE '%$s%'" : '';
$result = mysqli_query($conn, "SELECT Donor_ID, Name, Age, Blood_Group, City, State, Date_Joined FROM donor $where ORDER BY Name");
$total  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM donor"))[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donors — Blood Bank</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <div>
            <h1>Our Donors</h1>
            <p><?php echo $total; ?> registered donors — the heroes behind every saved life.</p>
        </div>
    </div>

    <form method="GET" class="search-bar">
        <input type="text" name="search" placeholder="Search by name, blood group, or city…" value="<?php echo htmlspecialchars($s); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if ($s): ?><a href="donors.php" class="btn btn-secondary">Clear</a><?php endif; ?>
    </form>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Age</th>
                <th>Blood Group</th>
                <th>City</th>
                <th>State</th>
                <th>Donor Since</th>
            </tr>
        </thead>
        <tbody>
        <?php $n = 0; while ($r = mysqli_fetch_assoc($result)): $n++; ?>
        <tr>
            <td style="color:var(--gray-400);"><?php echo $n; ?></td>
            <td><strong><?php echo htmlspecialchars($r['Name']); ?></strong></td>
            <td><?php echo $r['Age']; ?></td>
            <td><span class="blood-badge"><?php echo $r['Blood_Group']; ?></span></td>
            <td><?php echo htmlspecialchars($r['City']); ?></td>
            <td><?php echo htmlspecialchars($r['State']); ?></td>
            <td><?php echo $r['Date_Joined']; ?></td>
        </tr>
        <?php endwhile; if ($n === 0): ?>
        <tr><td colspan="7" class="empty">No donors found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <p style="margin-top:16px;font-size:.85rem;color:var(--gray-600);">
        ℹ️ Phone numbers and addresses are kept private. To reach a donor, contact our office.
    </p>
</div>

<footer>
    <p>🩸 Blood Bank Management System &nbsp;|&nbsp; <a href="contact.php">Contact Us</a> &nbsp;|&nbsp; <a href="../login.php">Staff Login</a></p>
</footer>
</body>
</html>
