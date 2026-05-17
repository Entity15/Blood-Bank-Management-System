<?php
include 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }

$success = $error = '';

if (isset($_POST['submit'])) {
    $name    = mysqli_real_escape_string($conn, $_POST['name']);
    $age     = (int)$_POST['age'];
    $phone   = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    if ($age < 18 || $age > 65) {
        $error = "Donor age must be between 18 and 65.";
    } else {
        $sql = "INSERT INTO donor (Name, Age, Phone, Address)
                VALUES ('$name', $age, '$phone', '$address')";
        if (mysqli_query($conn, $sql)) {
            $success = "Donor <strong>$name</strong> registered successfully!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Donor – Blood Bank</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-header">
        <h1>Register Donor</h1>
        <a href="view_donors.php" class="btn btn-secondary">← Back to Donors</a>
    </div>

    <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <div class="form-card">
        <form method="POST">
            <div class="form-group">
                <label>Full Name <span class="required">*</span></label>
                <input type="text" name="name" required placeholder="e.g. Abdullah Ayman">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Age <span class="required">*</span></label>
                    <input type="number" name="age" min="18" max="65" required placeholder="18–65">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" placeholder="e.g. 01700000000">
                </div>
            </div>
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" rows="3" placeholder="Full address..."></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" name="submit" class="btn btn-primary">Register Donor</button>
                <a href="view_donors.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
