<?php
include 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
$id = (int)$_GET['id'];
$r = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM donor WHERE Donor_ID=$id"));
if (!$r) { header("Location: view_donors.php"); exit(); }
$success=$error='';
if (isset($_POST['update'])) {
    $name=$_POST['name']; $age=(int)$_POST['age']; $phone=$_POST['phone']; $bg=$_POST['blood_group'];
    $str=$_POST['street']; $city=$_POST['city']; $state=$_POST['state']; $pin=$_POST['pin_code']; $dj=$_POST['date_joined'];
    foreach(['name','phone','bg','str','city','state','pin','dj'] as $v) $$v = mysqli_real_escape_string($conn, $$v);
    if (mysqli_query($conn, "UPDATE donor SET Name='$name',Age=$age,Phone='$phone',Blood_Group='$bg',Street='$str',City='$city',State='$state',PIN_Code='$pin',Date_Joined='$dj' WHERE Donor_ID=$id")) {
        $success="Donor updated!"; $r=mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM donor WHERE Donor_ID=$id"));
    } else $error=mysqli_error($conn);
}
$bgroups=['A+','A-','B+','B-','AB+','AB-','O+','O-'];
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Edit Donor</title><link rel="stylesheet" href="style.css"></head><body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-header"><h1>Edit Donor</h1><a href="view_donors.php" class="btn btn-secondary">← Back</a></div>
    <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <div class="form-card"><form method="POST">
        <div class="form-row">
            <div class="form-group"><label>Full Name</label><input type="text" name="name" value="<?php echo htmlspecialchars($r['Name']); ?>" required></div>
            <div class="form-group"><label>Blood Group</label><select name="blood_group"><?php foreach($bgroups as $b): ?><option<?php echo $r['Blood_Group']===$b?' selected':''; ?>><?php echo $b; ?></option><?php endforeach; ?></select></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Age</label><input type="number" name="age" value="<?php echo $r['Age']; ?>" min="18" max="65"></div>
            <div class="form-group"><label>Phone</label><input type="text" name="phone" value="<?php echo htmlspecialchars($r['Phone']); ?>"></div>
        </div>
        <div class="section-sep">Address</div>
        <div class="form-group"><label>Street</label><input type="text" name="street" value="<?php echo htmlspecialchars($r['Street']); ?>"></div>
        <div class="form-row">
            <div class="form-group"><label>City</label><input type="text" name="city" value="<?php echo htmlspecialchars($r['City']); ?>"></div>
            <div class="form-group"><label>State</label><input type="text" name="state" value="<?php echo htmlspecialchars($r['State']); ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>PIN Code</label><input type="text" name="pin_code" value="<?php echo htmlspecialchars($r['PIN_Code']); ?>"></div>
            <div class="form-group"><label>Date Joined</label><input type="date" name="date_joined" value="<?php echo $r['Date_Joined']; ?>"></div>
        </div>
        <div class="form-actions"><button type="submit" name="update" class="btn btn-primary">Update</button><a href="view_donors.php" class="btn btn-secondary">Cancel</a></div>
    </form></div>
</div></body></html>
