<?php
require_once 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
$msg=$error='';

// Approve — find a matching donation, link via donation_to_request, update status
if (isset($_GET['approve'])) {
    $rid = (int)$_GET['approve'];
    $req = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM request WHERE Request_ID=$rid"));
    if ($req && $req['Status']==='Pending') {
        $bg   = mysqli_real_escape_string($conn, $req['Blood_Group']);
        $need = (int)$req['Units'];
        // Find available non-expired donations with matching blood group not yet fully used
        $avail = mysqli_query($conn,"
            SELECT d.Donation_ID, d.Units,
                   COALESCE((SELECT SUM(dtr.Units_Provided) FROM donation_to_request dtr WHERE dtr.Donation_ID=d.Donation_ID),0) AS Used
            FROM donation d
            JOIN blood b ON d.Blood_ID=b.Blood_ID
            WHERE b.Blood_Group='$bg'
              AND b.Expiry_Date >= CURDATE()
            HAVING (d.Units - Used) > 0
            ORDER BY d.Expiry_Date ASC");
        $total_avail=0;
        $rows=[];
        while($av=mysqli_fetch_assoc($avail)){ $rows[]=$av; $total_avail+=($av['Units']-$av['Used']); }
        if ($total_avail >= $need) {
            $remaining=$need;
            foreach($rows as $av){
                if($remaining<=0) break;
                $give=min($remaining, $av['Units']-$av['Used']);
                $did=(int)$av['Donation_ID'];
                $today=date('Y-m-d');
                mysqli_query($conn,"INSERT INTO donation_to_request (Donation_ID,Request_ID,Units_Provided,Date_Provided) VALUES ($did,$rid,$give,'$today') ON DUPLICATE KEY UPDATE Units_Provided=Units_Provided+$give");
                $remaining-=$give;
            }
            mysqli_query($conn,"UPDATE request SET Status='Approved' WHERE Request_ID=$rid");
            $msg="Request #$rid approved and fulfilled from available donations.";
        } else {
            $error="Not enough $bg blood available (need $need, have $total_avail). Request rejected.";
            mysqli_query($conn,"UPDATE request SET Status='Rejected' WHERE Request_ID=$rid");
        }
    }
}
if (isset($_GET['reject'])) {
    $rid=(int)$_GET['reject'];
    mysqli_query($conn,"UPDATE request SET Status='Rejected' WHERE Request_ID=$rid");
    $msg="Request #$rid rejected.";
}

$result = mysqli_query($conn,"
    SELECT r.*, p.Name AS PatientName, p.Disease_Name, h.Name AS HospitalName
    FROM request r
    JOIN patient  p ON r.Patient_ID  = p.Patient_Disease_ID
    JOIN hospital h ON r.Hospital_ID = h.Hospital_ID
    ORDER BY r.Request_ID DESC");
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Blood Requests</title><link rel="stylesheet" href="style.css"></head><body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-header"><h1>Blood Requests</h1><a href="add_request.php" class="btn btn-primary">+ New Request</a></div>
    <?php if ($msg):   ?><div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <table><thead><tr><th>ID</th><th>Patient</th><th>Disease</th><th>Hospital</th><th>Blood Group</th><th>Units</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
    <?php $n=0; while($r=mysqli_fetch_assoc($result)): $n++;
        $sc=$r['Status']==='Approved'?'s-ok':($r['Status']==='Rejected'?'s-critical':'s-warn');
    ?>
    <tr>
        <td><?php echo $r['Request_ID']; ?></td>
        <td><?php echo htmlspecialchars($r['PatientName']); ?></td>
        <td><?php echo htmlspecialchars($r['Disease_Name']); ?></td>
        <td><?php echo htmlspecialchars($r['HospitalName']); ?></td>
        <td><span class="blood-badge"><?php echo $r['Blood_Group']; ?></span></td>
        <td><?php echo $r['Units']; ?></td>
        <td><?php echo $r['Request_Date']; ?></td>
        <td><span class="stag <?php echo $sc; ?>"><?php echo $r['Status']; ?></span></td>
        <td>
        <?php if($r['Status']==='Pending'): ?>
            <a class="abtn edit" href="?approve=<?php echo $r['Request_ID']; ?>" onclick="return confirm('Approve this request?');">Approve</a>
            <a class="abtn del"  href="?reject=<?php echo $r['Request_ID']; ?>"  onclick="return confirm('Reject?');">Reject</a>
        <?php else: ?><span class="muted">—</span><?php endif; ?>
        </td>
    </tr>
    <?php endwhile; if($n===0): ?><tr><td colspan="9" class="empty">No requests found.</td></tr><?php endif; ?>
    </tbody></table>
</div></body></html>
