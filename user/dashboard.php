<?php
require_once 'config.php';
require_user_login();
$u  = current_user();
$uid = $u['id'];

// Stats for this user
$my_requests   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM request WHERE User_ID=$uid"))[0];
$my_pending    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM request WHERE User_ID=$uid AND Status='Pending'"))[0];
$my_approved   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM request WHERE User_ID=$uid AND Status='Approved'"))[0];

// Global stats (read-only view)
$total_donors  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM donor"))[0];
$total_donated = mysqli_fetch_row(mysqli_query($conn, "SELECT COALESCE(SUM(Units),0) FROM donation WHERE Expiry_Date >= CURDATE()"))[0];

// Blood stock snapshot
$stock_res = mysqli_query($conn, "
    SELECT b.Blood_Group,
           COALESCE(SUM(b.Units),0)
           - COALESCE((SELECT SUM(dtr.Units_Provided)
                        FROM donation_to_request dtr
                        JOIN donation d2 ON dtr.Donation_ID=d2.Donation_ID
                        JOIN blood b2    ON d2.Blood_ID=b2.Blood_ID
                        WHERE b2.Blood_Group=b.Blood_Group),0) AS Available
    FROM blood b WHERE b.Expiry_Date >= CURDATE()
    GROUP BY b.Blood_Group ORDER BY b.Blood_Group");
$stock = [];
while ($r = mysqli_fetch_assoc($stock_res)) $stock[$r['Blood_Group']] = max(0,(int)$r['Available']);

// User's 5 most recent requests
$recent_req = mysqli_query($conn, "
    SELECT r.*, p.Name AS PatientName, h.Name AS HospName
    FROM request r
    JOIN patient  p ON r.Patient_ID  = p.Patient_Disease_ID
    JOIN hospital h ON r.Hospital_ID = h.Hospital_ID
    WHERE r.User_ID = $uid
    ORDER BY r.Request_ID DESC LIMIT 5");

// 5 most recent donations (public info)
$recent_don = mysqli_query($conn, "
    SELECT d.Donation_Date, dn.Name AS DonorName, b.Blood_Group, d.Units
    FROM donation d
    JOIN donor dn ON d.Donor_ID = dn.Donor_ID
    JOIN blood  b  ON d.Blood_ID = b.Blood_ID
    ORDER BY d.Donation_Date DESC, d.Donation_ID DESC LIMIT 5");
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Dashboard – LifeBank</title>
<link rel="stylesheet" href="user_style.css">
</head><body>
<?php include 'navbar.php'; ?>

<div class="page-hero">
    <div class="page-hero-inner">
        <h1>Welcome back, <?= htmlspecialchars(explode(' ',$u['name'])[0]) ?>.</h1>
        <p>Here's a snapshot of your account and the blood bank today.</p>
    </div>
</div>

<div class="page-wrap">

    <!-- My Stats -->
    <div style="margin-bottom:10px;"><span class="sec-eyebrow">My Account</span></div>
    <div class="dash-grid" style="margin-bottom:32px;">
        <a href="my_requests.php" class="dash-card">
            <div class="dash-icon">📋</div>
            <div class="dash-val"><?= $my_requests ?></div>
            <div class="dash-lbl">My Requests</div>
        </a>
        <a href="my_requests.php?status=Pending" class="dash-card warn">
            <div class="dash-icon">⏳</div>
            <div class="dash-val"><?= $my_pending ?></div>
            <div class="dash-lbl">Pending</div>
        </a>
        <a href="my_requests.php?status=Approved" class="dash-card">
            <div class="dash-icon">✅</div>
            <div class="dash-val"><?= $my_approved ?></div>
            <div class="dash-lbl">Approved</div>
        </a>
        <div class="dash-card">
            <div class="dash-icon">🩸</div>
            <div class="dash-val"><?= $total_donors ?></div>
            <div class="dash-lbl">Registered Donors</div>
        </div>
        <div class="dash-card">
            <div class="dash-icon">💉</div>
            <div class="dash-val"><?= $total_donated ?></div>
            <div class="dash-lbl">Units in Stock</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div style="margin-bottom:8px;"><span class="sec-eyebrow">Quick Actions</span></div>
    <div class="action-grid" style="margin-bottom:40px;">
        <a href="new_request.php" class="action-card">
            <span class="ac-icon">📝</span>New Blood Request
        </a>
        <a href="my_requests.php" class="action-card">
            <span class="ac-icon">📋</span>My Requests
        </a>
        <a href="blood_stock.php" class="action-card">
            <span class="ac-icon">🏦</span>Check Blood Stock
        </a>
        <a href="donations.php" class="action-card">
            <span class="ac-icon">💉</span>Donation History
        </a>
        <a href="donors.php" class="action-card">
            <span class="ac-icon">👥</span>Donor Registry
        </a>
        <a href="hospitals.php" class="action-card">
            <span class="ac-icon">🏥</span>Hospitals
        </a>
        <a href="profile.php" class="action-card">
            <span class="ac-icon">👤</span>My Profile
        </a>
    </div>

    <!-- Blood Stock Mini -->
    <div style="margin-bottom:8px;"><span class="sec-eyebrow">Blood Bank</span></div>
    <h2 class="sec-title" style="margin-bottom:16px;">Current Stock Levels</h2>
    <?php
    $all_groups = ['A+','A-','B+','B-','AB+','AB-','O+','O-'];
    ?>
    <div class="blood-grid" style="margin-bottom:40px;">
        <?php foreach ($all_groups as $bg):
            $avail = $stock[$bg] ?? 0;
            $cls   = $avail === 0 ? 'critical' : ($avail < 5 ? 'low' : 'ok');
            $lbl   = $avail === 0 ? 'Critical'  : ($avail < 5 ? 'Low'  : 'Available');
        ?>
        <div class="bg-card <?= $cls ?>">
            <div class="bg-label"><?= $bg ?></div>
            <div class="bg-units"><?= $avail ?></div>
            <div class="bg-sub">units</div>
            <span class="pill <?= $cls ?>"><?= $lbl ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:28px;flex-wrap:wrap;">

        <!-- Recent Requests -->
        <div>
            <div class="sec-eyebrow">Activity</div>
            <h2 class="sec-title" style="margin-bottom:16px;">My Recent Requests</h2>
            <div class="table-wrap">
                <table class="u-table">
                    <thead><tr><th>Patient</th><th>Blood</th><th>Units</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php $n=0; while($r=mysqli_fetch_assoc($recent_req)): $n++;
                        $sc = strtolower($r['Status']);
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($r['PatientName']) ?></td>
                        <td><span class="bb"><?= $r['Blood_Group'] ?></span></td>
                        <td><?= $r['Units'] ?></td>
                        <td><span class="pill <?= $sc ?>"><?= $r['Status'] ?></span></td>
                    </tr>
                    <?php endwhile; if ($n===0): ?>
                    <tr><td colspan="4" class="empty-row">No requests yet. <a href="new_request.php" style="color:var(--teal);font-weight:600;">Make one →</a></td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($n > 0): ?><p style="margin-top:10px;font-size:.82rem;"><a href="my_requests.php" style="color:var(--teal);font-weight:600;">View all my requests →</a></p><?php endif; ?>
        </div>

        <!-- Recent Donations -->
        <div>
            <div class="sec-eyebrow">Community</div>
            <h2 class="sec-title" style="margin-bottom:16px;">Recent Donations</h2>
            <div class="table-wrap">
                <table class="u-table">
                    <thead><tr><th>Donor</th><th>Blood</th><th>Units</th><th>Date</th></tr></thead>
                    <tbody>
                    <?php $n=0; while($r=mysqli_fetch_assoc($recent_don)): $n++; ?>
                    <tr>
                        <td><?= htmlspecialchars($r['DonorName']) ?></td>
                        <td><span class="bb"><?= $r['Blood_Group'] ?></span></td>
                        <td><?= $r['Units'] ?></td>
                        <td style="color:var(--muted);font-size:.82rem;"><?= $r['Donation_Date'] ?></td>
                    </tr>
                    <?php endwhile; if ($n===0): ?>
                    <tr><td colspan="4" class="empty-row">No donations recorded yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($n > 0): ?><p style="margin-top:10px;font-size:.82rem;"><a href="donations.php" style="color:var(--teal);font-weight:600;">Full donation history →</a></p><?php endif; ?>
        </div>

    </div>
</div>

<footer style="background:var(--ink);color:rgba(255,255,255,.4);text-align:center;padding:22px;font-size:.78rem;margin-top:48px;">
    <strong style="color:rgba(255,255,255,.7);">LifeBank</strong> · User Portal · North East University Bangladesh, CSE Dept
</footer>
</body></html>
