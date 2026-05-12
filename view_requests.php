<?php
require_once 'config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['approve'])) {
    $id = $_GET['approve'];

    $req = mysqli_fetch_assoc(mysqli_query($conn, 
        "SELECT * FROM Blood_Request WHERE Request_ID=$id"));

    $blood = $req['Blood_Group'];
    $units = $req['Units_Required'];

    $stock = mysqli_fetch_assoc(mysqli_query($conn, 
        "SELECT * FROM Blood_Stock WHERE Blood_Group='$blood'"));

    if ($stock['Units_Available'] >= $units) {
        mysqli_query($conn, 
            "UPDATE Blood_Stock 
             SET Units_Available = Units_Available - $units
             WHERE Blood_Group='$blood'");

        mysqli_query($conn, 
            "UPDATE Blood_Request 
             SET Status='Approved' 
             WHERE Request_ID=$id");

        echo "<p style='color:green;'>Request Approved!</p>";
    } else {
        echo "<p style='color:red;'>Not enough blood available!</p>";
    }
}

if (isset($_GET['reject'])) {
    $id = $_GET['reject'];
    mysqli_query($conn, 
        "UPDATE Blood_Request SET Status='Rejected' WHERE Request_ID=$id");
}

$result = mysqli_query($conn, "
    SELECT br.*, p.Name AS PatientName, h.Name AS HospitalName
    FROM Blood_Request br
    JOIN Patient p ON br.Patient_ID = p.Patient_ID
    JOIN Hospital h ON br.Hospital_ID = h.Hospital_ID
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Blood Requests</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<?php include 'navbar.php'; ?>

<div class="container">

<h2>Blood Requests</h2>

<table>
<tr>
    <th>ID</th>
    <th>Patient</th>
    <th>Hospital</th>
    <th>Blood Group</th>
    <th>Units</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?php echo $row['Request_ID']; ?></td>
    <td><?php echo $row['PatientName']; ?></td>
    <td><?php echo $row['HospitalName']; ?></td>
    <td><?php echo $row['Blood_Group']; ?></td>
    <td><?php echo $row['Units_Required']; ?></td>
    <td><?php echo $row['Status']; ?></td>
    <td>
        <?php if ($row['Status'] == 'Pending') { ?>
            <a class="action-btn" href="?approve=<?php echo $row['Request_ID']; ?>">Approve</a>
            <a class="action-btn" href="?reject=<?php echo $row['Request_ID']; ?>">Reject</a>
        <?php } ?>
    </td>
</tr>
<?php } ?>

</table>

</div>

</body>
</html>