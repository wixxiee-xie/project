<?php
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';
require INCLUDE_PATH . 'sidebar.php';


// Fitur Search
$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "
    SELECT 
        p.*, 
        -- Nilai rata-rata dari semua penilaian (semua bulan)
        ROUND(AVG(n.nilai_kedisiplinan), 2) AS avg_dis,
        ROUND(AVG(n.kinerja), 2) AS avg_kin,
        ROUND(AVG(n.sikap), 2) AS avg_sik,
        ROUND(AVG(n.kepemimpinan), 2) AS avg_kep,
        ROUND(AVG(n.loyalitas), 2) AS avg_loy,
        ROUND(AVG(n.it), 2) AS avg_it,
        ROUND(AVG(n.rata_rata), 2) AS avg_total,

        -- Data terbaru (bulan & tahun terbaru)
        (SELECT nilai_kedisiplinan FROM penilaian WHERE nip = p.nip ORDER BY tahun DESC, bulan DESC LIMIT 1) AS last_dis,
        (SELECT kinerja FROM penilaian WHERE nip = p.nip ORDER BY tahun DESC, bulan DESC LIMIT 1) AS last_kin,
        (SELECT sikap FROM penilaian WHERE nip = p.nip ORDER BY tahun DESC, bulan DESC LIMIT 1) AS last_sik,
        (SELECT kepemimpinan FROM penilaian WHERE nip = p.nip ORDER BY tahun DESC, bulan DESC LIMIT 1) AS last_kep,
        (SELECT loyalitas FROM penilaian WHERE nip = p.nip ORDER BY tahun DESC, bulan DESC LIMIT 1) AS last_loy,
        (SELECT it FROM penilaian WHERE nip = p.nip ORDER BY tahun DESC, bulan DESC LIMIT 1) AS last_it
    FROM pegawai p
    LEFT JOIN penilaian n ON p.nip = n.nip
    WHERE p.nama_lengkap LIKE '%$search%' OR p.nip LIKE '%$search%'
    GROUP BY p.nip
    ORDER BY p.tanggal_dibuat DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Pegawai</title>
<link rel="stylesheet" href="<?= BASE_URL ?>1_css/sidebar.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
/* === STYLE tetap stylish modern === */
body {
  font-family: 'Poppins', sans-serif;
  background-color: #f5f6fa;
  margin: 0;
  padding: 0;
}

.main-content {
  margin-left: 240px;
  padding: 25px;
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

.main-content.collapsed { margin-left: 70px; }

.top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
.top-bar h1 { color: #6a11cb; font-size: 24px; font-weight: 600; }

.actions { display: flex; align-items: center; gap: 10px; }
.search-box { display: flex; background: #fff; border-radius: 8px; border: 1px solid #ddd; overflow: hidden; }
.search-box input { border: none; padding: 8px 10px; width: 180px; font-size: 14px; }
.search-box button { background-color: #6a11cb; color: #fff; border: none; padding: 8px 10px; cursor: pointer; }

.btn-tambah {
  background-color: #6a11cb;
  color: white;
  border: none;
  padding: 8px 14px;
  border-radius: 6px;
  cursor: pointer;
  text-decoration: none;
  transition: 0.3s;
}
.btn-tambah:hover { background-color: #5311b0; }

.container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 1.5rem;
  align-items: start; /* biar card nggak loncat tinggi */
}

.pegawai-card {
  position: relative;
  overflow: hidden;
  background: linear-gradient(145deg, #6a11cb, #f7971e);
  color: #fff;
  border-radius: 18px;
  box-shadow: 0 6px 16px rgba(0,0,0,0.25);
  cursor: pointer;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  will-change: transform;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  transform: translateY(50px); /* start lebih jauh dari bawah */
  animation: fadeSlideIn 1s ease forwards; /* durasi lebih panjang */
}

.pegawai-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 10px 24px rgba(0,0,0,0.35);
}

.rate-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  background: rgba(255,255,255,0.25);
  padding: 8px 14px;
  border-radius: 10px;
  font-size: 18px;
  color: #fff;
  backdrop-filter: blur(8px);
  border: 1.5px solid rgba(255, 255, 255, 0.3);
  text-shadow: 0 1px 4px rgba(0,0,0,0.4);
  letter-spacing: 0.5px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.25);
  z-index: 10;
}


.foto-container {
  position: relative;
  width: 100%;
  height: 260px; /* tinggi tetap agar sejajar semua */
  overflow: hidden;
  border-top-left-radius: 18px;
  border-top-right-radius: 18px;
  background-color: #f5f5f5;
  display: flex;
  justify-content: center;
  align-items: center;
}

.foto-container img {
  width: 100%;
  height: 100%;
  object-fit: cover;     /* isi penuh tanpa distorsi */
  object-position: top;  /* fokus ke wajah */
  display: block;
  transition: transform 0.3s ease;
  margin: 0;
  padding: 0;
}


.pegawai-card:hover .foto-container img {
  transform: scale(1.03);
}

.nama-panel {
  background: rgba(0,0,0,0.3);
  text-align: center;
  padding: 10px 6px;
  border-top: 1px solid rgba(255,255,255,0.2);
  backdrop-filter: blur(6px);
}
.nama-panel h3 {
  font-size: 15px; font-weight: 600; color: #fff; margin: 0;
}
.nama-panel p {
  font-size: 12px; color: #fef3c7; margin: 3px 0 0;
}

.nilai-horizontal {
  text-align: center;
  font-size: 12px;
  font-weight: 500;
  padding: 10px;
  color: #fff;
  background: rgba(0,0,0,0.25);
  border-top: 1px solid rgba(255,255,255,0.15);
  letter-spacing: 0.3px;
  backdrop-filter: blur(6px);
}

.modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 2000; }
.modal-content { background: white; padding: 25px; border-radius: 12px; width: 320px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); position: relative; }
.modal-content h2 { margin-bottom: 10px; color: #6a11cb; }
.modal-content p { margin: 6px 0; font-size: 14px; }
.close { position: absolute; top: 10px; right: 15px; font-size: 24px; cursor: pointer; }
.modal-actions { display: flex; justify-content: space-between; margin-top: 20px; }
.modal-actions a { padding: 8px 12px; border-radius: 6px; color: white; text-decoration: none; font-size: 13px; }
.modal-actions .lihat { background-color: #6a11cb; }
.modal-actions .edit { background-color: #4caf50; }
.modal-actions .hapus { background-color: #e53935; }
.modal-actions a:hover { opacity: 0.9; }
</style>
</head>
<body>
<div class="main-content">
  <div class="top-bar">
    <h1>Data Pegawai</h1>
    <div class="actions">
      <form method="get" class="search-box">
        <input type="text" name="search" placeholder="Cari nama atau NIP..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">🔍</button>
      </form>
      <a href="tambah_pegawai.php" class="btn-tambah">+ Tambah Pegawai</a>
    </div>
  </div>

  <div class="container">
    <?php if ($result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
        <?php 
          $dis = $row['last_dis'] ?? 0;
          $kin = $row['last_kin'] ?? 0;
          $sik = $row['last_sik'] ?? 0;
          $kep = $row['last_kep'] ?? 0;
          $loy = $row['last_loy'] ?? 0;
          $it  = $row['last_it']  ?? 0;
          $avg_total = $row['avg_total'] ?? 0;
        ?>
        <div class="pegawai-card" onclick="openModal('<?= $row['nama_lengkap'] ?>', '<?= $row['nip'] ?>', '<?= $row['jabatan'] ?>', '<?= $row['foto_profil'] ?>', '<?= $row['username'] ?>')">
          <div class="rate-badge">★<?= $avg_total ? number_format($avg_total, 1) : '-' ?></div>
          <div class="foto-container">
           <img src="<?= BASE_URL ?>uploads/<?= $row['foto_profil'] ?: 'default.jpg' ?>" 
           alt="Foto <?= $row['nama_lengkap'] ?>">
          </div>
          <div class="nama-panel">
            <h3><?= strtoupper($row['nama_lengkap']) ?></h3>
            <p><?= $row['jabatan'] ?: '-' ?></p>
          </div>
          <div class="nilai-horizontal">
            DIS: <?= $dis ?> / KIN: <?= $kin ?> / SIK: <?= $sik ?> / KEP: <?= $kep ?> / LOY: <?= $loy ?> / IT: <?= $it ?>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="no-data">Tidak ada data pegawai.</p>
    <?php endif; ?>
  </div>
</div>

<!-- ✅ Modal -->
<div class="modal" id="pegawaiModal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h2 id="nama_lengkap"></h2>
    <p><strong>NIP:</strong> <span id="nip"></span></p>
    <p><strong>Jabatan:</strong> <span id="jabatan"></span></p>
    <div class="modal-actions">
      <a href="#" id="lihatBtn" class="lihat">Lihat Profil</a>
      <a href="#" id="editBtn" class="edit">Edit Data</a>
      <a href="pegawai_hapus.php" id="hapusBtn" class="hapus" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
    </div>
  </div>
</div>

<script>
function openModal(nama, nip, jabatan, foto) {
  document.getElementById('pegawaiModal').style.display = 'flex';
  document.getElementById('nama_lengkap').textContent = nama;
  document.getElementById('nip').textContent = nip;
  document.getElementById('jabatan').textContent = jabatan;
  document.getElementById('lihatBtn').href = 'profil_pegawai.php?user=' + encodeURIComponent(nip);
  document.getElementById('editBtn').href = 'edit_pegawai.php?user=' + encodeURIComponent(nip);
  document.getElementById('hapusBtn').href = 'pegawai_hapus.php?user=' + encodeURIComponent(nip);
}
function closeModal() {
  document.getElementById('pegawaiModal').style.display = 'none';
}
window.onclick = function(e) {
  const modal = document.getElementById('pegawaiModal');
  if (e.target == modal) modal.style.display = "none";
}
</script>

</body>
</html>
