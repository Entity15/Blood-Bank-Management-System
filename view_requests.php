<?php
include 'config.php';

if (isset($_GET['approve'])) {
    $id = $_GET['approve'];

    // Get request details
    $req = mysqli_fetch_assoc(mysqli_query($conn, 
        "SELECT * FROM Blood_Request WHERE Request_ID=$id"));

    $blood = $req['Blood_Group'];
    $units = $req['Units_Required'];

    // Check stock
    $stock = mysqli_fetch_assoc(mysqli_query($conn, 
        "SELECT * FROM Blood_Stock WHERE Blood_Group='$blood'"));

    if ($stock['Units_Available'] >= $units) {
        // Deduct stock
        mysqli_query($conn, 
            "UPDATE Blood_Stock 
             SET Units_Available = Units_Available - $units
             WHERE Blood_Group='$blood'");

        // Approve request
        mysqli_query($conn, 
            "UPDATE Blood_Request 
             SET Status='Approved' 
             WHERE Request_ID=$id");

        echo "Request Approved!";
    } else {
        echo "Not enough blood available!";
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

<h2>Blood Requests</h2>

<table border="1" cellpadding="10">
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
            <a href="?approve=<?php echo $row['Request_ID']; ?>">Approve</a> |
            <a href="?reject=<?php echo $row['Request_ID']; ?>">Reject</a>
        <?php } ?>
    </td>
</tr>
<?php } ?>

</table>