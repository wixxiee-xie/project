<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';
require INCLUDE_PATH . 'sidebar_pegawai.php';

// Pastikan pegawai sudah login
if (!isset($_SESSION['nip'])) {
    header("Location: login.php");
    exit;
}

$nip = $_SESSION['nip'];

// Ambil data pegawai
$pegawai = $conn->query("SELECT * FROM pegawai WHERE nip = '$nip'")->fetch_assoc();

// Ambil data penilaian
$sql = "
    SELECT 
        bulan,
        tahun,
        nilai_kedisiplinan,
        kinerja,
        sikap,
        kepemimpinan,
        loyalitas,
        it,
        rata_rata,
        masukan_atasan
    FROM penilaian
    WHERE nip = '$nip'
    ORDER BY tahun DESC, STR_TO_DATE(bulan, '%M') DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Penilaian Pegawai</title>
<link rel="stylesheet" href="<?= BASE_URL ?>1_css/sidebar.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
body {
  font-family: 'Poppins', sans-serif;
  background-color: #f4f6f9;
  margin: 0;
  padding: 0;
  color: #333;
}
.main-content {
  margin-left: 240px;
  padding: 40px;
  animation: fadeIn 0.8s ease forwards;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
h1 {
  font-weight: 600;
  font-size: 26px;
  margin-bottom: 20px;
  color: #1d3557;
}
.table-container {
  background: #fff;
  border-radius: 14px;
  padding: 25px;
  box-shadow: 0 5px 18px rgba(0,0,0,0.1);
  margin-top: 25px;
  overflow-x: auto;
}
table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}
table th, table td {
  padding: 12px 14px;
  text-align: center;
  border-bottom: 1px solid #ddd;
}
table th {
  background-color: #f0f3f7;
  color: #1d3557;
  font-weight: 600;
}
table tr:hover {
  background-color: #f9fafc;
}
.masukan-box {
  background: #fff;
  border-left: 5px solid #457b9d;
  padding: 18px 22px;
  margin-top: 30px;
  border-radius: 10px;
  box-shadow: 0 3px 12px rgba(0,0,0,0.08);
}
.masukan-box h3 {
  margin-top: 0;
  color: #1d3557;
  font-size: 18px;
}
.chart-container {
  margin-top: 40px;
  background: #fff;
  border-radius: 14px;
  padding: 25px;
  box-shadow: 0 5px 18px rgba(0,0,0,0.1);
}
canvas {
  width: 100%;
  max-height: 380px;
}
.btn-back {
  display: inline-block;
  margin-top: 30px;
  padding: 10px 20px;
  background: #457b9d;
  color: #fff;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 500;
  transition: 0.3s;
}
.btn-back:hover {
  background: #1d3557;
}
</style>
</head>
<body>
<div class="main-content">
  <h1>Laporan Penilaian Pegawai</h1>

  <div class="laporan-container">
    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card">
          <div class="card-header">
            <h2>Periode: <?= htmlspecialchars($row['bulan'] . ' ' . $row['tahun']) ?></h2>
            <span style="font-size:14px;color:#1d3557;">Rata-rata: <strong><?= $row['rata_rata'] ?></strong></span>
          </div>

          <div class="table-wrapper">
            <table>
              <thead>
                <tr>
                  <th>Kedisiplinan</th>
                  <th>Kinerja</th>
                  <th>Sikap</th>
                  <th>Kepemimpinan</th>
                  <th>Loyalitas</th>
                  <th>IT</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><?= $row['nilai_kedisiplinan'] ?></td>
                  <td><?= $row['kinerja'] ?></td>
                  <td><?= $row['sikap'] ?></td>
                  <td><?= $row['kepemimpinan'] ?></td>
                  <td><?= $row['loyalitas'] ?></td>
                  <td><?= $row['it'] ?></td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="masukan-box">
            <strong>Masukan Atasan:</strong>
            <?= $row['masukan_atasan'] ? htmlspecialchars($row['masukan_atasan']) : '<em>Belum ada masukan dari atasan.</em>' ?>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="no-data">Belum ada data penilaian untuk Anda.</div>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
