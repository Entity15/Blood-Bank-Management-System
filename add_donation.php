<?php
include 'config.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }

$donors  = mysqli_query($conn, "SELECT * FROM donor ORDER BY Name");
$success = $error = '';

if (isset($_POST['submit'])) {
    $donor_id       = (int)$_POST['donor_id'];
    $blood_group    = mysqli_real_escape_string($conn, $_POST['blood_group']);
    $units          = (int)$_POST['units'];
    $collection_date = mysqli_real_escape_string($conn, $_POST['collection_date']);
    $expiry_date    = mysqli_real_escape_string($conn, $_POST['expiry_date']);

    // Insert into blood table
    $blood_sql = "INSERT INTO blood (Blood_Group, Units, Collection_Date, Expiry_Date)
                  VALUES ('$blood_group', $units, '$collection_date', '$expiry_date')";

    if (mysqli_query($conn, $blood_sql)) {
        $blood_id = mysqli_insert_id($conn);

        // Link donation record
        mysqli_query($conn, "INSERT INTO donation (Donor_ID, Blood_ID, Date)
                             VALUES ($donor_id, $blood_id, '$collection_date')");

        // Update donor total
        mysqli_query($conn, "UPDATE donor SET Units_Donated = Units_Donated + $units WHERE Donor_ID = $donor_id");

        // Update blood_stock aggregate
        mysqli_query($conn, "UPDATE blood_stock SET Units_Available = Units_Available + $units
                             WHERE Blood_Group = '$blood_group'");

        $success = "Donation recorded and stock updated successfully!";
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
    <title>Record Donation – Blood Bank</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-header">
        <h1>Record Donation</h1>
        <a href="view_donations.php" class="btn btn-secondary">← Donation History</a>
    </div>

    <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <div class="form-card">
        <form method="POST">
            <div class="form-group">
                <label>Donor <span class="required">*</span></label>
                <select name="donor_id" required>
                    <option value="">— Select Donor —</option>
                    <?php while($d = mysqli_fetch_assoc($donors)): ?>
                        <option value="<?php echo $d['Donor_ID']; ?>">
                            <?php echo htmlspecialchars($d['Name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Blood Group <span class="required">*</span></label>
                    <select name="blood_group" required>
                        <option value="">— Select —</option>
                        <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
                            <option value="<?php echo $bg; ?>"><?php echo $bg; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Units <span class="required">*</span></label>
                    <input type="number" name="units" min="1" required placeholder="1">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Collection Date <span class="required">*</span></label>
                    <input type="date" name="collection_date" required>
                </div>
                <div class="form-group">
                    <label>Expiry Date <span class="required">*</span></label>
                    <input type="date" name="expiry_date" required>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" name="submit" class="btn btn-primary">Record Donation</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
