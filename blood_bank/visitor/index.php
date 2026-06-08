<?php
require_once 'config.php';

// ── Live stats ──────────────────────────────────────────────────────────────
$total_donors    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM donor"))[0];
$total_hospitals = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM hospital"))[0];
$total_donations = mysqli_fetch_row(mysqli_query($conn, "SELECT COALESCE(SUM(Units),0) FROM blood WHERE Expiry_Date >= CURDATE()"))[0];
$types_available = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(DISTINCT Blood_Group) FROM blood WHERE Expiry_Date >= CURDATE()"))[0];

// ── Blood stock snapshot (top 4 non-zero groups) ────────────────────────────
$stock_result = mysqli_query($conn, "
    SELECT b.Blood_Group,
           COALESCE(SUM(b.Units), 0) AS Total_Donated,
           COALESCE((
               SELECT SUM(dtr.Units_Provided)
               FROM donation_to_request dtr
               JOIN donation d2  ON dtr.Donation_ID = d2.Donation_ID
               JOIN blood    b2  ON d2.Blood_ID     = b2.Blood_ID
               WHERE b2.Blood_Group = b.Blood_Group
           ), 0) AS Total_Fulfilled
    FROM blood b
    WHERE b.Expiry_Date >= CURDATE()
    GROUP BY b.Blood_Group
    HAVING (Total_Donated - Total_Fulfilled) > 0
    ORDER BY Blood_Group
    LIMIT 8
");
$all_groups = ['A+'=>0,'A-'=>0,'B+'=>0,'B-'=>0,'AB+'=>0,'AB-'=>0,'O+'=>0,'O-'=>0];
while ($r = mysqli_fetch_assoc($stock_result)) {
    $all_groups[$r['Blood_Group']] = $r['Total_Donated'] - $r['Total_Fulfilled'];
}

// ── Partner hospitals ───────────────────────────────────────────────────────
$hospitals = mysqli_query($conn, "
    SELECT h.*, c.End_Date
    FROM hospital h
    LEFT JOIN contract c ON c.Hospital_ID = h.Hospital_ID AND c.End_Date >= CURDATE()
    ORDER BY h.Name
    LIMIT 6
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Bank — Home</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<!-- ── HERO ── -->
<section class="hero">
    <div class="hero-icon">🩸</div>
    <h1>Every Drop Counts</h1>
    <p>Our blood bank connects generous donors with patients and hospitals in need. Check live availability, find partner hospitals, and learn how you can help save lives.</p>
    <div class="hero-actions">
        <a href="blood_availability.php" class="btn-hero-primary">Check Blood Availability</a>
        <a href="hospitals.php" class="btn-hero-secondary">Partner Hospitals</a>
    </div>
</section>

<!-- ── LIVE STATS ── -->
<div class="container">
    <div class="stats-strip">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-val"><?php echo $total_donors; ?></div>
            <div class="stat-lbl">Registered Donors</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🏥</div>
            <div class="stat-val"><?php echo $total_hospitals; ?></div>
            <div class="stat-lbl">Partner Hospitals</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💉</div>
            <div class="stat-val"><?php echo $total_donations; ?></div>
            <div class="stat-lbl">Units in Stock</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🩸</div>
            <div class="stat-val"><?php echo $types_available; ?></div>
            <div class="stat-lbl">Blood Types Available</div>
        </div>
    </div>

    <!-- ── BLOOD AVAILABILITY SNAPSHOT ── -->
    <div class="section-header">
        <h2>Current Blood Availability</h2>
        <p>Live stock levels — updated automatically as donations arrive and requests are fulfilled.</p>
    </div>
    <div class="stock-grid">
        <?php foreach ($all_groups as $bg => $avail):
            $cls = $avail === 0 ? 'critical' : ($avail < 5 ? 'warn' : 'ok');
            $lbl = $avail === 0 ? 'Critical' : ($avail < 5 ? 'Low' : 'Available');
        ?>
        <div class="stock-card <?php echo $cls; ?>">
            <div class="stock-group"><?php echo $bg; ?></div>
            <div class="stock-units"><?php echo $avail; ?></div>
            <div class="stock-label">Units</div>
            <span class="stag s-<?php echo $cls; ?>"><?php echo $lbl; ?></span>
        </div>
        <?php endforeach; ?>
    </div>
    <p style="text-align:center;margin-top:14px;">
        <a href="blood_availability.php" class="btn btn-primary">View Full Stock Details →</a>
    </p>

    <hr class="section-divider">

    <!-- ── HOW IT WORKS ── -->
    <div class="section-header">
        <h2>How It Works</h2>
        <p>A simple, life-saving process — from donor to patient.</p>
    </div>
    <div class="steps-grid">
        <div class="step-card">
            <div class="step-num">1</div>
            <h3>Donors Register</h3>
            <p>Eligible donors sign up with our staff and have their blood group confirmed.</p>
        </div>
        <div class="step-card">
            <div class="step-num">2</div>
            <h3>Blood is Collected</h3>
            <p>Donations are processed, labelled, and stored safely with an expiry date.</p>
        </div>
        <div class="step-card">
            <div class="step-num">3</div>
            <h3>Hospitals Request</h3>
            <p>Partner hospitals raise requests for specific blood groups on behalf of patients.</p>
        </div>
        <div class="step-card">
            <div class="step-num">4</div>
            <h3>Blood is Dispatched</h3>
            <p>Our team fulfils the request and stock levels are updated in real time.</p>
        </div>
    </div>

    <hr class="section-divider">

    <!-- ── PARTNER HOSPITALS ── -->
    <div class="section-header">
        <h2>Partner Hospitals</h2>
        <p>Hospitals we currently work with under active contracts.</p>
    </div>
    <div class="cards-grid">
        <?php $n = 0; while ($h = mysqli_fetch_assoc($hospitals)): $n++; ?>
        <div class="info-card">
            <h3>🏥 <?php echo htmlspecialchars($h['Name']); ?></h3>
            <div class="meta">
                <span>📍 <?php echo htmlspecialchars($h['City'] . ', ' . $h['State']); ?></span>
                <span>📞 <?php echo htmlspecialchars($h['Contact']); ?></span>
                <?php if ($h['End_Date']): ?>
                <span>📋 Contract until <?php echo $h['End_Date']; ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?php endwhile; if ($n === 0): ?>
        <p style="color:var(--gray-400);">No partner hospitals on record yet.</p>
        <?php endif; ?>
    </div>
    <p style="text-align:center;margin-top:18px;">
        <a href="hospitals.php" class="btn btn-secondary">View All Hospitals →</a>
    </p>
</div>

<!-- ── FOOTER ── -->
<footer>
    <p>🩸 Blood Bank Management System &nbsp;|&nbsp; <a href="contact.php">Contact Us</a> &nbsp;|&nbsp; <a href="../login.php">Staff Login</a></p>
</footer>

</body>
</html>
