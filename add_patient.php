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
    $age = $_POST['age'];
    $blood_group = $_POST['blood_group'];
    $disease = $_POST['disease'];

    $sql = "INSERT INTO Patient (Name, Age, Blood_Group, Disease)
            VALUES ('$name', '$age', '$blood_group', '$disease')";

    if (mysqli_query($conn, $sql)) {
        echo "Patient added successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Patient</title>
</head>
<body>

<h2>Add Patient</h2>

<form method="POST">
    Name: <input type="text" name="name" required><br><br>
    Age: <input type="number" name="age"><br><br>
    Blood Group: <input type="text" name="blood_group"><br><br>
    Disease: <input type="text" name="disease"><br><br>

    <button type="submit" name="submit">Add Patient</button>
</form>

</body>
</html>