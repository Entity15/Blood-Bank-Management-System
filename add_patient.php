<?php
include 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
$success=$error='';
if (isset($_POST['submit'])) {
    $pid  = (int)$_POST['patient_id'];
    $name = mysqli_real_escape_string($conn,$_POST['name']);
    $dis  = mysqli_real_escape_string($conn,$_POST['disease_name']);
    $dd   = mysqli_real_escape_string($conn,$_POST['diagnosis_date']);
    $notes= mysqli_real_escape_string($conn,$_POST['notes']);
    $addr = mysqli_real_escape_string($conn,$_POST['address']);
    $ph   = mysqli_real_escape_string($conn,$_POST['phone']);
    $dr   = mysqli_real_escape_string($conn,$_POST['date_registered']);
    if (!$pid) { $error="Patient ID is required."; }
    elseif (mysqli_query($conn,"INSERT INTO patient (Patient_ID,Name,Disease_Name,Diagnosis_Date,Notes,Address,Phone,Date_Registered) VALUES ($pid,'$name','$dis','$dd','$notes','$addr','$ph','$dr')"))
        $success="Patient record added!";
    else $error=mysqli_error($conn);
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Add Patient</title><link rel="stylesheet" href="style.css"></head><body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-header"><h1>Add Patient Record</h1><a href="view_patients.php" class="btn btn-secondary">← Back</a></div>
    <p style="color:var(--gray-600);margin-bottom:16px;font-size:.9rem;">Each row represents one patient–disease combination. The same Patient ID can appear multiple times for different conditions.</p>
    <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <div class="form-card"><form method="POST">
        <div class="form-row">
            <div class="form-group"><label>Patient ID <span class="req">*</span></label><input type="number" name="patient_id" min="1" required placeholder="Numeric patient identifier"></div>
            <div class="form-group"><label>Full Name <span class="req">*</span></label><input type="text" name="name" required placeholder="Patient full name"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Disease / Condition</label><input type="text" name="disease_name" placeholder="e.g. Thalassemia"></div>
            <div class="form-group"><label>Diagnosis Date</label><input type="date" name="diagnosis_date"></div>
        </div>
        <div class="form-group"><label>Notes</label><input type="text" name="notes" placeholder="Additional notes…"></div>
        <div class="form-group"><label>Address</label><input type="text" name="address" placeholder="Full address"></div>
        <div class="form-row">
            <div class="form-group"><label>Phone</label><input type="text" name="phone"></div>
            <div class="form-group"><label>Date Registered</label><input type="date" name="date_registered" value="<?php echo date('Y-m-d'); ?>"></div>
        </div>
        <div class="form-actions"><button type="submit" name="submit" class="btn btn-primary">Add Patient</button><a href="view_patients.php" class="btn btn-secondary">Cancel</a></div>
    </form></div>
</div></body></html>
