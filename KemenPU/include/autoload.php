<?php
// Root project folder
define('ROOT_PATH', rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/kemenPU/');

// Base URL untuk akses dari web
define('BASE_URL', '/kemenPU/');

// Path folder utama
define('INCLUDE_PATH', ROOT_PATH . 'include/');
define('PAGES_PATH', ROOT_PATH . 'pages/');
define('UPLOADS_PATH', ROOT_PATH . 'uploads/');

// Sub-folder pages yang sering dipakai
define('PEGAWAI_PATH', PAGES_PATH . 'pegawai/');
define('PENGAJAR_PATH', PAGES_PATH . 'pengajar/');
define('MITIGAPRO_ADMIN_PATH', PAGES_PATH . 'mitigapro/admin/');
define('MITIGAPRO_USER_PATH', PAGES_PATH . 'mitigapro/user/');

// Auto-load database
require_once INCLUDE_PATH . 'db.php';

// Auto-load fungsi umum jika ada
if (file_exists(INCLUDE_PATH . 'functions.php')) {
    require_once INCLUDE_PATH . 'functions.php';
}

?>
