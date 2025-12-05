<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Topbar -->
<div class="topbar">
  <h1>DATA PENGAJAR PELATIHAN</h1>
  <nav>
    <a href="<?= BASE_URL ?>pages/pengajar/dashboard.php" 
           class="<?= basename($_SERVER['PHP_SELF'])=='dashboard.php' ? 'active' : '' ?>">
           <span>DASHBOARD</span>
        </a>

        <a href="<?= BASE_URL ?>pages/pengajar/pengajar.php" 
           class="<?= basename($_SERVER['PHP_SELF'])=='pengajar.php' ? 'active' : '' ?>">
           <span>DATA PENGAJAR</span>
        </a>

        <a href="<?= BASE_URL ?>pages/logout.php" 
           class="<?= basename($_SERVER['PHP_SELF'])=='logout.php' ? 'active' : '' ?>">
           <span>LOGOUT</span>
        </a>

  </nav>
</div>


