<?php
$cur = basename($_SERVER['PHP_SELF']);
function vl($file,$label,$cur){
    $a = ($cur===$file)?' style="background:rgba(255,255,255,.22);color:#fff;"':'';
    return "<a href=\"{$file}\"{$a}>{$label}</a>";
}
?>
<nav class="navbar">
    <div class="navbar-brand">
        <span class="nb-icon">🩸</span>
        <span>Blood Bank</span>
    </div>
    <div class="navbar-links">
        <?= vl('index.php',            'Home',              $cur) ?>
        <?= vl('blood_availability.php','Blood Availability',$cur) ?>
        <?= vl('donors.php',           'Donors',            $cur) ?>
        <?= vl('hospitals.php',        'Hospitals',         $cur) ?>
        <?= vl('contact.php',          'Contact',           $cur) ?>
    </div>
    <div class="navbar-user">
        <a href="../login.php" class="btn-login">Login</a>
    </div>
</nav>
