<?php
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';

// Mulai session (hanya jika belum dimulai)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hapus semua data session
session_unset();
session_destroy();

// Redirect ke halaman login
header("Location: " . BASE_URL . "pages/login_pegawai.php");
exit;
?>
