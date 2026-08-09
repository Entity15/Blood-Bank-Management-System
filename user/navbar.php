<?php
require_once 'config.php';
$u   = current_user();
$cur = basename($_SERVER['PHP_SELF']);
function nl($file, $label, $cur) {
    $a = ($cur === $file) ? ' active' : '';
    return "<a href=\"{$file}\" class=\"nav-link{$a}\">{$label}</a>";
}
?>
<nav class="navbar">
    <a href="dashboard.php" class="navbar-brand">
        <span class="brand-icon">🩸</span>
        <span>LifeBank <span style="font-weight:300;opacity:.7;font-size:.85em;">User Portal</span></span>
    </a>
    <div class="navbar-links">
        <?= nl('dashboard.php',   'Dashboard',   $cur) ?>
        <?= nl('blood_stock.php', 'Blood Stock',  $cur) ?>
        <?= nl('donations.php',   'Donations',    $cur) ?>
        <?= nl('donors.php',      'Donors',       $cur) ?>
        <?= nl('hospitals.php',   'Hospitals',    $cur) ?>
        <?= nl('my_requests.php', 'My Requests',  $cur) ?>
        <?= nl('profile.php',     'Profile',      $cur) ?>
    </div>
    <div class="navbar-user">
        <div class="user-chip">
            <div class="ava"><?= strtoupper(substr($u['name'],0,1)) ?></div>
            <?= htmlspecialchars($u['name']) ?>
            <?php if ($u['blood_group']): ?>
                &nbsp;<span style="opacity:.65;font-size:.78em;"><?= $u['blood_group'] ?></span>
            <?php endif; ?>
        </div>
        <a href="logout.php" class="btn-logout">Logout</a>
    </div>
    <button type="button" class="navbar-toggle" aria-label="Toggle menu" aria-expanded="false" onclick="document.querySelector('.navbar').classList.toggle('nav-open'); this.setAttribute('aria-expanded', document.querySelector('.navbar').classList.contains('nav-open'));">
        <span></span><span></span><span></span>
    </button>
</nav>
<div class="navbar-scrim" onclick="document.querySelector('.navbar').classList.remove('nav-open'); document.querySelector('.navbar-toggle').setAttribute('aria-expanded','false');"></div>
