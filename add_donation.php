<?php
include 'config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
// Fetch donors for dropdown
$donors = mysqli_query($conn, "SELECT * FROM Donor");

if (isset($_POST['submit'])) {
    $donor_id = $_POST['donor_id'];
    $blood_group = $_POST['blood_group'];
    $units = $_POST['units'];
    $collection_date = $_POST['collection_date'];
    $expiry_date = $_POST['expiry_date'];

    // Step 1: Insert into Blood table
    $blood_sql = "INSERT INTO Blood (Blood_Group, Units, Collection_Date, Expiry_Date)
                  VALUES ('$blood_group', '$units', '$collection_date', '$expiry_date')";

    if (mysqli_query($conn, $blood_sql)) {

        // Get last inserted Blood_ID
        $blood_id = mysqli_insert_id($conn);

        // Step 2: Insert into Donation table
        $donation_sql = "INSERT INTO Donation (Donor_ID, Blood_ID, Date)
                         VALUES ('$donor_id', '$blood_id', '$collection_date')";

        if (mysqli_query($conn, $donation_sql)) {

            // Step 3: Update donor total units
            mysqli_query($conn, "UPDATE Donor 
                                 SET Units_Donated = Units_Donated + $units 
                                 WHERE Donor_ID = $donor_id");

            echo "Donation recorded successfully!";
        } else {
            echo "Error in Donation: " . mysqli_error($conn);
        }

    } else {
        echo "Error in Blood: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Donation</title>
</head>
<body>

<h2>Record Donation</h2>

<form method="POST">

    Donor:
    <select name="donor_id" required>
        <option value="">Select Donor</option>
        <?php while($d = mysqli_fetch_assoc($donors)) { ?>
            <option value="<?php echo $d['Donor_ID']; ?>">
                <?php echo $d['Name']; ?>
            </option>
        <?php } ?>
    </select>
    <br><br>

    Blood Group:
    <input type="text" name="blood_group" placeholder="A+, B-, etc" required><br><br>

    Units:
    <input type="number" name="units" required><br><br>

    Collection Date:
    <input type="date" name="collection_date" required><br><br>

    Expiry Date:
    <input type="date" name="expiry_date" required><br><br>

    <button type="submit" name="submit">Record Donation</button>

</form>

</body>
</html>