<?php
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';
require INCLUDE_PATH . 'sidebar.php';

session_start();

// Cegah akses tanpa login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil NIP dari parameter URL
if (!isset($_GET['nip'])) {
    header("Location: tambah_penilaian.php");
    exit;
}

$nip = $_GET['nip'];

// Ambil data pegawai
$sqlPegawai = "SELECT nama_lengkap, nip FROM pegawai WHERE nip = '$nip'";
$resultPegawai = $conn->query($sqlPegawai);
if ($resultPegawai->num_rows == 0) {
    die("Pegawai tidak ditemukan.");
}
$pegawai = $resultPegawai->fetch_assoc();

// Ambil data penilaian per bulan
$sqlPenilaian = "
    SELECT 
        id_penilaian,
        bulan,
        tahun,
        jumlah_hari_efektif,
        jumlah_hari_kerja,
        lupa_absen,
        nilai_kedisiplinan,
        kinerja,
        kepemimpinan,
        loyalitas,
        it,
        rata_rata
    FROM penilaian
    WHERE nip = '$nip'
    ORDER BY tahun DESC, STR_TO_DATE(bulan, '%M') DESC
";
$resultPenilaian = $conn->query($sqlPenilaian);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Detail Penilaian Pegawai</title>
<link rel="stylesheet" href="<?= BASE_URL ?>1_css/sidebar.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #f3e7ff, #e6f0ff);
  margin: 0;
  padding: 0;
}

.main-content {
  margin-left: 240px;
  padding: 35px;
transition: margin-left 0.3s ease;
  opacity: 0;                 
  transform: translateY(50px); /* start lebih jauh dari bawah */
  animation: fadeSlideIn 1s ease forwards; /* durasi lebih panjang */
}

@keyframes fadeSlideIn {
  to {
    opacity: 1;
    transform: translateY(0); /* posisi normal */
  }
}

.content-wrapper {
  background: white;
  border-radius: 18px;
  padding: 25px 30px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  max-width: 1200px;
  margin: auto;
  transition: all 0.3s ease;
}

.header-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
}

.header-top h2 {
  font-size: 22px;
  color: #5e35b1;
  margin: 0;
}

.header-top button {
  background: linear-gradient(135deg, #6a11cb, #2575fc);
  color: white;
  border: none;
  padding: 10px 18px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  transition: 0.3s;
}

.header-top button:hover {
  transform: scale(1.05);
  box-shadow: 0 3px 10px rgba(106, 17, 203, 0.3);
}

.info-pegawai {
  background-color: #f9f7ff;
  border-left: 5px solid #6a11cb;
  padding: 15px;
  margin-bottom: 20px;
  border-radius: 10px;
}

.info-pegawai h3 {
  margin: 0;
  color: #333;
  font-size: 18px;
}

.info-pegawai p {
  margin: 3px 0 0;
  color: #555;
  font-size: 14px;
}

/* Table */
table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
  overflow: hidden;
  border-radius: 10px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.05);
}

thead {
  background: linear-gradient(135deg, #6a11cb, #2575fc);
  color: white;
}

th, td {
  padding: 12px 14px;
  text-align: center;
}

tbody tr:nth-child(even) {
  background-color: #fafaff;
}

tbody tr:hover {
  background-color: #f2ebff;
  transition: 0.2s ease;
}

/* Tombol Aksi */
.aksi-btn {
  display: flex;
  justify-content: center;
  gap: 8px;
}

.btn-edit, .btn-hapus {
  padding: 6px 12px;
  border: none;
  border-radius: 6px;
  color: white;
  font-size: 13px;
  cursor: pointer;
  transition: 0.2s;
}

.btn-edit {
  background-color: #43a047;
}

.btn-edit:hover {
  background-color: #2e7d32;
}

.btn-hapus {
  background-color: #e53935;
}

.btn-hapus:hover {
  background-color: #c62828;
}
</style>
</head>
<body>
<div class="main-content">
  <div class="content-wrapper">
    <div class="header-top">
      <h2>Detail Penilaian Pegawai</h2>
      <button onclick="window.location.href='form_penilaian.php?nip=<?php echo $nip; ?>'">+ Tambah Penilaian Baru</button>
    </div>

    <div class="info-pegawai">
      <h3><?php echo $pegawai['nama_lengkap']; ?></h3>
      <p>NIP: <?php echo $pegawai['nip']; ?></p>
    </div>

    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Bulan</th>
          <th>Tahun</th>
          <th>Hari Efektif</th>
          <th>Hari Kerja</th>
          <th>Lupa Absen</th>
          <th>Kedisiplinan</th>
          <th>Kinerja</th>
          <th>Kepemimpinan</th>
          <th>Loyalitas</th>
          <th>IT</th>
          <th>Rata-rata</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($resultPenilaian->num_rows > 0) {
            $no = 1;
            while ($row = $resultPenilaian->fetch_assoc()) {
                echo "
                <tr>
                  <td>{$no}</td>
                  <td>{$row['bulan']}</td>
                  <td>{$row['tahun']}</td>
                  <td>{$row['jumlah_hari_efektif']}</td>
                  <td>{$row['jumlah_hari_kerja']}</td>
                  <td>{$row['lupa_absen']}</td>
                  <td>{$row['nilai_kedisiplinan']}</td>
                  <td>{$row['kinerja']}</td>
                  <td>{$row['kepemimpinan']}</td>
                  <td>{$row['loyalitas']}</td>
                  <td>{$row['it']}</td>
                  <td><strong style='color:#5e35b1;'>{$row['rata_rata']}</strong></td>
                  <td class='aksi-btn'>
                    <button class='btn-edit' onclick=\"window.location.href='form_penilaian.php?id={$row['id_penilaian']}'\">Edit</button>
                    <button class='btn-hapus' onclick=\"hapusData({$row['id_penilaian']})\">Hapus</button>
                  </td>
                </tr>";
                $no++;
            }
        } else {
            echo "<tr><td colspan='13'>Belum ada data penilaian untuk pegawai ini.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<script>
function hapusData(id) {
  if (confirm('Yakin ingin menghapus data ini?')) {
    window.location.href = 'hapus_penilaian.php?id=' + id;
  }
}
</script>

</body>
</html>
