<?php
require_once 'config.php';

$s = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where = $s ? "WHERE h.Name LIKE '%$s%' OR h.City LIKE '%$s%'" : '';
$hospitals = mysqli_query($conn, "
    SELECT h.*,
           c.Start_Date, c.End_Date,
           CASE WHEN c.End_Date >= CURDATE() THEN 'Active' ELSE 'Inactive' END AS Contract_Status
    FROM hospital h
    LEFT JOIN contract c ON c.Hospital_ID = h.Hospital_ID
    $where
    ORDER BY h.Name
");
$total = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM hospital"))[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospitals — Blood Bank</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <div>
            <h1>Partner Hospitals</h1>
            <p><?php echo $total; ?> hospitals we work with to deliver blood to patients in need.</p>
        </div>
    </div>

    <form method="GET" class="search-bar">
        <input type="text" name="search" placeholder="Search by name or city…" value="<?php echo htmlspecialchars($s); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if ($s): ?><a href="hospitals.php" class="btn btn-secondary">Clear</a><?php endif; ?>
    </form>

    <div class="cards-grid">
    <?php $n = 0; while ($h = mysqli_fetch_assoc($hospitals)): $n++;
        $is_active = $h['Contract_Status'] === 'Active';
    ?>
        <div class="info-card">
            <h3>🏥 <?php echo htmlspecialchars($h['Name']); ?></h3>
            <div class="meta">
                <?php if ($h['Street']): ?>
                <span>📍 <?php echo htmlspecialchars($h['Street'] . ', ' . $h['City'] . ', ' . $h['State']); ?></span>
                <?php else: ?>
                <span>📍 <?php echo htmlspecialchars($h['City'] . ', ' . $h['State']); ?></span>
                <?php endif; ?>
                <span>📞 <?php echo htmlspecialchars($h['Contact']); ?></span>
                <?php if ($h['Start_Date']): ?>
                <span>📋 Contract: <?php echo $h['Start_Date']; ?> → <?php echo $h['End_Date']; ?></span>
                <?php endif; ?>
            </div>
            <?php if ($h['Start_Date']): ?>
            <div style="margin-top:12px;">
                <span class="stag <?php echo $is_active ? 's-ok' : 's-critical'; ?>">
                    <?php echo $is_active ? 'Active Contract' : 'Contract Expired'; ?>
                </span>
            </div>
            <?php endif; ?>
        </div>
    <?php endwhile; if ($n === 0): ?>
        <p style="color:var(--gray-400);">No hospitals found.</p>
    <?php endif; ?>
    </div>
</div>

<footer>
    <p>🩸 Blood Bank Management System &nbsp;|&nbsp; <a href="contact.php">Contact Us</a> &nbsp;|&nbsp; <a href="../login.php">Staff Login</a></p>
</footer>
</body>
</html>
