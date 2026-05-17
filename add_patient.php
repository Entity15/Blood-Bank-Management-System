<?php
include 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }

$success = $error = '';

if (isset($_POST['submit'])) {
    $name        = mysqli_real_escape_string($conn, $_POST['name']);
    $age         = (int)$_POST['age'];
    $blood_group = mysqli_real_escape_string($conn, $_POST['blood_group']);
    $disease     = mysqli_real_escape_string($conn, $_POST['disease']);

    $sql = "INSERT INTO patient (Name, Age, Blood_Group, Disease)
            VALUES ('$name', $age, '$blood_group', '$disease')";

    if (mysqli_query($conn, $sql)) {
        $success = "Patient <strong>$name</strong> added successfully!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Patient – Blood Bank</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-header">
        <h1>Add Patient</h1>
        <a href="view_patients.php" class="btn btn-secondary">← Back to Patients</a>
    </div>

    <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <div class="form-card">
        <form method="POST">
            <div class="form-group">
                <label>Full Name <span class="required">*</span></label>
                <input type="text" name="name" required placeholder="Patient full name">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Age</label>
                    <input type="number" name="age" min="0" placeholder="Age">
                </div>
                <div class="form-group">
                    <label>Blood Group</label>
                    <select name="blood_group">
                        <option value="">— Unknown —</option>
                        <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
                            <option value="<?php echo $bg; ?>"><?php echo $bg; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Disease / Condition</label>
                <input type="text" name="disease" placeholder="e.g. Thalassemia">
            </div>
            <div class="form-actions">
                <button type="submit" name="submit" class="btn btn-primary">Add Patient</button>
                <a href="view_patients.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
