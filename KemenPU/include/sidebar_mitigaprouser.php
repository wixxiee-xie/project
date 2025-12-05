<div class="sidebar" id="sidebar">
  <button class="sidebar-toggle" id="toggleBtn" onclick="toggleSidebar()">&lt;</button>
  <div>
    <h2>MITIGAPRO</h2>
    <ul>
      <li>
        <a href="all_user.php?menu=dashboard" 
           class="<?= ($_GET['menu'] ?? '') == 'dashboard' ? 'active' : '' ?>">
           <span>Dashboard</span>
        </a>
      </li>

      <li>
        <a href="all_user.php?menu=belanja_modal" 
           class="<?= ($_GET['menu'] ?? '') == 'belanja_modal' ? 'active' : '' ?>">
           <span>Pengendali Risiko Belanja Modal</span>
        </a>
      </li>

      <li>
        <a href="all_user.php?menu=bmn" 
           class="<?= ($_GET['menu'] ?? '') == 'bmn' ? 'active' : '' ?>">
           <span>Pengendali Risiko BMN</span>
        </a>
      </li>

      <li>
        <a href="all_user.php?menu=pelatihan" 
           class="<?= ($_GET['menu'] ?? '') == 'pelatihan' ? 'active' : '' ?>">
           <span>Pengendali Risiko Pelatihan</span>
        </a>
      </li>

      <li>
        <a href="all_user.php?menu=skp" 
           class="<?= ($_GET['menu'] ?? '') == 'skp' ? 'active' : '' ?>">
           <span>Pengendali Risiko SKP</span>
        </a>
      </li>

      <li>
        <a href="all_user.php?menu=tukin" 
           class="<?= ($_GET['menu'] ?? '') == 'tukin' ? 'active' : '' ?>">
           <span>Pengendali Risiko Tukin</span>
        </a>
      </li>

      <li>
        <a href="<?= BASE_URL ?>pages/logout.php">
          <span>Logout</span>
        </a>
      </li>
    </ul>
  </div>
</div>
