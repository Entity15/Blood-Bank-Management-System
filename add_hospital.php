<?php
include 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }

$success = $error = '';

if (isset($_POST['submit'])) {
    $name    = mysqli_real_escape_string($conn, $_POST['name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $sql = "INSERT INTO hospital (Name, Address) VALUES ('$name', '$address')";

    if (mysqli_query($conn, $sql)) {
        $success = "Hospital <strong>$name</strong> added successfully!";
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
    <title>Add Hospital – Blood Bank</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-header">
        <h1>Register Hospital</h1>
        <a href="view_hospitals.php" class="btn btn-secondary">← Back to Hospitals</a>
    </div>

    <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <div class="form-card">
        <form method="POST">
            <div class="form-group">
                <label>Hospital Name <span class="required">*</span></label>
                <input type="text" name="name" required placeholder="e.g. Ibn Sina Hospital">
            </div>
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" rows="3" placeholder="Full hospital address..."></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" name="submit" class="btn btn-primary">Register Hospital</button>
                <a href="view_hospitals.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
