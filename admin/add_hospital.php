<?php
include 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
$success=$error='';
if (isset($_POST['submit'])) {
    $name   = mysqli_real_escape_string($conn,$_POST['name']);
    $contact= mysqli_real_escape_string($conn,$_POST['contact']);
    $str    = mysqli_real_escape_string($conn,$_POST['street']);
    $city   = mysqli_real_escape_string($conn,$_POST['city']);
    $state  = mysqli_real_escape_string($conn,$_POST['state']);
    $pin    = mysqli_real_escape_string($conn,$_POST['pin_code']);
    if (mysqli_query($conn,"INSERT INTO hospital (Name,Contact,Street,City,State,PIN_Code) VALUES ('$name','$contact','$str','$city','$state','$pin')"))
        $success="Hospital <strong>$name</strong> registered!";
    else $error=mysqli_error($conn);
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Add Hospital</title><link rel="stylesheet" href="style.css"></head><body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-header"><h1>Register Hospital</h1><a href="view_hospitals.php" class="btn btn-secondary">← Back</a></div>
    <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <div class="form-card"><form method="POST">
        <div class="form-row">
            <div class="form-group"><label>Hospital Name <span class="req">*</span></label><input type="text" name="name" required placeholder="e.g. Ibn Sina Hospital"></div>
            <div class="form-group"><label>Contact</label><input type="text" name="contact" placeholder="Phone number"></div>
        </div>
        <div class="section-sep">Address</div>
        <div class="form-group"><label>Street</label><input type="text" name="street"></div>
        <div class="form-row">
            <div class="form-group"><label>City</label><input type="text" name="city"></div>
            <div class="form-group"><label>State / Division</label><input type="text" name="state"></div>
        </div>
        <div class="form-group"><label>PIN Code</label><input type="text" name="pin_code"></div>
        <div class="form-actions"><button type="submit" name="submit" class="btn btn-primary">Register Hospital</button><a href="view_hospitals.php" class="btn btn-secondary">Cancel</a></div>
    </form></div>
</div></body></html>
