<?php
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['user'])) {
    header("Location: data_pegawai.php");
    exit;
}

$nip = $_GET['user'];

// 1️⃣ Hapus data penilaian pegawai terlebih dahulu
$stmt1 = $conn->prepare("DELETE FROM penilaian WHERE nip=?");
$stmt1->bind_param("s", $nip);
$stmt1->execute();

// 2️⃣ Hapus data pegawai
$stmt2 = $conn->prepare("DELETE FROM pegawai WHERE nip=?");
$stmt2->bind_param("s", $nip);
$stmt2->execute();

if($stmt2->affected_rows > 0){
    // Berhasil dihapus
    header("Location: data_pegawai.php?msg=hapus_sukses");
} else {
    // Gagal hapus (misal nip tidak ada)
    header("Location: data_pegawai.php?msg=hapus_gagal");
}
exit;
