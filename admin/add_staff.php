<?php
include 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
$success=$error='';
if (isset($_POST['submit'])) {
    $name   = mysqli_real_escape_string($conn,$_POST['name']);
    $phone  = mysqli_real_escape_string($conn,$_POST['phone']);
    $role   = mysqli_real_escape_string($conn,$_POST['role']);
    $salary = (float)$_POST['salary'];
    if (mysqli_query($conn,"INSERT INTO staff (Name,Phone,Role,Salary) VALUES ('$name','$phone','$role',$salary)"))
        $success="Staff member <strong>$name</strong> added!";
    else $error=mysqli_error($conn);
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Add Staff</title><link rel="stylesheet" href="style.css"></head><body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-header"><h1>Add Staff</h1><a href="view_staff.php" class="btn btn-secondary">← Back</a></div>
    <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <div class="form-card"><form method="POST">
        <div class="form-row">
            <div class="form-group"><label>Full Name <span class="req">*</span></label><input type="text" name="name" required></div>
            <div class="form-group"><label>Role</label><input type="text" name="role" placeholder="e.g. Technician, Manager"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Phone</label><input type="text" name="phone"></div>
            <div class="form-group"><label>Salary (BDT)</label><input type="number" name="salary" min="0" step="0.01"></div>
        </div>
        <div class="form-actions"><button type="submit" name="submit" class="btn btn-primary">Add Staff</button><a href="view_staff.php" class="btn btn-secondary">Cancel</a></div>
    </form></div>
</div></body></html>
