<?php
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';
session_start();

if (!isset($_GET['id'])) die("Dinas tidak ditemukan.");

$dinas_id = intval($_GET['id']);

$q = mysqli_query($conn, "SELECT * FROM dinas WHERE id=$dinas_id");
$dinas = mysqli_fetch_assoc($q);
if (!$dinas) die("Data dinas tidak ditemukan.");

if (isset($_POST['simpan'])) {
    $jenis = mysqli_real_escape_string($conn, $_POST['jenis']);
    $kebutuhan = mysqli_real_escape_string($conn, $_POST['kebutuhan']);
    $tahun = intval($_POST['tahun']);

    mysqli_query($conn, "
        INSERT INTO identifikasi_pelatihan (dinas_id, jenis_pelatihan, kebutuhan, tahun)
        VALUES ($dinas_id, '$jenis', '$kebutuhan', $tahun)
    ");

    header("Location: detail_dinas.php?id=$dinas_id");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Tambah Pelatihan</title>

<style>
body {
    background: #f4f7ff;
    font-family: Poppins, sans-serif;
}
.form-wrap {
    width: 1200px;
    margin: 150px auto;
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
input, textarea {
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
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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
<div class="notif success">Data berhasil disimpan!</div>
<?php endif; ?>

<div class="form-wrap">
    <h2>Tambah Pelatihan<br> untuk <?= $dinas['nama_dinas'] ?></h2>

    <form method="POST">

        <label>Jenis Pelatihan</label>
        <input type="text" name="jenis" required>

        <label>Kebutuhan</label>
        <textarea name="kebutuhan" rows="4"></textarea>

        <label>Tahun Pelatihan</label>
        <input type="number" name="tahun" placeholder="2025" required>

        <button type="submit" name="simpan">Simpan</button>

    </form>
</div>

</body>
</html>
