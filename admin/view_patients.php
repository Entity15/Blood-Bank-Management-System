<?php
include 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
$s = isset($_GET['search']) ? mysqli_real_escape_string($conn,$_GET['search']) : '';
$w = $s ? "WHERE Name LIKE '%$s%' OR Disease_Name LIKE '%$s%'" : '';
$result = mysqli_query($conn,"SELECT * FROM patient $w ORDER BY Patient_ID, Diagnosis_Date DESC");
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Patients</title><link rel="stylesheet" href="style.css"></head><body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-header"><h1>Patient Records</h1><a href="add_patient.php" class="btn btn-primary">+ Add Patient</a></div>
    <form method="GET" class="search-bar">
        <input type="text" name="search" placeholder="Search by name or disease…" value="<?php echo htmlspecialchars($s); ?>">
        <button type="submit" class="btn btn-secondary">Search</button>
        <?php if($s): ?><a href="view_patients.php" class="btn btn-secondary">Clear</a><?php endif; ?>
    </form>
    <table><thead><tr><th>Rec ID</th><th>Patient ID</th><th>Name</th><th>Disease</th><th>Diagnosis Date</th><th>Notes</th><th>Phone</th><th>Registered</th></tr></thead>
    <tbody>
    <?php $n=0; while($r=mysqli_fetch_assoc($result)): $n++; ?>
    <tr>
        <td><?php echo $r['Patient_Disease_ID']; ?></td>
        <td><span class="badge"><?php echo $r['Patient_ID']; ?></span></td>
        <td><?php echo htmlspecialchars($r['Name']); ?></td>
        <td><?php echo htmlspecialchars($r['Disease_Name']); ?></td>
        <td><?php echo $r['Diagnosis_Date']; ?></td>
        <td><?php echo htmlspecialchars($r['Notes']); ?></td>
        <td><?php echo htmlspecialchars($r['Phone']); ?></td>
        <td><?php echo $r['Date_Registered']; ?></td>
    </tr>
    <?php endwhile; if($n===0): ?><tr><td colspan="8" class="empty">No patient records found.</td></tr><?php endif; ?>
    </tbody></table>
</div></body></html>
