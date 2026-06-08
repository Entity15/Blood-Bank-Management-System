<?php
require_once 'config.php';

$filter = isset($_GET['group']) ? mysqli_real_escape_string($conn, $_GET['group']) : '';

// Compute stock per group
$all_groups = ['A+'=>['donated'=>0,'fulfilled'=>0],'A-'=>['donated'=>0,'fulfilled'=>0],'B+'=>['donated'=>0,'fulfilled'=>0],'B-'=>['donated'=>0,'fulfilled'=>0],'AB+'=>['donated'=>0,'fulfilled'=>0],'AB-'=>['donated'=>0,'fulfilled'=>0],'O+'=>['donated'=>0,'fulfilled'=>0],'O-'=>['donated'=>0,'fulfilled'=>0]];
$result = mysqli_query($conn, "
    SELECT b.Blood_Group,
           COALESCE(SUM(b.Units), 0) AS Total_Donated,
           COALESCE((
               SELECT SUM(dtr.Units_Provided)
               FROM donation_to_request dtr
               JOIN donation d2 ON dtr.Donation_ID = d2.Donation_ID
               JOIN blood    b2 ON d2.Blood_ID     = b2.Blood_ID
               WHERE b2.Blood_Group = b.Blood_Group
           ), 0) AS Total_Fulfilled
    FROM blood b
    WHERE b.Expiry_Date >= CURDATE()
    GROUP BY b.Blood_Group
    ORDER BY b.Blood_Group
");
while ($r = mysqli_fetch_assoc($result)) {
    if (isset($all_groups[$r['Blood_Group']])) {
        $all_groups[$r['Blood_Group']] = ['donated' => $r['Total_Donated'], 'fulfilled' => $r['Total_Fulfilled']];
    }
}

// Recent donations (visible metadata — no personal donor details)
$where = $filter ? "AND b.Blood_Group = '$filter'" : '';
$donations = mysqli_query($conn, "
    SELECT b.Blood_Group, b.Units, b.Collection_Date, b.Expiry_Date
    FROM blood b
    WHERE b.Expiry_Date >= CURDATE() $where
    ORDER BY b.Collection_Date DESC
    LIMIT 20
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Availability — Blood Bank</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <div>
            <h1>Blood Availability</h1>
            <p>Live stock levels for all blood groups. Updated as donations arrive and requests are fulfilled.</p>
        </div>
    </div>

    <!-- Stock cards -->
    <div class="stock-grid" style="margin-bottom:36px;">
        <?php foreach ($all_groups as $bg => $v):
            $avail = $v['donated'] - $v['fulfilled'];
            $cls   = $avail === 0 ? 'critical' : ($avail < 5 ? 'warn' : 'ok');
            $lbl   = $avail === 0 ? 'Critical'  : ($avail < 5 ? 'Low'      : 'Available');
            $active = ($filter === $bg) ? 'style="outline:3px solid var(--red-dark);"' : '';
        ?>
        <a href="blood_availability.php?group=<?php echo urlencode($bg); ?>" style="text-decoration:none;">
            <div class="stock-card <?php echo $cls; ?>" <?php echo $active; ?>>
                <div class="stock-group"><?php echo $bg; ?></div>
                <div class="stock-units"><?php echo $avail; ?></div>
                <div class="stock-label">Units Available</div>
                <span class="stag s-<?php echo $cls; ?>"><?php echo $lbl; ?></span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>

    <?php if ($filter): ?>
    <p style="margin-bottom:16px;"><a href="blood_availability.php" class="btn btn-secondary">← Show All Groups</a></p>
    <?php endif; ?>

    <!-- Recent donations table (public-safe: no donor name/phone) -->
    <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:14px;">
        Recent Blood Batches <?php echo $filter ? "— <span class='blood-badge'>$filter</span>" : ''; ?>
    </h2>
    <table>
        <thead>
            <tr>
                <th>Blood Group</th>
                <th>Units</th>
                <th>Collection Date</th>
                <th>Expires On</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php $n = 0; while ($d = mysqli_fetch_assoc($donations)): $n++;
            $days_left = (strtotime($d['Expiry_Date']) - time()) / 86400;
            $exp_cls   = $days_left < 7 ? 's-warn' : 's-ok';
            $exp_lbl   = $days_left < 7 ? 'Expiring Soon' : 'Valid';
        ?>
        <tr>
            <td><span class="blood-badge"><?php echo $d['Blood_Group']; ?></span></td>
            <td><?php echo $d['Units']; ?></td>
            <td><?php echo $d['Collection_Date']; ?></td>
            <td><?php echo $d['Expiry_Date']; ?></td>
            <td><span class="stag <?php echo $exp_cls; ?>"><?php echo $exp_lbl; ?></span></td>
        </tr>
        <?php endwhile; if ($n === 0): ?>
        <tr><td colspan="5" class="empty">No blood batches on record for this group.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <p style="margin-top:16px;font-size:.85rem;color:var(--gray-600);">
        ℹ️ Stock = non-expired donations minus fulfilled requests. Contact your hospital to place a formal request.
    </p>
</div>

<footer>
    <p>🩸 Blood Bank Management System &nbsp;|&nbsp; <a href="contact.php">Contact Us</a> &nbsp;|&nbsp; <a href="../login.php">Staff Login</a></p>
</footer>
</body>
</html>
