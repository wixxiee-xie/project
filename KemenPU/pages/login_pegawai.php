<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';

// Password default pegawai
$default_password = "pegawai123";

if (isset($_POST['login'])) {

    $input = trim($_POST['email']);   // bisa email atau username
    $password = $_POST['password'];

    // ====================================================
    // 🔹 1. CEK LOGIN ADMIN / PENGAJAR (tabel users)
    // ====================================================
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $input);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {

        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['nama_lengkap'] = $admin['nama'];
        $_SESSION['email'] = $admin['email'];
        $_SESSION['role'] = $admin['role'];

        // Routing berdasarkan role
        if ($admin['role'] === 'admin') {
            header("Location: " . BASE_URL . "pages/pegawai/admin/db_pegawai.php");
            exit;
        }

        elseif ($admin['role'] === 'pengajar') {
            header("Location: " . BASE_URL . "pages/pengajar/dashboard.php");
            exit;
        }

        elseif ($admin['role'] === 'admin_mitigapro') {
            header("Location: " . BASE_URL . "pages/mitigapro/admin/db_mitigapro.php");
            exit;
        }

        // fallback jika role tidak dikenal
        header("Location: " . BASE_URL . "pages/pegawai/admin/db_pegawai.php");
        exit;
    }

    // ====================================================
    // 🔹 2. CEK LOGIN PEGAWAI (tabel pegawai)
    // ====================================================
    $stmt2 = $conn->prepare("SELECT * FROM pegawai WHERE username = ?");
    $stmt2->bind_param("s", $input);
    $stmt2->execute();
    $pegawai = $stmt2->get_result()->fetch_assoc();

    if ($pegawai) {

        // Password default (tidak di-hash)
        if ($password === $default_password) {

            $_SESSION['pegawai_id']  = $pegawai['id'];
            $_SESSION['nama_lengkap'] = $pegawai['nama_lengkap'];
            $_SESSION['username']    = $pegawai['username'];
            $_SESSION['nip']         = $pegawai['nip'];
            $_SESSION['role']        = 'pegawai';

            header("Location: " . BASE_URL . "pages/pegawai/karyawan/db_karyawan.php");
            exit;
        }

        // Password hashed (pegawai sudah update password)
        if (password_verify($password, $pegawai['password'])) {

            $_SESSION['pegawai_id']  = $pegawai['id'];
            $_SESSION['nama_lengkap'] = $pegawai['nama_lengkap'];
            $_SESSION['username']    = $pegawai['username'];
            $_SESSION['nip']         = $pegawai['nip'];
            $_SESSION['role']        = 'pegawai';

            header("Location: " . BASE_URL . "pages/pegawai/karyawan/db_karyawan.php");
            exit;
        }

        echo "<script>alert('Password salah!');</script>";
    }

    // ====================================================
    // Jika semua gagal
    // ====================================================
    else {
        echo "<script>alert('Email / Username tidak ditemukan!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="<?= BASE_URL ?>1_css/login.css">
<title>Login Sistem Data Pengajar</title>
</head>
<body>

<div class="login-container">
    <h2>Login</h2>
    <form method="POST">
        <input type="text" name="email" placeholder="Masukkan Email / Username" required><br>
        <input type="password" name="password" placeholder="Masukkan Password" required><br>
        <button type="submit" name="login">Login</button>
    </form>
</div>

</body>
</html>
