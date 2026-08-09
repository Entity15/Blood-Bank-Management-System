<?php
require_once 'config.php';
require_user_login();
$u   = current_user();
$uid = $u['id'];

$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$bg_filter     = isset($_GET['blood_group']) ? mysqli_real_escape_string($conn, $_GET['blood_group']) : '';

$conds = ["r.User_ID = $uid"];
if ($status_filter) $conds[] = "r.Status='$status_filter'";
if ($bg_filter)     $conds[] = "r.Blood_Group='$bg_filter'";
$where = 'WHERE ' . implode(' AND ', $conds);

$result = mysqli_query($conn, "
    SELECT r.*, p.Name AS PatientName, p.Disease_Name, h.Name AS HospName
    FROM request r
    JOIN patient  p ON r.Patient_ID  = p.Patient_Disease_ID
    JOIN hospital h ON r.Hospital_ID = h.Hospital_ID
    $where
    ORDER BY r.Request_ID DESC");

$total = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM request r $where"))[0];
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>My Requests – LifeBank</title>
<link rel="stylesheet" href="user_style.css">
</head><body>
<?php include 'navbar.php'; ?>

<div class="page-hero">
    <div class="page-hero-inner">
        <h1>My Blood Requests</h1>
        <p>Track every request you've submitted — see status, fulfilment, and history.</p>
    </div>
</div>

<div class="page-wrap">
    <?php if (!empty($_SESSION['flash_ok'])): ?>
        <div class="alert alert-ok"><?= htmlspecialchars($_SESSION['flash_ok']) ?></div>
        <?php unset($_SESSION['flash_ok']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_err'])): ?>
        <div class="alert alert-err"><?= htmlspecialchars($_SESSION['flash_err']) ?></div>
        <?php unset($_SESSION['flash_err']); ?>
    <?php endif; ?>
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
        <div>
            <div class="sec-eyebrow">History</div>
            <h2 class="sec-title" style="margin-bottom:0;"><?= $total ?> Request<?= $total!=1?'s':'' ?></h2>
        </div>
        <a href="new_request.php" class="btn btn-primary">📝 New Request</a>
    </div>

    <!-- Filters -->
    <form method="GET" class="search-row" style="margin-bottom:20px;">
        <select name="status" class="form-select" style="max-width:160px;">
            <option value="">All Statuses</option>
            <?php foreach(['Pending','Approved','Rejected','Cancelled'] as $s):
                $sel = ($status_filter===$s)?'selected':''; ?>
            <option <?= $sel ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>
        <select name="blood_group" class="form-select" style="max-width:160px;">
            <option value="">All Blood Groups</option>
            <?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $g):
                $sel = ($bg_filter===$g)?'selected':''; ?>
            <option <?= $sel ?>><?= $g ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        <?php if ($status_filter || $bg_filter): ?>
        <a href="my_requests.php" class="btn btn-ghost">Clear</a>
        <?php endif; ?>
    </form>

    <div class="table-wrap">
        <table class="u-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Patient</th>
                    <th>Disease</th>
                    <th>Hospital</th>
                    <th>Blood Group</th>
                    <th>Units</th>
                    <th>Date Requested</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th style="width:100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php $n=0; while ($r = mysqli_fetch_assoc($result)): $n++;
                $sc = strtolower($r['Status']);

                // Check if fulfilled via donation_to_request
                $fulfilled = mysqli_fetch_row(mysqli_query($conn,
                    "SELECT COALESCE(SUM(Units_Provided),0) FROM donation_to_request WHERE Request_ID={$r['Request_ID']}"))[0];
            ?>
            <tr>
                <td style="color:var(--muted);font-size:.8rem;"><?= $r['Request_ID'] ?></td>
                <td style="font-weight:500;"><?= htmlspecialchars($r['PatientName']) ?></td>
                <td style="color:var(--muted);font-size:.84rem;"><?= htmlspecialchars($r['Disease_Name']) ?></td>
                <td style="font-size:.84rem;"><?= htmlspecialchars($r['HospName']) ?></td>
                <td><span class="bb"><?= $r['Blood_Group'] ?></span></td>
                <td><?= $r['Units'] ?></td>
                <td style="color:var(--muted);font-size:.82rem;"><?= $r['Request_Date'] ?></td>
                <td><span class="pill <?= $sc ?>"><?= $r['Status'] ?></span></td>
                <td>
                    <?php if ($r['Status'] === 'Approved'): ?>
                        <span style="font-size:.82rem;color:var(--emerald);">✔ <?= $fulfilled ?> / <?= $r['Units'] ?> units fulfilled</span>
                    <?php elseif ($r['Status'] === 'Pending'): ?>
                        <div class="req-timeline">
                            <div class="req-step done"><span class="step-dot"></span>Submitted</div>
                            <div class="step-line"></div>
                            <div class="req-step active"><span class="step-dot"></span>Review</div>
                            <div class="step-line"></div>
                            <div class="req-step"><span class="step-dot"></span>Decision</div>
                        </div>
                    <?php elseif ($r['Status'] === 'Rejected'): ?>
                        <span style="font-size:.82rem;color:var(--ruby);">✗ Insufficient stock at time of review</span>
                    <?php elseif ($r['Status'] === 'Cancelled'): ?>
                        <span style="font-size:.82rem;color:var(--muted);">Cancelled by you</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($r['Status'] === 'Pending'): ?>
                        <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-start;">
                            <a href="edit_request.php?id=<?= $r['Request_ID'] ?>" class="btn btn-ghost btn-sm" style="width:100%;justify-content:center;">Edit</a>
                            <form method="POST" action="cancel_request.php" onsubmit="return confirm('Cancel this blood request? This cannot be undone.');" style="width:100%;">
                                <button type="submit" class="btn btn-danger btn-sm" style="width:100%;justify-content:center;">Cancel</button>
                                <input type="hidden" name="request_id" value="<?= $r['Request_ID'] ?>">
                            </form>
                        </div>
                    <?php else: ?>
                        <span style="color:var(--muted);font-size:.8rem;">—</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; if ($n === 0): ?>
            <tr><td colspan="10" class="empty-row">
                No requests found.
                <?php if (!$status_filter && !$bg_filter): ?>
                    <a href="new_request.php" style="color:var(--teal);font-weight:600;">Make your first request →</a>
                <?php endif; ?>
            </td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <p style="margin-top:14px;font-size:.8rem;color:var(--muted);">
        ℹ Requests are reviewed and approved by blood bank administrators. Approval depends on stock availability.
    </p>
</div>

<footer style="background:var(--ink);color:rgba(255,255,255,.4);text-align:center;padding:22px;font-size:.78rem;margin-top:48px;">
    <strong style="color:rgba(255,255,255,.7);">LifeBank</strong> · User Portal · North East University Bangladesh, CSE Dept
</footer>
</body></html>
