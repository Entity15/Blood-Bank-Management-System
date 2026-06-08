<?php
include 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
$hospitals = mysqli_query($conn,"SELECT * FROM hospital ORDER BY Name");
$patients  = mysqli_query($conn,"SELECT * FROM patient ORDER BY Name, Disease_Name");
$success=$error='';
if (isset($_POST['submit'])) {
    $hid = (int)$_POST['hospital_id'];
    $pid = (int)$_POST['patient_id'];
    $bg  = mysqli_real_escape_string($conn,$_POST['blood_group']);
    $u   = (int)$_POST['units'];
    $dt  = mysqli_real_escape_string($conn,$_POST['request_date']);
    if (mysqli_query($conn,"INSERT INTO request (Patient_ID,Hospital_ID,Blood_Group,Units,Request_Date) VALUES ($pid,$hid,'$bg',$u,'$dt')"))
        $success="Blood request submitted successfully!";
    else $error=mysqli_error($conn);
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>New Request</title><link rel="stylesheet" href="style.css"></head><body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-header"><h1>New Blood Request</h1><a href="view_requests.php" class="btn btn-secondary">← All Requests</a></div>
    <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <div class="form-card"><form method="POST">
        <div class="form-row">
            <div class="form-group"><label>Hospital <span class="req">*</span></label>
                <select name="hospital_id" required><option value="">— Select Hospital —</option>
                <?php while($h=mysqli_fetch_assoc($hospitals)): ?><option value="<?php echo $h['Hospital_ID']; ?>"><?php echo htmlspecialchars($h['Name']); ?></option><?php endwhile; ?>
                </select></div>
            <div class="form-group"><label>Patient Record <span class="req">*</span></label>
                <select name="patient_id" required><option value="">— Select Patient —</option>
                <?php while($p=mysqli_fetch_assoc($patients)): ?><option value="<?php echo $p['Patient_Disease_ID']; ?>"><?php echo htmlspecialchars($p['Name']); ?> — <?php echo htmlspecialchars($p['Disease_Name']); ?></option><?php endwhile; ?>
                </select></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Blood Group <span class="req">*</span></label><select name="blood_group" required><option value="">— Select —</option><?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $b): ?><option><?php echo $b; ?></option><?php endforeach; ?></select></div>
            <div class="form-group"><label>Units Required <span class="req">*</span></label><input type="number" name="units" min="1" required></div>
        </div>
        <div class="form-group"><label>Request Date <span class="req">*</span></label><input type="date" name="request_date" required value="<?php echo date('Y-m-d'); ?>"></div>
        <div class="form-actions"><button type="submit" name="submit" class="btn btn-primary">Submit Request</button><a href="view_requests.php" class="btn btn-secondary">Cancel</a></div>
    </form></div>
</div></body></html>
