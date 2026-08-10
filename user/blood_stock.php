<?php
require_once 'config.php';
require_user_login();

$all_groups = ['A+','A-','B+','B-','AB+','AB-','O+','O-'];
$stock = [];
foreach ($all_groups as $g) $stock[$g] = ['donated'=>0,'fulfilled'=>0];

$res = mysqli_query($conn, "
    SELECT b.Blood_Group,
           COALESCE(SUM(b.Units),0) AS Donated,
           COALESCE((SELECT SUM(dtr.Units_Provided)
                     FROM donation_to_request dtr
                     JOIN donation d2 ON dtr.Donation_ID=d2.Donation_ID
                     JOIN blood b2    ON d2.Blood_ID=b2.Blood_ID
                     WHERE b2.Blood_Group=b.Blood_Group AND b2.Expiry_Date >= CURDATE()),0) AS Fulfilled
    FROM blood b WHERE b.Expiry_Date >= CURDATE()
    GROUP BY b.Blood_Group");
while ($r = mysqli_fetch_assoc($res)) {
    if (isset($stock[$r['Blood_Group']])) {
        $stock[$r['Blood_Group']] = ['donated'=>(int)$r['Donated'],'fulfilled'=>(int)$r['Fulfilled']];
    }
}

$exp_res = mysqli_query($conn,"SELECT Blood_Group, SUM(Units) AS u FROM blood WHERE Expiry_Date>=CURDATE() AND Expiry_Date<=DATE_ADD(CURDATE(),INTERVAL 7 DAY) GROUP BY Blood_Group");
$expiring=[];
while ($e=mysqli_fetch_assoc($exp_res)) $expiring[$e['Blood_Group']]=$e['u'];
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Blood Stock – LifeBank</title>
<link rel="stylesheet" href="user_style.css">
</head><body>
<?php include 'navbar.php'; ?>
<div class="page-hero">
    <div class="page-hero-inner">
        <h1>Blood Stock</h1>
        <p>Live inventory — non-expired donations minus fulfilled requests.</p>
    </div>
</div>
<div class="page-wrap">
    <div class="sec-eyebrow">Overview</div>
    <h2 class="sec-title" style="margin-bottom:18px;">All Blood Groups</h2>
    <div class="blood-grid" style="margin-bottom:40px;">
        <?php foreach ($all_groups as $bg):
            $avail = max(0,$stock[$bg]['donated']-$stock[$bg]['fulfilled']);
            $cls   = $avail===0?'critical':($avail<5?'low':'ok');
            $lbl   = $avail===0?'Critical':($avail<5?'Low':'Available');
        ?>
        <div class="bg-card <?= $cls ?>">
            <div class="bg-label"><?= $bg ?></div>
            <div class="bg-units"><?= $avail ?></div>
            <div class="bg-sub">units available</div>
            <span class="pill <?= $cls ?>"><?= $lbl ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <h3 style="font-family:'Lora',serif;font-size:1.2rem;margin-bottom:16px;color:var(--ink);">Detailed Breakdown</h3>
    <div class="table-wrap">
        <table class="u-table">
            <thead><tr><th>Blood Group</th><th>Units Donated (valid)</th><th>Units Fulfilled</th><th>Available</th><th>Expiring in 7 days</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($all_groups as $bg):
                $avail = max(0,$stock[$bg]['donated']-$stock[$bg]['fulfilled']);
                $exp   = $expiring[$bg]??0;
                $cls   = $avail===0?'critical':($avail<5?'low':'ok');
                $lbl   = $avail===0?'Critical':($avail<5?'Low':'Available');
            ?>
            <tr>
                <td><span class="bb"><?= $bg ?></span></td>
                <td><?= $stock[$bg]['donated'] ?></td>
                <td><?= $stock[$bg]['fulfilled'] ?></td>
                <td><strong><?= $avail ?></strong></td>
                <td><?php if($exp>0): ?><span class="pill low">⚠ <?= $exp ?> unit<?= $exp>1?'s':'' ?></span><?php else: ?><span style="color:var(--muted)">—</span><?php endif; ?></td>
                <td><span class="pill <?= $cls ?>"><?= $lbl ?></span></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p style="margin-top:14px;font-size:.8rem;color:var(--muted);">ℹ Only non-expired stock is counted. Need blood? <a href="new_request.php" style="color:var(--teal);font-weight:600;">Submit a request →</a></p>
</div>
<footer style="background:var(--ink);color:rgba(255,255,255,.4);text-align:center;padding:22px;font-size:.78rem;margin-top:48px;">
    <strong style="color:rgba(255,255,255,.7);">LifeBank</strong> · User Portal · North East University Bangladesh, CSE Dept
</footer>
</body></html>
