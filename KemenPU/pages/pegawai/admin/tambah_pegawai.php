<?php
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = $_POST['nama_lengkap'];
    $nip = $_POST['nip'];
    $jabatan = $_POST['jabatan'];
    $foto = $_FILES['foto_profil']['name'];

    // Upload foto
    $targetDir = "../uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if (!empty($foto)) {
        $targetFile = $targetDir . basename($foto);
        move_uploaded_file($_FILES['foto_profil']['tmp_name'], $targetFile);
    } else {
        $foto = 'default.jpg';
    }

    // Generate username dan password default
    $username = strtolower(str_replace(' ', '', $nama_lengkap)) . "01@pu.go.id";
    $password = password_hash('pegawai123', PASSWORD_DEFAULT);

    // Simpan ke database (hapus kolom golongan karena tidak ada di tabel)
    $sql = "INSERT INTO pegawai (nama_lengkap, nip, jabatan, foto_profil, username, password)
            VALUES ('$nama_lengkap', '$nip', '$jabatan', '$foto', '$username', '$password')";

    if ($conn->query($sql)) {
        echo "<script>alert('✅ Pegawai berhasil ditambahkan!'); window.location='data_pegawai.php';</script>";
    } else {
        echo "<script>alert('❌ Gagal menambahkan pegawai: " . $conn->error . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Pegawai</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>1_css/tambah_pegawai.css">
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        background: linear-gradient(135deg, #eef2f3, #cfd9df);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: fadeIn 1s ease-in-out;
    }

    .container {
    width: 100%;
    padding: 30px;
    width: 100%;
    margin: 0;
    padding: 20px;
    max-width: 1500px;
    margin: 50px auto;
    background: white;
    border-radius: 16px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    padding: 40px 50px;
    animation: fadeIn 0.6s ease;
    }

    .card {
        background: #fff;
        border-radius: 20px;
        padding: 40px 35px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        animation: slideUp 0.6s ease;
    }

    h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #333;
        font-weight: 600;
        position: relative;
    }

    h2::after {
        content: '';
        width: 60px;
        height: 3px;
        background: #0066cc;
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        bottom: -8px;
        border-radius: 2px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    label {
        display: block;
        margin-bottom: 6px;
        color: #333;
        font-weight: 500;
    }

    input[type="text"],
    input[type="file"],
    select {
        width: 100%;
        padding: 10px 14px;
        border: 1.8px solid #ccc;
        border-radius: 10px;
        font-size: 14px;
        outline: none;
        transition: all 0.3s ease;
    }

    input:focus,
    select:focus {
        border-color: #0066cc;
        box-shadow: 0 0 6px rgba(0,102,204,0.3);
    }

    .info {
        background: #f8faff;
        border-left: 4px solid #0066cc;
        padding: 10px 15px;
        border-radius: 10px;
        margin: 15px 0 20px;
        font-size: 13px;
        color: #333;
    }

    .info strong {
        color: #0066cc;
    }

    .form-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 25px;
    }

    .btn-submit, .btn-cancel {
        width: 48%;
        padding: 12px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        transition: 0.3s ease;
    }

    .btn-submit {
        background: #0066cc;
        color: #fff;
        box-shadow: 0 5px 15px rgba(0,102,204,0.3);
    }

    .btn-submit:hover {
        background: #0051a1;
        transform: translateY(-2px);
    }

    .btn-cancel {
        background: #f1f1f1;
        color: #333;
    }

    .btn-cancel:hover {
        background: #ddd;
        transform: translateY(-2px);
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

</style>
</head>
<body>
<div class="container">
    <div class="card">
        <h2>Tambah Data Pegawai</h2>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" placeholder="Masukkan Nama Lengkap..." required>
            </div>

            <div class="form-group">
                <label>NIP</label>
                <input 
                    type="text" 
                    name="nip" 
                    maxlength="18" 
                    pattern="[0-9]{1,18}" 
                    placeholder="Maksimal 18 digit angka"
                    title="Maksimal 18 digit angka" 
                    required>
            </div>

            <div class="form-group">
                <label>Jabatan</label>
                <input type="text" name="jabatan" placeholder="Masukkan Jabatan...">
            </div>

            <div class="form-group">
                <label>Foto Profil</label>
                <input type="file" name="foto_profil" accept="image/*">
            </div>

            <div class="info">
                <p>Email akan dibuat otomatis berdasarkan <strong>Nama Lengkap</strong>.</p>
                <p>Password default: <strong>pegawai123</strong></p>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Simpan</button>
                <a href="data_pegawai.php" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
