<?php
include 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
$success = $error = '';
if (isset($_POST['submit'])) {
    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $age   = (int)$_POST['age'];
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $bg    = mysqli_real_escape_string($conn, $_POST['blood_group']);
    $str   = mysqli_real_escape_string($conn, $_POST['street']);
    $city  = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $pin   = mysqli_real_escape_string($conn, $_POST['pin_code']);
    $dj    = mysqli_real_escape_string($conn, $_POST['date_joined']);
    if ($age < 18 || $age > 65) { $error = "Donor age must be 18–65."; }
    else {
        if (mysqli_query($conn, "INSERT INTO donor (Name,Age,Phone,Blood_Group,Street,City,State,PIN_Code,Date_Joined) VALUES ('$name',$age,'$phone','$bg','$str','$city','$state','$pin','$dj')"))
            $success = "Donor <strong>$name</strong> registered!";
        else $error = mysqli_error($conn);
    }
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Add Donor – Blood Bank</title><link rel="stylesheet" href="style.css"></head><body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-header"><h1>Register Donor</h1><a href="view_donors.php" class="btn btn-secondary">← Back</a></div>
    <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <div class="form-card">
        <form method="POST">
            <div class="form-row">
                <div class="form-group"><label>Full Name <span class="req">*</span></label><input type="text" name="name" required placeholder="Full name"></div>
                <div class="form-group"><label>Blood Group <span class="req">*</span></label><select name="blood_group" required><option value="">— Select —</option><?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $b): ?><option><?php echo $b; ?></option><?php endforeach; ?></select></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Age <span class="req">*</span></label><input type="number" name="age" min="18" max="65" required></div>
                <div class="form-group"><label>Phone</label><input type="text" name="phone" placeholder="01700000000"></div>
            </div>
            <div class="section-sep">Address</div>
            <div class="form-group"><label>Street</label><input type="text" name="street" placeholder="Street / Area"></div>
            <div class="form-row">
                <div class="form-group"><label>City</label><input type="text" name="city" placeholder="City"></div>
                <div class="form-group"><label>State / Division</label><input type="text" name="state" placeholder="e.g. Sylhet"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>PIN Code</label><input type="text" name="pin_code"></div>
                <div class="form-group"><label>Date Joined</label><input type="date" name="date_joined" value="<?php echo date('Y-m-d'); ?>"></div>
            </div>
            <div class="form-actions"><button type="submit" name="submit" class="btn btn-primary">Register Donor</button><a href="view_donors.php" class="btn btn-secondary">Cancel</a></div>
        </form>
    </div>
</div></body></html>
