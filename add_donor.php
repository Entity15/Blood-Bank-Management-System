<?php
include 'config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $sql = "INSERT INTO Donor (Name, Age, Phone, Address) 
            VALUES ('$name', '$age', '$phone', '$address')";

    if (mysqli_query($conn, $sql)) {
        echo "Donor added successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Donor</title>
</head>
<body>

<h2>Add Donor</h2>

<form method="POST">
    Name: <input type="text" name="name" required><br><br>
    Age: <input type="number" name="age"><br><br>
    Phone: <input type="text" name="phone"><br><br>
    Address: <textarea name="address"></textarea><br><br>

    <button type="submit" name="submit">Add Donor</button>
</form>

</body>
</html>