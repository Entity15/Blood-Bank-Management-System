<?php
require_once 'config.php';
// Show only staff with visible/public-facing roles (no salary shown)
$staff = mysqli_query($conn, "SELECT Name, Phone, Role FROM staff ORDER BY Role, Name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us — Blood Bank</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <div>
            <h1>Contact Us</h1>
            <p>Reach out for donations, queries, or to place a blood request through your hospital.</p>
        </div>
    </div>

    <!-- General contact cards -->
    <div class="contact-grid" style="margin-bottom:40px;">
        <div class="contact-card">
            <div class="contact-icon">📍</div>
            <h3>Our Location</h3>
            <p>Blood Bank Management Centre<br>Sylhet, Bangladesh</p>
        </div>
        <div class="contact-card">
            <div class="contact-icon">⏰</div>
            <h3>Working Hours</h3>
            <p>Saturday – Thursday<br>8:00 AM – 5:00 PM</p>
        </div>
        <div class="contact-card">
            <div class="contact-icon">📋</div>
            <h3>How to Request</h3>
            <p>Blood requests must be placed by a registered partner hospital on behalf of a patient.</p>
        </div>
    </div>

    <!-- Staff directory (no salary) -->
    <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:14px;">Our Team</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Role</th>
                <th>Phone</th>
            </tr>
        </thead>
        <tbody>
        <?php $n = 0; while ($s = mysqli_fetch_assoc($staff)): $n++; ?>
        <tr>
            <td><strong><?php echo htmlspecialchars($s['Name']); ?></strong></td>
            <td><span class="badge"><?php echo htmlspecialchars($s['Role']); ?></span></td>
            <td><?php echo htmlspecialchars($s['Phone']); ?></td>
        </tr>
        <?php endwhile; if ($n === 0): ?>
        <tr><td colspan="3" class="empty">No staff on record.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<footer>
    <p>🩸 Blood Bank Management System &nbsp;|&nbsp; <a href="contact.php">Contact Us</a> &nbsp;|&nbsp; <a href="../login.php">Staff Login</a></p>
</footer>
</body>
</html>
