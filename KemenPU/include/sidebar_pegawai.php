<div class="sidebar" id="sidebar">
  <button class="sidebar-toggle" id="toggleBtn" onclick="toggleSidebar()">&lt;</button>
  <div>
    <h2>KinPro</h2>
    <ul>
      <li>
        <a href="<?= BASE_URL ?>pages/pegawai/karyawan/db_karyawan.php" 
           class="<?= basename($_SERVER['PHP_SELF'])=='db_karyawan.php' ? 'active' : '' ?>">
           <span>Dashboard</span>
        </a>
      </li>

      <li>
        <a href="<?= BASE_URL ?>pages/pegawai/karyawan/profil_karyawan.php" 
           class="<?= basename($_SERVER['PHP_SELF'])=='profil_karyawan.php' ? 'active' : '' ?>">
           <span>Profil Pegawai</span>
        </a>
      </li>

      <li>
        <a href="<?= BASE_URL ?>pages/pegawai/karyawan/laporan_karyawan.php" 
           class="<?= basename($_SERVER['PHP_SELF'])=='laporan_karyawan.php' ? 'active' : '' ?>">
           <span>Laporan Pegawai</span>
        </a>
      </li>

      <li>
        <a href="<?= BASE_URL ?>pages/pegawai/karyawan/pengaduan_karyawan.php" 
           class="<?= basename($_SERVER['PHP_SELF'])=='pengaduan_karyawan.php' ? 'active' : '' ?>">
           <span>Pengaduan</span>
        </a>
      </li>

      <li>
        <a href="<?= BASE_URL ?>pages/logout.php" 
           class="<?= basename($_SERVER['PHP_SELF'])=='logout.php' ? 'active' : '' ?>">
           <span>Logout</span>
        </a>
      </li>
    </ul>
  </div>
</div>
