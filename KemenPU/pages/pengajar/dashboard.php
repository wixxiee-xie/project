<?php
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';
require INCLUDE_PATH . 'topbar_pengajar.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="<?= BASE_URL ?>1_css/topbar.css">
<link rel="stylesheet" href="<?= BASE_URL ?>1_css/footer.css">
<title>Dashboard | Wilayah Kerja Bapekom PU VIII Makassar</title>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<style>
/* ===================== GLOBAL ===================== */
body {
  font-family: 'Poppins', sans-serif;
  margin: 0;
  background: linear-gradient(135deg, #e3eeff, #f5f9ff);
  color: #333;
  overflow-x: hidden;
  animation: fadeIn 0.6s ease-in-out;
}

/* ===================== TOPBAR ===================== */
.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: linear-gradient(90deg, #1e3c72, #2a5298);
  color: #fff;
  padding: 18px 60px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  position: sticky;
  top: 0;
  z-index: 100;
}
.topbar h1 {
  font-size: 20px;
  font-weight: 600;
  letter-spacing: 0.5px;
}
.topbar a {
  color: #fff;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s ease;
}
.topbar a:hover {
  color: #ffd86f;
}

/* ===================== HEADER ===================== */
.dashboard-container {
  text-align: center;
  padding: 80px 60px 40px;
}
.dashboard-container h2 {
  font-size: 1.9rem;
  font-weight: 700;
  color: #1e3c72;
}
.dashboard-container .subtext {
  color: #555;
  margin-top: 10px;
  font-size: 15px;
}

/* ===================== GRID CARD ===================== */
.balai-grid {
  display: grid;
  grid-template-columns: repeat(7, minmax(180px, 1fr));
  gap: 25px;
  justify-content: center;
  align-items: stretch;
  margin: 60px auto;
  padding: 0 60px;
  max-width: 1700px;
}
@media (max-width: 1400px) { .balai-grid { grid-template-columns: repeat(4, 1fr); } }
@media (max-width: 992px) { .balai-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 768px) { .balai-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 500px) { .balai-grid { grid-template-columns: 1fr; } }

/* ===================== CARD STYLE ===================== */
.balai-card {
  position: relative;
  background: linear-gradient(145deg, #f8faff, #e6ecf8);
  border-radius: 18px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.08);
  padding: 28px 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-decoration: none;
  color: inherit;
  transition: all 0.4s ease;
  overflow: hidden;
  cursor: pointer;
}

/* Gradasi lembut per card */
.balai-card:nth-child(1) { background: linear-gradient(145deg, #e3eeff, #f2f7ff); }
.balai-card:nth-child(2) { background: linear-gradient(145deg, #f3f1ff, #f8f5ff); }
.balai-card:nth-child(3) { background: linear-gradient(145deg, #fdf6ef, #f9f3e8); }
.balai-card:nth-child(4) { background: linear-gradient(145deg, #f1fbf4, #e8f9ef); }
.balai-card:nth-child(5) { background: linear-gradient(145deg, #fef3f6, #fbeff4); }
.balai-card:nth-child(6) { background: linear-gradient(145deg, #f4faff, #edf6fb); }
.balai-card:nth-child(7) { background: linear-gradient(145deg, #f7f3ff, #eee9fb); }

/* Hover effect */
.balai-card::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(70,90,255,0.08), rgba(120,150,255,0.1));
  opacity: 0;
  transition: opacity 0.4s ease;
  z-index: 0;
}
.balai-card:hover::before { opacity: 1; }

.balai-card:hover {
  transform: translateY(-10px) scale(1.03);
  box-shadow: 0 15px 35px rgba(0,0,0,0.12);
}

.balai-icon {
  font-size: 2.6rem;
  color: #4253e8;
  margin-bottom: 15px;
  z-index: 1;
}

.balai-info h3 {
  margin: 5px 0 6px;
  color: #1f2764;
  font-size: 1rem;
  z-index: 1;
}

.balai-info p {
  font-size: 13px;
  color: #555;
  z-index: 1;
  margin: 0;
}

/* ===================== DECORATIVE ELEMENTS ===================== */
.floating-shape {
  position: absolute;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(102,126,234,0.25), transparent 70%);
  filter: blur(70px);
  z-index: -1;
  animation: float 10s infinite ease-in-out alternate;
}
.shape1 { width: 350px; height: 350px; top: -100px; left: -80px; }
.shape2 { width: 300px; height: 300px; bottom: -120px; right: -60px; }
@keyframes float {
  from { transform: translateY(0px); }
  to { transform: translateY(30px); }
}

/* ===================== ANNOUNCEMENT ===================== */
.announcement {
  background: linear-gradient(135deg, #eaf3ff, #f7fbff);
  border-left: 6px solid #1e3c72;
  padding: 22px 28px;
  border-radius: 14px;
  margin: 70px auto 40px;
  width: 90%;
  box-shadow: 0 6px 20px rgba(30, 60, 114, 0.15);
  transition: transform 0.3s ease;
}
.announcement:hover { transform: translateY(-5px); }
.announcement h3 {
  margin: 0;
  color: #1f3a66;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 10px;
}
.announcement p {
  color: #444;
  font-size: 14px;
  margin-top: 5px;
}

/* ===================== ANIMATIONS ===================== */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(15px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
</head>
<body>
<div class="floating-shape shape1"></div>
<div class="floating-shape shape2"></div>
<!-- MAIN -->
<div class="dashboard-container">
  <h2>Wilayah Kerja Balai Pengembangan Kompetensi PU Wilayah VIII Makassar</h2>
  <p class="subtext">Pilih salah satu wilayah untuk melihat data dinas dan hasil identifikasi pelatihan.</p>

  <div class="balai-grid">
    <?php
    $wilayah = [
      "WILAYAH KERJA SULAWESI SELATAN",
      "WILAYAH KERJA SULAWESI BARAT",
      "WILAYAH KERJA SULAWESI TENGAH",
      "WILAYAH KERJA SULAWESI UTARA",
      "WILAYAH KERJA SULAWESI TENGGARA",
      "WILAYAH KERJA GORONTALO",
      "WILAYAH KERJA MALUKU UTARA"
    ];
    $icons = ['fa-map-marked-alt', 'fa-globe-asia', 'fa-university', 'fa-city', 'fa-building', 'fa-landmark', 'fa-sitemap'];
    foreach ($wilayah as $i => $nama) {
        $id = $i + 1; // ID wilayah yang sesuai database (1–7)

        echo "
        <a href='wilayah.php?id=$id' class='balai-card'>
          <div class='balai-icon'>
            <i class='fas {$icons[$i]}'></i>
          </div>
          <div class='balai-info'>
            <h3>$nama</h3>
            <p>Lihat dinas terkait dan hasil identifikasi pelatihan di wilayah ini.</p>
          </div>
        </a>
        ";
}

    ?>
  </div>

  <div class="announcement">
    <h3><i class="fas fa-bullhorn"></i> Informasi Sistem</h3>
    <p>Pastikan data dinas dan pelatihan selalu diperbarui agar proses penyusunan program berjalan optimal.</p>
  </div>
</div>
</body>
</html>
