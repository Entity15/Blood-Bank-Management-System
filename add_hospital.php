<?php
include 'config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
?>
<?php
include 'config.php';

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $address = $_POST['address'];

    $sql = "INSERT INTO Hospital (Name, Address)
            VALUES ('$name', '$address')";

    if (mysqli_query($conn, $sql)) {
        echo "Hospital added successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Hospital</title>
</head>
<body>

<h2>Add Hospital</h2>

<form method="POST">
    Name: <input type="text" name="name" required><br><br>
    Address: <textarea name="address"></textarea><br><br>

    <button type="submit" name="submit">Add Hospital</button>
</form>

</body>
</html>