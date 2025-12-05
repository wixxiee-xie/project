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

// Hitung nilai rata-rata keseluruhan dan per aspek
$sql = "
    SELECT 
        ROUND(AVG(nilai_kedisiplinan), 2) AS avg_dis,
        ROUND(AVG(kinerja), 2) AS avg_kin,
        ROUND(AVG(sikap), 2) AS avg_sik,
        ROUND(AVG(kepemimpinan), 2) AS avg_kep,
        ROUND(AVG(loyalitas), 2) AS avg_loy,
        ROUND(AVG(it), 2) AS avg_it,
        ROUND(AVG(rata_rata), 2) AS avg_total
    FROM penilaian 
    WHERE nip = '$nip'
";
$nilai = $conn->query($sql)->fetch_assoc();

// Ambil rata-rata bulanan untuk chart
$chartData = $conn->query("
    SELECT bulan, ROUND(AVG(rata_rata), 2) AS rata_bulanan
    FROM penilaian
    WHERE nip = '$nip'
    GROUP BY bulan
    ORDER BY STR_TO_DATE(bulan, '%M %Y') ASC
");

$bulan = [];
$rata = [];
while ($row = $chartData->fetch_assoc()) {
    $bulan[] = $row['bulan'];
    $rata[] = $row['rata_bulanan'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Pegawai</title>
<link rel="stylesheet" href="<?= BASE_URL ?>1_css/sidebar.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body {
  font-family: 'Poppins', sans-serif;
  background-color: #f8f9fa;
  margin: 0;
  padding: 0;
  color: #333;
  overflow-x: hidden;
}

/* ===== Main Content ===== */
.main-content {
  margin-left: 240px;
  padding: 40px;
  animation: fadeIn 0.8s ease forwards;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

/* ===== Heading ===== */
h1 {
  font-weight: 600;
  font-size: 28px;
  margin-bottom: 20px;
  color: #1d3557;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
}

.welcome {
  font-size: 20px;
  color: #333;
  font-weight: 500;
}

/* ===== Card Container ===== */
.card-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 25px;
  margin-top: 30px;
}

.card {
  background: #fff;
  border-radius: 14px;
  padding: 25px;
  text-align: center;
  transition: 0.3s ease;
  box-shadow: 0 3px 8px rgba(0,0,0,0.08);
  border: 1px solid #e0e0e0;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 12px rgba(0,0,0,0.1);
}

.card h2 {
  font-size: 34px;
  margin: 5px 0;
  color: #1d3557;
}

.card p {
  font-size: 14px;
  color: #666;
  letter-spacing: 0.4px;
}

.progress-container {
  margin-top: 40px;
  background: rgba(255,255,255,0.08);
  padding: 25px;
  border-radius: 20px;
  backdrop-filter: blur(10px);
}
.progress {
  margin-bottom: 15px;
}
.progress span {
  font-size: 14px;
  display: block;
  margin-bottom: 6px;
}
.progress-bar {
  background: rgba(255,255,255,0.1);
  border-radius: 10px;
  overflow: hidden;
  height: 12px;
}
.progress-bar div {
  height: 100%;
  border-radius: 10px;
  background: linear-gradient(90deg, #f7951eaf, #fffb00ab);
  width: 0;
  transition: width 1s ease;
}

.chart-container {
  margin-top: 50px;
  background: rgba(255, 255, 255, 0.35); /* soft frosted white */
  border-radius: 20px;
  padding: 30px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.25);
}

canvas {
  width: 100%;
  max-height: 400px;
}

.btn-group {
  margin-top: 30px;
  display: flex;
  gap: 15px;
}
.btn {
  background: rgba(255,255,255,0.2);
  color: white;
  text-decoration: none;
  padding: 10px 20px;
  border-radius: 10px;
  font-weight: 500;
  transition: 0.3s;
}
.btn:hover {
  background: rgba(255,255,255,0.35);
  transform: translateY(-2px);
}
</style>
</head>
<body>
<div class="main-content">
  <div class="dashboard-header">
    <h1>Dashboard Pegawai</h1>
    <div class="welcome">Selamat datang, <strong><?= $pegawai['nama_lengkap'] ?></strong></div>
  </div>

  <div class="card-container">
    <div class="card">
      <h2><?= $nilai['avg_total'] ?: '-' ?></h2>
      <p>Rata-rata Keseluruhan</p>
    </div>
    <div class="card">
      <h2><?= $nilai['avg_dis'] ?: '-' ?></h2>
      <p>Kedisiplinan</p>
    </div>
    <div class="card">
      <h2><?= $nilai['avg_kin'] ?: '-' ?></h2>
      <p>Kinerja</p>
    </div>
    <div class="card">
      <h2><?= $nilai['avg_sik'] ?: '-' ?></h2>
      <p>Sikap</p>
    </div>
  </div>

  <div class="progress-container">
    <h3 style="margin-bottom:15px;">Progress Nilai Tiap Aspek</h3>
    <?php 
      $aspek = ['Kedisiplinan' => 'avg_dis', 'Kinerja' => 'avg_kin', 'Sikap' => 'avg_sik', 'Kepemimpinan' => 'avg_kep', 'Loyalitas' => 'avg_loy', 'IT Skill' => 'avg_it'];
      foreach ($aspek as $nama => $kolom):
          $persen = ($nilai[$kolom] / 5) * 100;
    ?>
      <div class="progress">
        <span><?= $nama ?> (<?= $nilai[$kolom] ?: '-' ?>)</span>
        <div class="progress-bar"><div style="width: <?= $persen ?>%;"></div></div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="chart-container">
    <h3 style="margin-bottom:20px;">Grafik Perkembangan Nilai Bulanan</h3>
    <canvas id="chartNilai"></canvas>
  </div>

  <div class="btn-group">
    <a href="profil.php?user=<?= $nip ?>" class="btn">Lihat Profil</a>
    <a href="laporan.php" class="btn">Lihat Laporan</a>
  </div>
</div>

<script>
const labels = <?= json_encode($bulan) ?>;
const dataValues = <?= json_encode($rata) ?>;

const ctx = document.getElementById('chartNilai').getContext('2d');
const gradient = ctx.createLinearGradient(0, 0, 0, 400);
gradient.addColorStop(0, 'rgba(255, 251, 0, 0.6)');  // biru lembut
gradient.addColorStop(1, 'rgba(180, 210, 255, 0.1)'); // putih kebiruan transparan

new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Rata-rata Nilai Bulanan',
            data: dataValues,
            backgroundColor: gradient,
            borderColor: '#ffe600ff',   // biru elegan
            fill: true,
            tension: 0.35,
            borderWidth: 2,
            pointRadius: 4,
            pointBackgroundColor: '#ffe600ff'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { 
                beginAtZero: true, 
                ticks: { color: '#333' }, 
                grid: { color: 'rgba(0,0,0,0.05)' } 
            },
            x: { 
                ticks: { color: '#333' }, 
                grid: { color: 'rgba(0,0,0,0.03)' } 
            }
        }
    }
});

</script>

</body>
</html>
