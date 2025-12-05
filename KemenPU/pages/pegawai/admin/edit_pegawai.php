<?php
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';


if (!isset($_GET['user'])) {
    header("Location: data_pegawai.php");
    exit;
}

$nip = $_GET['user'];

// Ambil data pegawai
$sql = "SELECT * FROM pegawai WHERE nip = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nip);
$stmt->execute();
$result = $stmt->get_result();
$pegawai = $result->fetch_assoc();

if (!$pegawai) {
    echo "Data pegawai tidak ditemukan.";
    exit;
}

// Proses update
if (isset($_POST['simpan'])) {
    $nama = $_POST['nama_lengkap'];
    $jabatan = $_POST['jabatan'];
    $username = $_POST['username'];

    // upload foto jika ada
$targetDir = $_SERVER['DOCUMENT_ROOT'] . "/kemenPU/uploads/";
$foto = $pegawai['foto_profil'];
if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $allowed = ['jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, $allowed)) {
        // hapus foto lama
        if (!empty($foto) && file_exists($targetDir . $foto)) {
            unlink($targetDir . $foto);
        }
        // nama baru
        $foto = time() . "_" . uniqid() . "." . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], $targetDir . $foto);
    }
}

    $update = "UPDATE pegawai SET nama_lengkap=?, jabatan=?, username=?, foto_profil=? WHERE nip=?";
    $stmt2 = $conn->prepare($update);
    $stmt2->bind_param("sssss", $nama, $jabatan, $username, $foto, $nip);
    $stmt2->execute();

    echo "<script>alert('✅ Data berhasil diperbarui!'); window.location='data_pegawai.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Data Pegawai</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
* {
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
}

body {
    background: linear-gradient(135deg, #eef2f3, #cfd9df);
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    animation: fadeIn 1s ease-in-out;
}

.container {
    width: 100%;
    max-width: 480px;
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    padding: 40px 35px;
    animation: slideUp 0.7s ease;
}

h2 {
    text-align: center;
    margin-bottom: 30px;
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
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 6px;
    font-weight: 500;
    color: #333;
}

input[type="text"],
input[type="file"] {
    width: 100%;
    padding: 10px 14px;
    border: 1.8px solid #ccc;
    border-radius: 10px;
    font-size: 14px;
    transition: 0.3s ease;
    outline: none;
}
input:focus {
    border-color: #0066cc;
    box-shadow: 0 0 6px rgba(0,102,204,0.3);
}

.preview {
    text-align: center;
    margin-bottom: 15px;
}
.preview img {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 50%;
    border: 3px solid #0066cc33;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.btn-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 25px;
}

button, a {
    width: 48%;
    padding: 12px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s ease;
    text-align: center;
    text-decoration: none;
    display: inline-block;
}

button {
    background: #0066cc;
    color: #fff;
    box-shadow: 0 5px 15px rgba(0,102,204,0.3);
}
button:hover {
    background: #004f9e;
    transform: translateY(-2px);
}

a.cancel {
    background: #f1f1f1;
    color: #333;
}
a.cancel:hover {
    background: #ddd;
    transform: translateY(-2px);
}

.username-warning {
    display: none;
    color: #d9534f;
    font-size: 13px;
    margin-top: 5px;
}

@keyframes slideUp {
    from {opacity: 0; transform: translateY(40px);}
    to {opacity: 1; transform: translateY(0);}
}
@keyframes fadeIn {
    from {opacity: 0;}
    to {opacity: 1;}
}
</style>
</head>
<body>
<div class="container">
    <h2>Edit Data Pegawai</h2>
    <form method="post" enctype="multipart/form-data" onsubmit="return confirmUsernameChange()">
        <div class="preview">
            <img src="../uploads/<?= htmlspecialchars($pegawai['foto_profil']) ?>" alt="Foto Pegawai">
        </div>

        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($pegawai['nama_lengkap']) ?>" required>
        </div>

        <div class="form-group">
            <label>Username</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($pegawai['username']) ?>" required>
            <div id="usernameWarning" class="username-warning">⚠️ Kamu akan mengubah username login pegawai ini.</div>
        </div>

        <div class="form-group">
            <label>Jabatan</label>
            <input type="text" name="jabatan" value="<?= htmlspecialchars($pegawai['jabatan']) ?>">
        </div>

        <div class="form-group">
            <label>Ubah Foto Profil</label>
            <input type="file" name="foto" accept="image/*">
        </div>

        <div class="btn-actions">
            <button type="submit" name="simpan">Simpan</button>
            <a href="data_pegawai.php" class="cancel">Batal</a>
        </div>
    </form>
</div>

<script>
// Peringatan ketika user mengubah username
const usernameInput = document.getElementById('username');
const warning = document.getElementById('usernameWarning');
const oldUsername = "<?= htmlspecialchars($pegawai['username']) ?>";

usernameInput.addEventListener('input', () => {
    if (usernameInput.value !== oldUsername) {
        warning.style.display = 'block';
    } else {
        warning.style.display = 'none';
    }
});

// Konfirmasi saat submit
function confirmUsernameChange() {
    if (usernameInput.value !== oldUsername) {
        return confirm("⚠️ Apakah kamu yakin ingin mengubah username pegawai ini? Perubahan ini akan memengaruhi data login mereka.");
    }
    return true;
}
</script>

</body>
</html>
