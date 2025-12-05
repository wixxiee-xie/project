<?php
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';

session_start();

// pastikan user login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];

// cek NIP
if (!isset($_GET['nip'])) {
    header("Location: pengajar.php");
    exit;
}

$nip = $_GET['nip'];

// ambil data pengajar
$stmt = $conn->prepare("SELECT * FROM pengajar WHERE nip = ?");
$stmt->bind_param("s", $nip);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "<h2>Data tidak ditemukan.</h2>";
    exit;
}

// menentukan foto
$foto = (!empty($data['foto'])) ? $data['foto'] : 'default.jpg';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengajar | KemenPU DataSystem</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>1_css/pengajar_view.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>1_css/topbar.css">
</head>
<body>

<div class="profile-container">
    <div class="profile-header">
        <div class="photo-section">
            <img 
                src="<?= BASE_URL ?>uploads/pengajar/<?= $foto ?>" 
                alt="Foto Pengajar <?= htmlspecialchars($data['nama_pengajar']); ?>">
        </div>


        <div class="info-section">
            <h2><?php echo htmlspecialchars($data['nama_pengajar']); ?></h2>
            <p><?php echo htmlspecialchars($data['jabatan']); ?> — <?php echo htmlspecialchars($data['unit_kerja']); ?></p>
            <span class="status <?php echo $data['status']; ?>">
                <?php echo ucfirst($data['status']); ?>
            </span>
        </div>
    </div>

    <div class="profile-content">
        <!-- Data Pribadi -->
        <div class="card">
            <h3>Data Pribadi</h3>
            <div class="grid">
                <p><strong>NIP:</strong> <?php echo $data['nip']; ?></p>
                <p><strong>Jenis Kelamin:</strong> <?php echo $data['jenis_kelamin']; ?></p>
                <p><strong>Tempat, Tanggal Lahir:</strong> <?php echo $data['tempat_lahir'] . ', ' . date('d M Y', strtotime($data['tanggal_lahir'])); ?></p>
                <p><strong>Agama:</strong> <?php echo $data['agama']; ?></p>
                <p><strong>Pendidikan:</strong> <?php echo $data['pendidikan_terakhir']; ?></p>
                <p><strong>Golongan:</strong> <?php echo $data['golongan']; ?></p>
            </div>
        </div>

        <!-- Data Pekerjaan -->
        <div class="card">
            <h3>Data Pekerjaan</h3>
            <div class="grid">
                <p><strong>Instansi:</strong> <?php echo $data['instansi']; ?></p>
                <p><strong>Alamat Kantor:</strong> <?php echo $data['alamat_kantor']; ?></p>
                <p><strong>Email:</strong> <?php echo $data['email_pengajar']; ?></p>
                <p><strong>No. HP:</strong> <?php echo $data['no_hp']; ?></p>
            </div>
        </div>

        <!-- Data Rekening -->
        <div class="card">
            <h3>Data Lain-Lain</h3>
            <div class="grid">
                <p><strong>Nomor NPWP:</strong> <?php echo $data['npwp']; ?></p>
            </div>
        </div>

        <div class="button-group">
            <?php if ($role === 'pengajar'): ?>
                <a href="pengajar_edit.php?nip=<?= $data['nip']; ?>" class="btn-primary">Edit</a>
            <?php endif; ?>
            <a href="pengajar.php" class="btn-secondary">Kembali</a>
        </div>
    </div>

    <!-- Form Download PDF -->
    <div class="download-section">
        <h3>Atur Informasi Pelatihan</h3>
        <form id="pdfForm" method="GET" action="download_pdf.php">
            <input type="hidden" name="nip" value="<?= $data['nip']; ?>">

            <div class="form-group">
                <label>Nama Pelatihan</label>
                <input type="text" name="nama_lengkap_pelatihan" placeholder="Contoh: PELATIHAN PERENCANAAN TEKNIS IRIGASI" required>
            </div>

            <div class="form-group">
                <label>Tanggal Pelatihan</label>
                <input type="text" name="tanggal" placeholder="Contoh: MAKASSAR, 29 JULI s.d 03 SEPTEMBER 2025" required>
            </div>

            <div class="form-group wide">
                <label>Instansi Penyelenggara</label>
                <input type="text" name="instansi" placeholder="Contoh: BALAI PENGEMBANGAN KOMPETENSI PU WILAYAH VIII MAKASSAR" required>
            </div>
            
            <button class="submit" style="float: right;">Download PDF</button>
        </form>
    </div>
</div>  
</body>
</html>
