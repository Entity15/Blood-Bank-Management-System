<?php
include 'config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch hospitals & patients
$hospitals = mysqli_query($conn, "SELECT * FROM Hospital");
$patients = mysqli_query($conn, "SELECT * FROM Patient");

if (isset($_POST['submit'])) {
    $hospital_id = $_POST['hospital_id'];
    $patient_id = $_POST['patient_id'];
    $blood_group = $_POST['blood_group'];
    $units = $_POST['units'];
    $date = $_POST['date'];

    $sql = "INSERT INTO Request (Hospital_ID, Patient_ID, Blood_Group, Units, Request_Date)
            VALUES ('$hospital_id', '$patient_id', '$blood_group', '$units', '$date')";

    if (mysqli_query($conn, $sql)) {
        echo "Request created successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Request Blood</title>
</head>
<body>

<h2>Create Blood Request</h2>

<form method="POST">

    Hospital:
    <select name="hospital_id" required>
        <option value="">Select Hospital</option>
        <?php while($h = mysqli_fetch_assoc($hospitals)) { ?>
            <option value="<?php echo $h['Hospital_ID']; ?>">
                <?php echo $h['Name']; ?>
            </option>
        <?php } ?>
    </select><br><br>

    Patient:
    <select name="patient_id" required>
        <option value="">Select Patient</option>
        <?php while($p = mysqli_fetch_assoc($patients)) { ?>
            <option value="<?php echo $p['Patient_ID']; ?>">
                <?php echo $p['Name']; ?>
            </option>
        <?php } ?>
    </select><br><br>

    Blood Group:
    <input type="text" name="blood_group" required><br><br>

    Units:
    <input type="number" name="units" required><br><br>

    Date:
    <input type="date" name="date" required><br><br>

    <button type="submit" name="submit">Submit Request</button>

</form>

</body>
</html>