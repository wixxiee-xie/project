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

// Ambil data pegawai (opsional untuk tampilan)
$pegawai = $conn->query("SELECT * FROM pegawai WHERE nip = '". $conn->real_escape_string($nip) ."'")->fetch_assoc();

// Ambil daftar pengaduan milik karyawan (terbaru dulu)
$stmt = $conn->prepare("SELECT * FROM pengaduan WHERE nip = ? ORDER BY tanggal_pengaduan DESC");
$stmt->bind_param("s", $nip);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pengaduan Lupa Absen</title>
<link rel="stylesheet" href="<?= BASE_URL ?>1_css/sidebar.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
/* Gunakan style serupa dengan template kamu, disesuaikan */
body { font-family: 'Poppins', sans-serif; background-color: #f4f6f9; margin:0; color:#333; }
.main-content { margin-left:240px; padding:40px; animation:fadeIn 0.8s ease forwards; }
@keyframes fadeIn { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
.card { background:#fff; border-radius:14px; padding:22px; box-shadow:0 5px 18px rgba(0,0,0,0.08); margin-bottom:20px; }
h1{ color:#1d3557; font-size:26px; font-weight:600; margin-bottom:18px; }
.form-row { display:flex; gap:12px; flex-wrap:wrap; }
.form-row .col { flex:1 1 220px; min-width:180px; }
.label { font-weight:600; margin-bottom:6px; color:#1d3557; display:block; }
.input, select, textarea { width:100%; padding:10px 12px; border-radius:8px; border:1px solid #e6e9ef; font-size:14px; }
.btn { display:inline-block; padding:10px 16px; background:#457b9d; color:#fff; border-radius:8px; text-decoration:none; border:none; cursor:pointer; font-weight:600; }
.btn:disabled { opacity:0.6; cursor:not-allowed; }
.preview { margin-top:10px; max-width:220px; border-radius:8px; box-shadow:0 3px 10px rgba(0,0,0,0.06); }
.status-badge { display:inline-block; padding:6px 10px; border-radius:999px; font-weight:600; font-size:13px; }
.status-pending { background:#e6f0fb; color:#1d6fb0; }
.status-approved { background:#e6f9f0; color:#1a8a4a; }
.status-rejected { background:#fdecea; color:#b32525; }
.table { width:100%; border-collapse:collapse; margin-top:12px; font-size:14px; }
.table th, .table td { padding:10px 12px; border-bottom:1px solid #eee; text-align:left; }
.small-muted { font-size:13px; color:#657286; }
</style>
</head>
<body>
<div class="main-content">

<?php if (isset($_SESSION['success_pengaduan'])): ?>
    <script>
        alert("<?= addslashes($_SESSION['success_pengaduan']) ?>");
    </script>
    <?php unset($_SESSION['success_pengaduan']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['err_pengaduan'])): ?>
    <script>
        alert("<?= addslashes($_SESSION['err_pengaduan']) ?>");
    </script>
    <?php unset($_SESSION['err_pengaduan']); ?>
<?php endif; ?>

  <h1>Pengaduan Lupa Absen</h1>

  <div class="card">
    <form action="../pengaduan_proses.php" method="POST" enctype="multipart/form-data">
      <div class="form-row">
        <div class="col">
          <label class="label">Jenis Laporan</label>
          <select name="jenis_laporan" id="jenis_laporan" class="input" required>
            <option value="">-- Pilih jenis laporan --</option>
            <option value="Lupa Absen Masuk">Lupa Absen Masuk</option>
            <option value="Lupa Absen Pulang">Lupa Absen Pulang</option>
            <option value="Lainnya">Lainnya</option>
          </select>
        </div>

        <div class="col" id="customJenisWrapper" style="display:none;">
          <label class="label">Jika "Lainnya", sebutkan</label>
          <input type="text" name="jenis_laporan_custom" id="jenis_laporan_custom" class="input" placeholder="Masukkan jenis laporan" />
        </div>

        <div class="col">
          <label class="label">Tanggal Kejadian</label>
          <input type="date" name="tanggal_kejadian" class="input" required />
        </div>
      </div>

      <div style="margin-top:12px;">
        <label class="label">Keterangan (opsional)</label>
        <textarea name="keterangan" rows="4" class="input" placeholder="Jelaskan alasan anda disini"></textarea>
      </div>

      <div style="margin-top:12px;">
        <label class="label">Upload Bukti (max 2MB)</label>
       <input type="file" name="bukti" id="bukti" accept=".jpg,.jpeg,.png" class="input" />
        <img src="" id="preview" class="preview" style="display:none;">
        <div class="small-muted">Format foto: JPG, JPEG, PNG (maks 2MB).</div>    
      </div>

      <div style="margin-top:14px;">
        <button type="submit" class="btn">Kirim Pengaduan</button>
      </div>
    </form>
  </div>

  <div class="card">
    <h2 style="margin-top:0;">Riwayat Pengaduan</h2>

    <?php if ($result->num_rows > 0): ?>
      <table class="table">
        <thead>
          <tr><th>Tanggal Pengaduan</th><th>Jenis</th><th>Tanggal Kejadian</th><th>Status</th><th>Bukti</th></tr>
        </thead>
        <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars(date('d M Y, H:i', strtotime($row['tanggal_pengaduan']))) ?></td>
            <td>
              <?php
                if ($row['jenis_laporan'] === 'Lainnya') {
                  echo 'Lainnya: ' . htmlspecialchars($row['jenis_laporan_custom']);
                } else {
                  echo htmlspecialchars($row['jenis_laporan']);
                }
              ?>
            </td>
            <td><?= htmlspecialchars(date('d M Y', strtotime($row['tanggal_kejadian']))) ?></td>
            <td>
              <?php if ($row['status'] === 'pending'): ?>
                <span class="status-badge status-pending">Pending</span>
              <?php elseif ($row['status'] === 'approved'): ?>
                <span class="status-badge status-approved">Approved</span>
              <?php else: ?>
                <span class="status-badge status-rejected">Rejected</span>
                <?php if ($row['alasan_penolakan']): ?>
                  <div class="small-muted">Alasan: <?= htmlspecialchars($row['alasan_penolakan']) ?></div>
                <?php endif; ?>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($row['bukti']): ?>
                <a href="../../uploads/pengaduan/<?= rawurlencode($row['bukti']) ?>" target="_blank">Lihat Bukti</a>
              <?php else: ?>
                -
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="small-muted">Belum ada pengaduan.</div>
    <?php endif; ?>
  </div>
</div>

<script>
document.getElementById('jenis_laporan').addEventListener('change', function(){
  var val = this.value;
  var wrapper = document.getElementById('customJenisWrapper');
  var input = document.getElementById('jenis_laporan_custom');
  if (val === 'Lainnya') {
    wrapper.style.display = 'block';
    input.required = true;
  } else {
    wrapper.style.display = 'none';
    input.required = false;
    input.value = '';
  }
});

document.getElementById('bukti').addEventListener('change', function(e){
  var file = this.files[0];
  if (!file) return;

  var maxSize = 2 * 1024 * 1024; // 2MB
  if (file.size > maxSize) {
    alert('File terlalu besar. Maksimal 2MB.');
    this.value = '';
    return;
  }

  var allowed = ['image/jpeg','image/jpg','image/png'];
  if (!allowed.includes(file.type)) {
    alert('Format tidak didukung. Gunakan foto JPG, JPEG, atau PNG');
    this.value = '';
    return;
  }

  var reader = new FileReader();
  reader.onload = function(ev){
    var img = document.getElementById('preview');
    img.src = ev.target.result;
    img.style.display = 'block';
  };
  reader.readAsDataURL(file);
});

</script>
</body>
</html>
