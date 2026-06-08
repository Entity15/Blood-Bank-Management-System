<?php
require_once 'config.php';
require_user_login();

$s  = isset($_GET['search'])      ? mysqli_real_escape_string($conn,trim($_GET['search'])) : '';
$bg = isset($_GET['blood_group']) ? mysqli_real_escape_string($conn,$_GET['blood_group'])  : '';

$conds = [];
if ($s)  $conds[] = "(Name LIKE '%$s%' OR City LIKE '%$s%')";
if ($bg) $conds[] = "Blood_Group='$bg'";
$where = $conds ? 'WHERE '.implode(' AND ',$conds) : '';

$total  = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM donor $where"))[0];
$result = mysqli_query($conn,"SELECT * FROM donor $where ORDER BY Name");

// Donation counts per donor
$dcounts=[];
$dc=mysqli_query($conn,"SELECT Donor_ID, COUNT(*) AS c, SUM(Units) AS u FROM donation GROUP BY Donor_ID");
while($d=mysqli_fetch_assoc($dc)) $dcounts[$d['Donor_ID']]=['count'=>$d['c'],'units'=>$d['u']];
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Donors – LifeBank</title>
<link rel="stylesheet" href="user_style.css">
</head><body>
<?php include 'navbar.php'; ?>
<div class="page-hero">
    <div class="page-hero-inner">
        <h1>Donor Registry</h1>
        <p><?= $total ?> registered donor<?= $total!=1?'s':'' ?> — the heroes behind every unit of blood.</p>
    </div>
</div>
<div class="page-wrap">
    <div class="sec-eyebrow">Registry</div>
    <h2 class="sec-title" style="margin-bottom:16px;">All Donors</h2>

    <form method="GET" class="search-row">
        <input type="text" name="search" class="form-input" placeholder="Search by name or city…" value="<?= htmlspecialchars($s) ?>">
        <select name="blood_group" class="form-select">
            <option value="">All Blood Groups</option>
            <?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $g):
                $sel=($bg===$g)?'selected':''; ?>
            <option <?= $sel ?>><?= $g ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if($s||$bg): ?><a href="donors.php" class="btn btn-ghost">Clear</a><?php endif; ?>
    </form>

    <div class="table-wrap">
        <table class="u-table">
            <thead><tr><th>#</th><th>Name</th><th>Age</th><th>Blood Group</th><th>City</th><th>Donations Made</th><th>Total Units Given</th><th>Joined</th></tr></thead>
            <tbody>
            <?php $n=0; while($r=mysqli_fetch_assoc($result)): $n++;
                $dc   = $dcounts[$r['Donor_ID']] ?? ['count'=>0,'units'=>0];
            ?>
            <tr>
                <td style="color:var(--muted);font-size:.8rem;"><?= $n ?></td>
                <td style="font-weight:500;"><?= htmlspecialchars($r['Name']) ?></td>
                <td><?= $r['Age']??'—' ?></td>
                <td><span class="bb"><?= htmlspecialchars($r['Blood_Group']) ?></span></td>
                <td><?= htmlspecialchars($r['City']??'—') ?></td>
                <td><?= $dc['count'] ?></td>
                <td><?= $dc['units'] ?? 0 ?></td>
                <td style="color:var(--muted);font-size:.82rem;"><?= $r['Date_Joined']??'—' ?></td>
            </tr>
            <?php endwhile; if($n===0): ?>
            <tr><td colspan="8" class="empty-row">No donors found<?= ($s||$bg)?' for that search.':'.'; ?></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <p style="margin-top:14px;font-size:.8rem;color:var(--muted);">Contact details are not shown publicly.</p>
</div>
<footer style="background:var(--ink);color:rgba(255,255,255,.4);text-align:center;padding:22px;font-size:.78rem;margin-top:48px;">
    <strong style="color:rgba(255,255,255,.7);">LifeBank</strong> · User Portal · North East University Bangladesh, CSE Dept
</footer>
</body></html>
