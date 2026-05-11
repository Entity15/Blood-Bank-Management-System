<?php
include 'config.php';

$id = $_GET['id'];

// Fetch existing data
$result = mysqli_query($conn, "SELECT * FROM Donor WHERE Donor_ID=$id");
$row = mysqli_fetch_assoc($result);

if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $sql = "UPDATE Donor 
            SET Name='$name', Age='$age', Phone='$phone', Address='$address'
            WHERE Donor_ID=$id";

    if (mysqli_query($conn, $sql)) {
        echo "Updated successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Donor</title>
</head>
<body>

<h2>Edit Donor</h2>

<form method="POST">
    Name: <input type="text" name="name" value="<?php echo $row['Name']; ?>" required><br><br>
    Age: <input type="number" name="age" value="<?php echo $row['Age']; ?>"><br><br>
    Phone: <input type="text" name="phone" value="<?php echo $row['Phone']; ?>"><br><br>
    Address: <textarea name="address"><?php echo $row['Address']; ?></textarea><br><br>

    <button type="submit" name="update">Update</button>
</form>

</body>
</html>