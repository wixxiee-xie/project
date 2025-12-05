<?php
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';
session_start();

// Jika ingin batasi akses → uncomment ini
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

// Ambil daftar wilayah untuk dropdown
$q_wilayah = mysqli_query($conn, "SELECT * FROM wilayah ORDER BY nama_wilayah ASC");

if (isset($_POST['simpan'])) {
    $wilayah_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $nama_dinas = mysqli_real_escape_string($conn, $_POST['nama_dinas']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $kontak = mysqli_real_escape_string($conn, $_POST['kontak']);

    mysqli_query($conn, "
        INSERT INTO dinas (wilayah_id, nama_dinas, alamat, kontak)
        VALUES ($wilayah_id, '$nama_dinas', '$alamat', '$kontak')
    ");

    header("Location: tambah_dinas.php?success=1");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Tambah Dinas</title>

<style>
body {
    background: #f4f7ff;
    font-family: Poppins, sans-serif;
}
.form-wrap {
    width: 1200px;
    margin: 120px auto;
    background: #fff;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}
h2 {
    margin: 0 0 15px;
    color: #1e3c72;
}
label {
    font-weight: 600;
    color: #334155;
}
input, textarea, select {
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    border: 1px solid #cbd5e1;
    margin-top: 8px;
    margin-bottom: 18px;
    font-size: 15px;
}
button {
    width: 100%;
    background: #1e3c72;
    color: white;
    padding: 12px;
    border-radius: 10px;
    font-size: 16px;
    border: none;
    cursor: pointer;
    transition: 0.3s;
}
button:hover {
    background: #2a5298;
}

.notif {
    position: fixed;
    top: 25px;
    right: 25px;
    padding: 16px 22px;
    background: #10b981;
    color: white;
    border-radius: 12px;
    font-weight: 600;
    animation: fadeInOut 3s ease forwards;
    z-index: 9999;
}

@keyframes fadeInOut {
    0% { opacity: 0; transform: translateY(-10px); }
    10% { opacity: 1; transform: translateY(0); }
    80% { opacity: 1; }
    100% { opacity: 0; transform: translateY(-10px); }
}
</style>
</head>

<body>

<?php if (isset($_GET['success'])): ?>
<div class="notif">Dinas berhasil ditambahkan!</div>
<?php endif; ?>

<div class="form-wrap">
    <h2>Tambah Dinas</h2>

    <form method="POST">

        <label>Wilayah</label>
        <select name="wilayah_id" required>
            <option value="" disabled selected>-- Pilih Wilayah --</option>
            <?php while ($w = mysqli_fetch_assoc($q_wilayah)): ?>
                <option value="<?= $w['id'] ?>">
                    <?= $w['nama_wilayah'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Nama Dinas</label>
        <input type="text" name="nama_dinas" required>

        <label>Alamat</label>
        <textarea name="alamat" rows="3"></textarea>

        <label>Kontak</label>
        <input type="text" name="kontak" placeholder="Email / Telepon">

        <button type="submit" name="simpan">Simpan</button>

    </form>
</div>

</body>
</html>
