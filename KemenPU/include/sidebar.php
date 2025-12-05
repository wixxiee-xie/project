<div class="sidebar" id="sidebar">
  <button class="sidebar-toggle" id="toggleBtn" onclick="toggleSidebar()">&lt;</button>
  <div>
    <h2>KinPro</h2>
    <ul>
      <li>
        <a href="<?= BASE_URL ?>pages/pegawai/admin/db_pegawai.php" 
           class="<?= basename($_SERVER['PHP_SELF'])=='db_pegawai.php' ? 'active' : '' ?>">
           <span>Dashboard</span>
        </a>
      </li>
  
      <li>
        <a href="<?= BASE_URL ?>pages/pegawai/admin/data_pegawai.php" 
           class="<?= basename($_SERVER['PHP_SELF'])=='data_pegawai.php' ? 'active' : '' ?>">
           <span>Data Pegawai</span>
        </a>
      </li>

      <li>
        <a href="<?= BASE_URL ?>pages/pegawai/admin/tambah_penilaian.php" 
           class="<?= basename($_SERVER['PHP_SELF'])=='tambah_penilaian.php' ? 'active' : '' ?>">
           <span>Tambah Penilaian</span>
        </a>
      </li>

      <li>
        <a href="<?= BASE_URL ?>pages/pegawai/admin/aduan_karyawan.php" 
           class="<?= basename($_SERVER['PHP_SELF'])=='aduan_karyawan.php' ? 'active' : '' ?>">
           <span>Aduan Karyawan</span>
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
