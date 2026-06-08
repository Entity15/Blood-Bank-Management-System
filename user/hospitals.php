<?php
require_once 'config.php';
require_user_login();

$result = mysqli_query($conn,"
    SELECT h.*, c.Start_Date, c.End_Date
    FROM hospital h
    LEFT JOIN contract c ON h.Hospital_ID=c.Hospital_ID
    ORDER BY h.Name");
$rows=[];
while($r=mysqli_fetch_assoc($result)) $rows[]=$r;
$total=count($rows);
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Hospitals – LifeBank</title>
<link rel="stylesheet" href="user_style.css">
</head><body>
<?php include 'navbar.php'; ?>
<div class="page-hero">
    <div class="page-hero-inner">
        <h1>Partner Hospitals</h1>
        <p><?= $total ?> hospital<?= $total!=1?'s':'' ?> registered with the LifeBank network.</p>
    </div>
</div>
<div class="page-wrap">
    <div class="sec-eyebrow">Network</div>
    <h2 class="sec-title" style="margin-bottom:18px;">Hospital Directory</h2>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;margin-bottom:36px;">
    <?php foreach($rows as $h):
        $has = !empty($h['End_Date']);
        $act = $has && strtotime($h['End_Date'])>=time();
        $bc  = !$has?'no-contract':($act?'approved':'rejected');
        $bt  = !$has?'No Contract':($act?'Active Contract':'Contract Expired');
    ?>
    <div style="background:var(--white);border-radius:var(--r-lg);padding:22px;box-shadow:var(--shadow-sm);border:1px solid var(--ivory-2);border-top:3px solid <?= $act?'var(--emerald)':($has?'var(--ruby)':'var(--ivory-3)') ?>;">
        <div style="font-family:'Lora',serif;font-size:1.05rem;font-weight:700;color:var(--ink);margin-bottom:8px;">
            🏥 <?= htmlspecialchars($h['Name']) ?>
        </div>
        <div style="font-size:.83rem;color:var(--muted);display:flex;flex-direction:column;gap:5px;margin-bottom:12px;">
            <span>📍 <?= htmlspecialchars(trim(($h['Street']??'').($h['Street']?', ':'').$h['City'].', '.$h['State'])) ?></span>
            <?php if($h['Contact']): ?><span>📞 <?= htmlspecialchars($h['Contact']) ?></span><?php endif; ?>
            <?php if($has): ?><span>📅 <?= $h['Start_Date'] ?> → <?= $h['End_Date'] ?></span><?php endif; ?>
        </div>
        <span class="pill <?= $bc ?>"><?= $bt ?></span>
    </div>
    <?php endforeach; ?>
    <?php if(empty($rows)): ?><p class="empty-row" style="grid-column:1/-1;">No hospitals registered yet.</p><?php endif; ?>
    </div>

    <div class="table-wrap">
        <table class="u-table">
            <thead><tr><th>#</th><th>Hospital</th><th>Contact</th><th>City</th><th>State</th><th>Contract Status</th></tr></thead>
            <tbody>
            <?php $n=0; foreach($rows as $h): $n++;
                $has=$h['End_Date']!='';
                $act=$has&&strtotime($h['End_Date'])>=time();
                $bc=!$has?'no-contract':($act?'approved':'rejected');
                $bt=!$has?'No Contract':($act?'Active':'Expired');
            ?>
            <tr>
                <td style="color:var(--muted);font-size:.8rem;"><?= $n ?></td>
                <td style="font-weight:500;"><?= htmlspecialchars($h['Name']) ?></td>
                <td style="font-size:.84rem;"><?= htmlspecialchars($h['Contact']??'—') ?></td>
                <td><?= htmlspecialchars($h['City']??'—') ?></td>
                <td><?= htmlspecialchars($h['State']??'—') ?></td>
                <td><span class="pill <?= $bc ?>"><?= $bt ?></span></td>
            </tr>
            <?php endforeach; if($n===0): ?>
            <tr><td colspan="6" class="empty-row">No hospitals found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<footer style="background:var(--ink);color:rgba(255,255,255,.4);text-align:center;padding:22px;font-size:.78rem;margin-top:48px;">
    <strong style="color:rgba(255,255,255,.7);">LifeBank</strong> · User Portal · North East University Bangladesh, CSE Dept
</footer>
</body></html>
