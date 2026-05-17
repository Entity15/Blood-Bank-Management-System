<?php
include 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }

$id = (int)$_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM donor WHERE Donor_ID=$id");
if (mysqli_num_rows($result) === 0) { header("Location: view_donors.php"); exit(); }
$row = mysqli_fetch_assoc($result);

$success = $error = '';

if (isset($_POST['update'])) {
    $name    = mysqli_real_escape_string($conn, $_POST['name']);
    $age     = (int)$_POST['age'];
    $phone   = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $sql = "UPDATE donor SET Name='$name', Age=$age, Phone='$phone', Address='$address' WHERE Donor_ID=$id";
    if (mysqli_query($conn, $sql)) {
        $success = "Donor updated successfully!";
        $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM donor WHERE Donor_ID=$id"));
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
    <title>Edit Donor – Blood Bank</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-header">
        <h1>Edit Donor</h1>
        <a href="view_donors.php" class="btn btn-secondary">← Back</a>
    </div>

    <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <div class="form-card">
        <form method="POST">
            <div class="form-group">
                <label>Full Name <span class="required">*</span></label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($row['Name']); ?>" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Age</label>
                    <input type="number" name="age" value="<?php echo $row['Age']; ?>" min="18" max="65">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($row['Phone']); ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" rows="3"><?php echo htmlspecialchars($row['Address']); ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" name="update" class="btn btn-primary">Update Donor</button>
                <a href="view_donors.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
