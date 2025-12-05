<?php
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("ID pelatihan tidak ditemukan.");
}

$id = intval($_GET['id']);

// Ambil data pelatihan
$q = mysqli_query($conn, "SELECT * FROM dinas WHERE id = $id");
$data = mysqli_fetch_assoc($q);

if (!$data) die("Data dinas tidak ditemukan.");

if (isset($_POST['simpan'])) {
    $dinas = mysqli_real_escape_string($conn, $_POST['nama_dinas']);

    mysqli_query($conn, "
        UPDATE dinas 
        SET nama_dinas='$dinas'
        WHERE id=$id
    ");

    header("Location: wilayah.php?id=" . $data['id']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Pelatihan</title>

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f1f5fb;
    padding: 40px;
}

.card {
    background: #fff;
    padding: 25px;
    border-radius: 14px;
    max-width: 550px;
    margin: auto;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}

label {
    font-size: 14px;
    font-weight: 600;
    color: #1e3c72;
}

input[type=text] {
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    border: 1px solid #cfd7e3;
    margin-top: 8px;
    font-size: 15px;
}

button {
    background: #1e3c72;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 10px;
    margin-top: 20px;
    cursor: pointer;
    font-weight: 600;
}
button:hover {
    background: #2b57a5;
}
</style>
</head>

<body>

<div class="card">
    <h2>Edit Pelatihan</h2>

    <form method="POST">
        <label>Nama Pelatihan</label>
        <input type="text" name="nama_dinas" value="<?= $data['nama_dinas'] ?>" required>

        <button type="submit" name="simpan">Simpan</button>
    </form>
</div>

</body>
</html>
