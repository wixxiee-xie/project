<?php
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';
require INCLUDE_PATH . 'topbar_pengajar.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Wilayah tidak ditemukan.");
}

$wilayah_id = intval($_GET['id']);

// Ambil data wilayah
$q_wilayah = mysqli_query($conn, "SELECT * FROM wilayah WHERE id='$wilayah_id'");
$wilayah = mysqli_fetch_assoc($q_wilayah);
if (!$wilayah) die("Data wilayah tidak ditemukan.");

// Ambil daftar dinas
$q_dinas = mysqli_query($conn, "SELECT * FROM dinas WHERE wilayah_id='$wilayah_id'");

// Ambil identifikasi pelatihan
$q_pelatihan = mysqli_query($conn, "
    SELECT p.*, d.nama_dinas 
    FROM identifikasi_pelatihan p
    INNER JOIN dinas d ON p.dinas_id = d.id
    WHERE d.wilayah_id='$wilayah_id'
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dinas Kerja | <?= $wilayah['nama_wilayah'] ?></title>
<link rel="stylesheet" href="<?= BASE_URL ?>1_css/topbar.css">
<link rel="stylesheet" href="<?= BASE_URL ?>1_css/footer.css">
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<style>
body {
    background: linear-gradient(135deg, #eef4ff, #f8fbff);
    font-family: 'Poppins', sans-serif;
    margin: 0;
    color: #2d3e50;
}

/* Container */
.page-container {
    padding: 40px 60px;
}

/* Header Wilayah */
.header-title {
    text-align: center;
    margin-bottom: 40px;
}
.header-title h2 {
    font-size: 2rem;
    font-weight: 700;
    color: #1e3c72;
}
.header-title p {
    color: #555;
    margin-top: 8px;
}

/* Card wilayah */
.info-card {
    background: #ffffff;
    border-radius: 18px;
    padding: 25px 30px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    margin-bottom: 40px;
    animation: fadeIn 0.5s ease-in-out;
}
.info-card h3 {
    color: #1e3c72;
    margin: 0;
    font-weight: 700;
}
.info-card p {
    margin: 6px 0 0;
    color: #444;
}

/* Tabel */
.table-box {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    padding: 25px;
    margin-bottom: 40px;
}

.table-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}
.table-title h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    color: #1e3c72;
}

.add-btn {
    background: #1e3c72;
    padding: 8px 15px;
    color: #fff;
    border-radius: 8px;
    font-size: 14px;
    text-decoration: none;
    transition: 0.3s;
}
.add-btn:hover { background: #284c9f; }

/* Table Style */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}
th {
    background: #e8eefc;
    padding: 12px;
    text-align: left;
    font-size: 14px;
    color: #1e3c72;
}
td {
    padding: 12px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
    color: #444;
}

.action-btn {
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 12px;
    color: #fff;
    text-decoration: none;
}
.edit { background: #f1c40f; }
.delete { background: #e74c3c; }

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

</head>
<body>

<div class="page-container">

    <div class="header-title">
        <h2><?= $wilayah['nama_wilayah'] ?></h2>
        <p>Daftar dinas & hasil identifikasi pelatihan untuk wilayah ini.</p>
    </div>

    <div class="info-card">
        <h3><i class="fas fa-map-marked-alt"></i> Informasi Wilayah</h3>
        <p>Wilayah ini merupakan bagian dari Balai Pengembangan Kompetensi PU Wilayah VIII Makassar.</p>
    </div>

    <!-- DINAS -->
    <div class="table-box">
        <div class="table-title">
            <h3>Daftar Dinas</h3>
            <a href="tambah_dinas.php?id=<?= $wilayah_id ?>" class="add-btn"></i> Tambah Dinas</a>
        </div>

        <table>
            <tr>
                <th>No</th>
                <th>Nama Dinas</th>
                <th>Alamat</th>
                <th>Kontak</th>
                <th>Aksi</th>
            </tr>

    <?php
    $no = 1;
    while ($d = mysqli_fetch_assoc($q_dinas)) {
        echo "
        <tr>
            <td>$no</td>
            <td>
                <a href='detail_dinas.php?id={$d['id']}' style='color:#1e3c72; font-weight:600; text-decoration:none;'>
                    {$d['nama_dinas']}
                </a>
            </td>
            <td>{$d['alamat']}</td>
            <td>{$d['kontak']}</td>
            <td>
                <a href='edit_dinas.php?id={$d['id']}' class='action-btn edit'>Edit</a>
                <a href='hapus_dinas.php?id={$d['id']}' class='action-btn delete' onclick=\"return confirm('Hapus dinas ini?');\">Hapus</a>
            </td>
        </tr>
        ";
        $no++;
    }
    ?>

</div>

</body>
</html>
