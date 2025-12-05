<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';

// Pastikan BASE_URL selalu memiliki trailing slash
$BASE = rtrim(BASE_URL, '/') . '/';

// Cek login
if (!isset($_SESSION['nip'])) {
    header("Location: " . $BASE . "login.php");
    exit;
}

$nip = $_SESSION['nip'];

// Validasi request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . $BASE . "pages/pegawai/karyawan/pengaduan_karyawan.php");
    exit;
}

$jenis_laporan = $_POST['jenis_laporan'] ?? '';
$jenis_laporan_custom = $_POST['jenis_laporan_custom'] ?? null;
$tanggal_kejadian = $_POST['tanggal_kejadian'] ?? null;
$keterangan = $_POST['keterangan'] ?? null;

// VALIDASI DASAR
if (!$jenis_laporan || !$tanggal_kejadian) {
    $_SESSION['err_pengaduan'] = "Jenis laporan dan tanggal kejadian wajib diisi.";
    header("Location: " . $BASE . "pages/pegawai/karyawan/pengaduan_karyawan.php");
    exit;
}

if ($jenis_laporan === "Lainnya" && !$jenis_laporan_custom) {
    $_SESSION['err_pengaduan'] = "Mohon isi jenis laporan untuk opsi 'Lainnya'.";
    header("Location: " . $BASE . "pages/pegawai/karyawan/pengaduan_karyawan.php");
    exit;
}

// ---- FOLDER UPLOAD ----
$uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/kemenPU/uploads/pengaduan/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// ---- HANDLE FILE UPLOAD ----
$uploadedFileName = null;

if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] !== UPLOAD_ERR_NO_FILE) {

    $file = $_FILES['bukti'];

    // Error upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['err_pengaduan'] = "Gagal mengunggah file.";
        header("Location: " . $BASE . "pages/pegawai/karyawan/pengaduan_karyawan.php");
        exit;
    }

    // Batas ukuran 2MB
    if ($file['size'] > 2 * 1024 * 1024) {
        $_SESSION['err_pengaduan'] = "Ukuran file maksimal adalah 2MB.";
        header("Location: " . $BASE . "pages/pegawai/karyawan/pengaduan_karyawan.php");
        exit;
    }

    // MIME CHECK
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedMime = ['image/jpeg', 'image/jpg', 'image/png'];

    if (!in_array($mime, $allowedMime)) {
        $_SESSION['err_pengaduan'] = "Format file tidak didukung. Harap upload foto (JPG/PNG).";
        header("Location: " . $BASE . "pages/pegawai/karyawan/pengaduan_karyawan.php");
        exit;
    }

    // Ekstensi berdasarkan mime
    $ext = $mime === 'image/png' ? '.png' : '.jpg';

    // Nama file aman dan unik
    $safeNip = preg_replace('/[^A-Za-z0-9\-]/', '', $nip);
    $newName = time() . '_' . $safeNip . '_' . bin2hex(random_bytes(5)) . $ext;

    $path = $uploadDir . $newName;

    if (!move_uploaded_file($file['tmp_name'], $path)) {
        $_SESSION['err_pengaduan'] = "Gagal menyimpan file upload.";
        header("Location: " . $BASE . "pages/pegawai/karyawan/pengaduan_karyawan.php");
        exit;
    }

    $uploadedFileName = $newName;
}

// ---- SIMPAN DATABASE ----
$sql = "INSERT INTO pengaduan 
        (nip, jenis_laporan, jenis_laporan_custom, tanggal_kejadian, keterangan, bukti, status) 
        VALUES (?, ?, ?, ?, ?, ?, 'pending')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $nip, $jenis_laporan, $jenis_laporan_custom, $tanggal_kejadian, $keterangan, $uploadedFileName);

if ($stmt->execute()) {
    $_SESSION['success_pengaduan'] = "Pengaduan berhasil dikirim dan berstatus Pending.";
} else {
    $_SESSION['err_pengaduan'] = "Terjadi kesalahan saat menyimpan pengaduan.";
    if ($uploadedFileName) {
        @unlink($uploadDir . $uploadedFileName);
    }
}

$stmt->close();

// Redirect aman & TIDAK PERNAH ke halaman kosong
header("Location: " . $BASE . "pages/pegawai/karyawan/pengaduan_karyawan.php");
exit;
