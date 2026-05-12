<?php
include 'config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$patients = mysqli_query($conn, "SELECT * FROM Patient");
$hospitals = mysqli_query($conn, "SELECT * FROM Hospital");

if (isset($_POST['submit'])) {
    $patient_id = $_POST['patient_id'];
    $hospital_id = $_POST['hospital_id'];
    $blood_group = $_POST['blood_group'];
    $units = $_POST['units'];

    $sql = "INSERT INTO Blood_Request 
            (Patient_ID, Hospital_ID, Blood_Group, Units_Required)
            VALUES ('$patient_id', '$hospital_id', '$blood_group', '$units')";

    if (mysqli_query($conn, $sql)) {
        echo "Request submitted!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<h2>Create Blood Request</h2>

<form method="POST">
    Patient:
    <select name="patient_id">
        <?php while($p = mysqli_fetch_assoc($patients)) { ?>
            <option value="<?php echo $p['Patient_ID']; ?>">
                <?php echo $p['Name']; ?>
            </option>
        <?php } ?>
    </select><br><br>

    Hospital:
    <select name="hospital_id">
        <?php while($h = mysqli_fetch_assoc($hospitals)) { ?>
            <option value="<?php echo $h['Hospital_ID']; ?>">
                <?php echo $h['Name']; ?>
            </option>
        <?php } ?>
    </select><br><br>

    Blood Group: <input type="text" name="blood_group"><br><br>
    Units Required: <input type="number" name="units"><br><br>

    <button name="submit">Submit</button>
</form>