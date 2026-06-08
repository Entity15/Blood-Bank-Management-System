<?php
require_once 'config.php';
require_user_login();

$bg_filter = isset($_GET['blood_group']) ? mysqli_real_escape_string($conn,$_GET['blood_group']) : '';
$search    = isset($_GET['search'])      ? mysqli_real_escape_string($conn,trim($_GET['search'])) : '';
$status_f  = isset($_GET['status'])      ? $_GET['status'] : '';

$conds = [];
if ($bg_filter) $conds[] = "b.Blood_Group='$bg_filter'";
if ($search)    $conds[] = "dn.Name LIKE '%$search%'";
if ($status_f === 'valid')   $conds[] = "b.Expiry_Date >= CURDATE()";
if ($status_f === 'expiring') $conds[] = "b.Expiry_Date >= CURDATE() AND b.Expiry_Date <= DATE_ADD(CURDATE(),INTERVAL 7 DAY)";
if ($status_f === 'expired') $conds[] = "b.Expiry_Date < CURDATE()";
$where = $conds ? 'WHERE '.implode(' AND ',$conds) : '';

$total  = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM donation d JOIN donor dn ON d.Donor_ID=dn.Donor_ID JOIN blood b ON d.Blood_ID=b.Blood_ID $where"))[0];
$result = mysqli_query($conn,"
    SELECT d.Donation_ID, dn.Name AS DonorName, b.Blood_Group, d.Units, d.Donation_Date, b.Expiry_Date
    FROM donation d
    JOIN donor dn ON d.Donor_ID=dn.Donor_ID
    JOIN blood  b  ON d.Blood_ID=b.Blood_ID
    $where
    ORDER BY d.Donation_Date DESC, d.Donation_ID DESC");
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Donation History – LifeBank</title>
<link rel="stylesheet" href="user_style.css">
</head><body>
<?php include 'navbar.php'; ?>
<div class="page-hero">
    <div class="page-hero-inner">
        <h1>Donation History</h1>
        <p>Every donation that has come into the blood bank — who gave, when, and how much.</p>
    </div>
</div>
<div class="page-wrap">
    <div class="sec-eyebrow">Records</div>
    <h2 class="sec-title" style="margin-bottom:16px;"><?= $total ?> Donation<?= $total!=1?'s':'' ?></h2>

    <form method="GET" class="search-row">
        <input type="text" name="search" class="form-input" placeholder="Search donor name…" value="<?= htmlspecialchars($search) ?>">
        <select name="blood_group" class="form-select">
            <option value="">All Blood Groups</option>
            <?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $g):
                $s=($bg_filter===$g)?'selected':''; ?>
            <option <?= $s ?>><?= $g ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status" class="form-select">
            <option value="">All Statuses</option>
            <?php foreach(['valid'=>'Valid','expiring'=>'Expiring Soon','expired'=>'Expired'] as $v=>$l):
                $s=($status_f===$v)?'selected':''; ?>
            <option value="<?= $v ?>" <?= $s ?>><?= $l ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        <?php if ($bg_filter||$search||$status_f): ?><a href="donations.php" class="btn btn-ghost">Clear</a><?php endif; ?>
    </form>

    <div class="table-wrap">
        <table class="u-table">
            <thead><tr><th>#</th><th>Donor</th><th>Blood Group</th><th>Units</th><th>Donation Date</th><th>Expiry Date</th><th>Status</th></tr></thead>
            <tbody>
            <?php $n=0; while($r=mysqli_fetch_assoc($result)): $n++;
                $expired = strtotime($r['Expiry_Date']) < time();
                $expiring = !$expired && strtotime($r['Expiry_Date']) <= strtotime('+7 days');
                $cls = $expired ? 'expired' : ($expiring ? 'expiring' : 'valid');
                $lbl = $expired ? 'Expired'  : ($expiring ? 'Expiring Soon' : 'Valid');
            ?>
            <tr>
                <td style="color:var(--muted);font-size:.8rem;"><?= $r['Donation_ID'] ?></td>
                <td style="font-weight:500;"><?= htmlspecialchars($r['DonorName']) ?></td>
                <td><span class="bb"><?= $r['Blood_Group'] ?></span></td>
                <td><?= $r['Units'] ?></td>
                <td><?= $r['Donation_Date'] ?></td>
                <td style="font-size:.83rem;color:var(--muted);"><?= $r['Expiry_Date'] ?></td>
                <td><span class="pill <?= $cls ?>"><?= $lbl ?></span></td>
            </tr>
            <?php endwhile; if($n===0): ?>
            <tr><td colspan="7" class="empty-row">No donations found<?= ($bg_filter||$search||$status_f)?' for that filter.':'.'; ?></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<footer style="background:var(--ink);color:rgba(255,255,255,.4);text-align:center;padding:22px;font-size:.78rem;margin-top:48px;">
    <strong style="color:rgba(255,255,255,.7);">LifeBank</strong> · User Portal · North East University Bangladesh, CSE Dept
</footer>
</body></html>
