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
$sql = "
  SELECT p.*, 
         ROUND(AVG(n.nilai_kedisiplinan), 2) AS avg_dis,
         ROUND(AVG(n.kinerja), 2) AS avg_kin,
         ROUND(AVG(n.sikap), 2) AS avg_sik,
         ROUND(AVG(n.kepemimpinan), 2) AS avg_kep,
         ROUND(AVG(n.loyalitas), 2) AS avg_loy,
         ROUND(AVG(n.it), 2) AS avg_it,
         ROUND(AVG(n.rata_rata), 2) AS avg_total
  FROM pegawai p
  LEFT JOIN penilaian n ON p.nip = n.nip
  WHERE p.nip = '$nip'
  GROUP BY p.nip
";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
  echo "<h2 style='text-align:center;margin-top:50px;color:#888;'>Data tidak ditemukan.</h2>";
  exit;
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Profil Saya</title>
<link rel="stylesheet" href="<?= BASE_URL ?>1_css/sidebar.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #0e00d1ff, #ffd900ff);
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
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
.profile-card {
  background: rgba(255,255,255,0.1);
  border-radius: 20px;
  padding: 40px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.25);
  backdrop-filter: blur(15px);
  display: flex;
  gap: 40px;
  align-items: center;
  color: white;
}
.profile-photo {
  flex: 0 0 250px;
  height: 250px;
  border-radius: 50%;
  overflow: hidden;
  border: 4px solid rgba(255,255,255,0.4);
  box-shadow: 0 4px 20px rgba(0,0,0,0.4);
}
.profile-photo img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.profile-info {
  flex: 1;
}
.profile-info h1 {
  font-size: 28px;
  font-weight: 700;
  margin-bottom: 5px;
  text-transform: uppercase;
}
.profile-info p {
  font-size: 16px;
  margin: 4px 0;
}
.stat-card {
  margin-top: 35px;
  background: rgba(255,255,255,0.15);
  border-radius: 16px;
  padding: 25px;
  backdrop-filter: blur(10px);
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 20px;
  color: #fff;
  text-align: center;
}
.stat {
  background: rgba(255,255,255,0.08);
  border-radius: 12px;
  padding: 12px;
  transition: 0.3s;
}
.stat:hover {
  transform: translateY(-4px);
  background: rgba(255,255,255,0.2);
}
.stat h2 {
  font-size: 22px;
  margin: 5px 0;
  color: #fff;
}
.stat span {
  font-size: 13px;
  letter-spacing: 0.3px;
  color: #eee;
}
.avg-score {
  margin-top: 40px;
  text-align: center;
}
.avg-score .circle {
  width: 130px;
  height: 130px;
  border-radius: 50%;
  background: conic-gradient(#f7971e <?= ($row['avg_total'] / 5) * 100 ?>%, rgba(255,255,255,0.1) 0%);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 34px;
  font-weight: bold;
  color: #fff;
  margin: 0 auto 10px;
  box-shadow: 0 0 15px rgba(0,0,0,0.3);
}
.btn-back {
  display: inline-block;
  margin-top: 30px;
  padding: 10px 18px;
  background: rgba(255,255,255,0.2);
  border-radius: 10px;
  text-decoration: none;
  color: #fff;
  transition: 0.3s;
}
.btn-back:hover {
  background: rgba(255,255,255,0.35);
}
</style>
</head>
<body>
<div class="main-content">
  <div class="profile-card">
    <div class="profile-photo">
           <img src="<?= BASE_URL ?>uploads/<?= $row['foto_profil'] ?: 'default.jpg' ?>" 
           alt="Foto <?= $row['nama_lengkap'] ?>">
    </div>
    <div class="profile-info">
      <h1><?= strtoupper($row['nama_lengkap']) ?></h1>
      <p><strong>NIP:</strong> <?= $row['nip'] ?></p>
      <p><strong>Jabatan:</strong> <?= $row['jabatan'] ?: '-' ?></p>
      <p><strong>Username:</strong> <?= $row['username'] ?></p>
      <a href="dashboard.php" class="btn-back">← Kembali ke Dashboard</a>
    </div>
  </div>

  <div class="avg-score">
    <div class="circle"><?= number_format($row['avg_total'] ?? 0, 1) ?></div>
    <p>Rata-Rata Keseluruhan</p>
  </div>

  <div class="stat-card">
    <div class="stat"><h2><?= $row['avg_dis'] ?: '-' ?></h2><span>Kedisiplinan</span></div>
    <div class="stat"><h2><?= $row['avg_kin'] ?: '-' ?></h2><span>Kinerja</span></div>
    <div class="stat"><h2><?= $row['avg_sik'] ?: '-' ?></h2><span>Sikap</span></div>
    <div class="stat"><h2><?= $row['avg_kep'] ?: '-' ?></h2><span>Kepemimpinan</span></div>
    <div class="stat"><h2><?= $row['avg_loy'] ?: '-' ?></h2><span>Loyalitas</span></div>
    <div class="stat"><h2><?= $row['avg_it'] ?: '-' ?></h2><span>IT Skill</span></div>
  </div>
</div>

</body>
</html>
