<?php
include 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
$donors = mysqli_query($conn,"SELECT * FROM donor ORDER BY Name");
$success=$error='';
if (isset($_POST['submit'])) {
    $did  = (int)$_POST['donor_id'];
    $bg   = mysqli_real_escape_string($conn,$_POST['blood_group']);
    $units= (int)$_POST['units'];
    $cd   = mysqli_real_escape_string($conn,$_POST['collection_date']);
    $ed   = mysqli_real_escape_string($conn,$_POST['expiry_date']);
    if (mysqli_query($conn,"INSERT INTO blood (Blood_Group,Units,Collection_Date,Expiry_Date) VALUES ('$bg',$units,'$cd','$ed')")) {
        $bid = mysqli_insert_id($conn);
        mysqli_query($conn,"INSERT INTO donation (Donor_ID,Blood_ID,Units,Donation_Date,Expiry_Date) VALUES ($did,$bid,$units,'$cd','$ed')");
        $success="Donation recorded successfully!";
    } else $error=mysqli_error($conn);
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Record Donation</title><link rel="stylesheet" href="style.css"></head><body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-header"><h1>Record Donation</h1><a href="view_donations.php" class="btn btn-secondary">← History</a></div>
    <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <div class="form-card"><form method="POST">
        <div class="form-group"><label>Donor <span class="req">*</span></label>
            <select name="donor_id" required><option value="">— Select Donor —</option>
            <?php while($d=mysqli_fetch_assoc($donors)): ?><option value="<?php echo $d['Donor_ID']; ?>"><?php echo htmlspecialchars($d['Name']); ?> (<?php echo $d['Blood_Group']; ?>)</option><?php endwhile; ?>
            </select></div>
        <div class="form-row">
            <div class="form-group"><label>Blood Group <span class="req">*</span></label><select name="blood_group" required><option value="">— Select —</option><?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $b): ?><option><?php echo $b; ?></option><?php endforeach; ?></select></div>
            <div class="form-group"><label>Units <span class="req">*</span></label><input type="number" name="units" min="1" required value="1"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Collection Date <span class="req">*</span></label><input type="date" name="collection_date" required value="<?php echo date('Y-m-d'); ?>"></div>
            <div class="form-group"><label>Expiry Date <span class="req">*</span></label><input type="date" name="expiry_date" required></div>
        </div>
        <div class="form-actions"><button type="submit" name="submit" class="btn btn-primary">Record Donation</button></div>
    </form></div>
</div></body></html>
