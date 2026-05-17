<?php
require_once 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }

$msg = $error = '';

// Approve
if (isset($_GET['approve'])) {
    $id  = (int)$_GET['approve'];
    $req = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT * FROM blood_request WHERE Request_ID=$id"));

    if ($req && $req['Status'] === 'Pending') {
        $blood  = $req['Blood_Group'];
        $units  = $req['Units_Required'];

        $stock  = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT * FROM blood_stock WHERE Blood_Group='$blood'"));

        if ($stock && $stock['Units_Available'] >= $units) {
            mysqli_query($conn,
                "UPDATE blood_stock SET Units_Available = Units_Available - $units
                 WHERE Blood_Group='$blood'");
            mysqli_query($conn,
                "UPDATE blood_request SET Status='Approved' WHERE Request_ID=$id");
            $msg = "Request #$id approved. Stock updated.";
        } else {
            $error = "Not enough $blood units in stock to approve request #$id.";
            mysqli_query($conn,
                "UPDATE blood_request SET Status='Rejected' WHERE Request_ID=$id");
        }
    }
}

// Reject
if (isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    mysqli_query($conn,
        "UPDATE blood_request SET Status='Rejected' WHERE Request_ID=$id");
    $msg = "Request #$id rejected.";
}

$result = mysqli_query($conn, "
    SELECT br.*, p.Name AS PatientName, h.Name AS HospitalName
    FROM blood_request br
    JOIN patient  p ON br.Patient_ID  = p.Patient_ID
    JOIN hospital h ON br.Hospital_ID = h.Hospital_ID
    ORDER BY br.Request_ID DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Requests – Blood Bank</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-header">
        <h1>Blood Requests</h1>
        <a href="add_request.php" class="btn btn-primary">+ New Request</a>
    </div>

    <?php if ($msg):   ?><div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th><th>Patient</th><th>Hospital</th>
                <th>Blood Group</th><th>Units</th><th>Date</th><th>Status</th><th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php $count=0; while($row = mysqli_fetch_assoc($result)): $count++;
            $statusClass = $row['Status'] === 'Approved' ? 'status-ok' :
                          ($row['Status'] === 'Rejected'  ? 'status-critical' : 'status-low');
        ?>
        <tr>
            <td><?php echo $row['Request_ID']; ?></td>
            <td><?php echo htmlspecialchars($row['PatientName']); ?></td>
            <td><?php echo htmlspecialchars($row['HospitalName']); ?></td>
            <td><span class="blood-badge"><?php echo htmlspecialchars($row['Blood_Group']); ?></span></td>
            <td><?php echo $row['Units_Required']; ?></td>
            <td><?php echo $row['Request_Date'] ?? '—'; ?></td>
            <td><span class="status-tag <?php echo $statusClass; ?>"><?php echo $row['Status']; ?></span></td>
            <td>
                <?php if ($row['Status'] === 'Pending'): ?>
                    <a class="action-btn edit" href="?approve=<?php echo $row['Request_ID']; ?>"
                       onclick="return confirm('Approve this request?');">Approve</a>
                    <a class="action-btn delete" href="?reject=<?php echo $row['Request_ID']; ?>"
                       onclick="return confirm('Reject this request?');">Reject</a>
                <?php else: ?>
                    <span class="muted">—</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
        <?php if ($count===0): ?><tr><td colspan="8" class="empty-msg">No requests found.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
