<?php
// form_penilaian.php
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ambil nip dari GET (untuk tambah) atau ambil id untuk edit
$nip = isset($_GET['nip']) ? $conn->real_escape_string($_GET['nip']) : null;
$id   = isset($_GET['id'])  ? intval($_GET['id']) : null;

// ambil data pegawai bila nip tersedia
$pegawai = null;
if ($nip) {
    $q = $conn->query("SELECT nip, nama_lengkap, jabatan FROM pegawai WHERE nip = '$nip' LIMIT 1");
    if ($q && $q->num_rows) $pegawai = $q->fetch_assoc();
}

// bila edit (ada id), ambil data penilaian
$editing = false;
$editData = [];
if ($id) {
    $editing = true;
    $q = $conn->query("SELECT * FROM penilaian WHERE id_penilaian = $id LIMIT 1");
    if ($q && $q->num_rows) {
        $editData = $q->fetch_assoc();
        // jika nip tidak di-set, ambil dari editData
        if (!$nip && isset($editData['nip'])) {
            $nip = $editData['nip'];
            $q2 = $conn->query("SELECT nip, nama_lengkap, jabatan FROM pegawai WHERE nip = '$nip' LIMIT 1");
            if ($q2 && $q2->num_rows) $pegawai = $q2->fetch_assoc();
        }
    } else {
        die("Data penilaian tidak ditemukan.");
    }
}

// proses simpan (insert atau update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    // ambil & sanitasi
    $nip_post = $conn->real_escape_string($_POST['nip']);
    $bulan = $conn->real_escape_string($_POST['bulan']);
    $masukan_atasan = $conn->real_escape_string($_POST['masukan_atasan']);
    $tahun = intval($_POST['tahun']);
    $jumlah_hari_efektif = intval($_POST['jumlah_hari_efektif']);
    $jumlah_hari_kerja   = intval($_POST['jumlah_hari_kerja']);
    $lupa_absen          = intval($_POST['lupa_absen']);

    // kedisiplinan diset dari input (frontend sudah menghitung), tetap sanitize
    $nilai_kedisiplinan  = floatval($_POST['nilai_kedisiplinan']);

    $kinerja     = floatval($_POST['kinerja']);
    $sikap       = floatval($_POST['sikap']);
    $kepemimpinan= floatval($_POST['kepemimpinan']);
    $loyalitas   = floatval($_POST['loyalitas']);
    $it          = floatval($_POST['it']);

    // (opsional) hitung kembali server-side agar aman
    if ($jumlah_hari_efektif > 0) {
        $calc_dis = (($jumlah_hari_kerja - $lupa_absen) / $jumlah_hari_efektif) * 100;
        if (!is_finite($calc_dis)) $calc_dis = 0;
        $calc_dis = round(max(0, min(100, $calc_dis)), 2);
    } else {
        $calc_dis = round(max(0, min(100, $nilai_kedisiplinan)), 2);
    }
    // gunakan hasil server-side sebagai sumber kebenaran
    $nilai_kedisiplinan = $calc_dis;

    // rata-rata akhir (6 aspek) server-side
    $rata = round( ($nilai_kedisiplinan + $kinerja + $sikap + $kepemimpinan + $loyalitas + $it) / 6, 2 );

    if (isset($_POST['id']) && intval($_POST['id']) > 0) {
        // update
        $id_upd = intval($_POST['id']);
        $sql = "UPDATE penilaian SET
            nip = '$nip_post',
            bulan = '$bulan',
            tahun = $tahun,
            jumlah_hari_efektif = $jumlah_hari_efektif,
            jumlah_hari_kerja = $jumlah_hari_kerja,
            lupa_absen = $lupa_absen,
            nilai_kedisiplinan = $nilai_kedisiplinan,
            kinerja = $kinerja,
            sikap = $sikap,
            kepemimpinan = $kepemimpinan,
            loyalitas = $loyalitas,
            it = $it,
            masukan_atasan = '$masukan_atasan'
            WHERE id_penilaian = $id_upd
        ";

        $ok = $conn->query($sql);
        if ($ok) {
            header("Location: detail_penilaian.php?nip=" . urlencode($nip_post) . "&success=updated");
            exit;
        } else {
            $error = "Gagal update: " . $conn->error;
        }
    } else {
        // insert (cek apakah bulan & tahun untuk nip sudah ada)
        $cek = $conn->query("SELECT id_penilaian FROM penilaian WHERE nip = '$nip_post' AND bulan = '$bulan' AND tahun = $tahun LIMIT 1");
        if ($cek && $cek->num_rows > 0) {
            $error = "Penilaian untuk pegawai ini pada bulan & tahun tersebut sudah ada. Gunakan fitur edit.";
        } else {
            $sql = "INSERT INTO penilaian
(nip, bulan, tahun, jumlah_hari_efektif, jumlah_hari_kerja, lupa_absen, nilai_kedisiplinan, kinerja, sikap, kepemimpinan, loyalitas, it, masukan_atasan)
VALUES
('$nip_post', '$bulan', $tahun, $jumlah_hari_efektif, $jumlah_hari_kerja, $lupa_absen, $nilai_kedisiplinan, $kinerja, $sikap, $kepemimpinan, $loyalitas, $it, '$masukan_atasan')";
            $ok = $conn->query($sql);
            if ($ok) {
                header("Location: detail_penilaian.php?nip=" . urlencode($nip_post) . "&success=1");
                exit;
            } else {
                $error = "Gagal menyimpan: " . $conn->error;
            }
        }
    }
}

// nilai untuk prefill (edit) atau default
$pref = [
    'bulan' => $editData['bulan'] ?? '',
    'tahun'  => $editData['tahun'] ?? date('Y'),
    'jumlah_hari_efektif' => $editData['jumlah_hari_efektif'] ?? 0,
    'jumlah_hari_kerja'   => $editData['jumlah_hari_kerja'] ?? 0,
    'lupa_absen'          => $editData['lupa_absen'] ?? 0,
    'nilai_kedisiplinan'  => $editData['nilai_kedisiplinan'] ?? 0,
    'kinerja'  => $editData['kinerja'] ?? 0,
    'sikap'    => $editData['sikap'] ?? 0,
    'kepemimpinan' => $editData['kepemimpinan'] ?? 0,
    'loyalitas'    => $editData['loyalitas'] ?? 0,
    'it'           => $editData['it'] ?? 0,
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Form Penilaian Pegawai</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
/* Global & layout */
body { 
  font-family: 'Poppins', sans-serif; margin:0; 
  background: linear-gradient(135deg,#f3e7ff,#ffe3f4); color:#222; }

  .main-content { 
    margin-left:240px; 
    padding:32px; 
    transition:margin-left .3s; }

.content-card { 
    width: 90%;
    max-width: 1200px;
    margin: 45px auto 45px 0;   /* geser ke kanan */
    background: #fff;
    border-radius: 14px;
    padding: clamp(20px, 3vw, 32px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    animation: fadeIn 0.5s ease;
}


/* Header */
.form-header { 
  display:flex; 
  justify-content:space-between; 
  align-items:center; margin-bottom:18px; }

  .form-header h2 { 
    color:#5e35b1; 
    margin:0; font-size:22px; }

    .form-header .back-btn { 
    background:transparent; 
    border:1px solid #ddd; 
    padding:8px 12px; 
    border-radius:8px; 
    cursor:pointer; }

/* Grid form */
.form-grid { 
  display:grid; 
  grid-template-columns: 1fr 1fr; 
  gap:18px; margin-bottom:14px; }

  .section { background:#fbf9ff; 
    border-radius:10px; 
    padding:14px; 
    border-left:4px solid #6a11cb; }

/* Labels & inputs */
.label { font-size:13px; color:#444; margin-bottom:6px; display:block; }
.input, select { width:100%; padding:10px 12px; border-radius:8px; border:1px solid #e6e6ee; font-size:14px; outline:none; box-sizing:border-box; }
.input:focus, select:focus { box-shadow:0 6px 20px rgba(106,17,203,0.06); border-color:#6a11cb; }

/* full width row */
.full-row { grid-column: 1 / -1; display:flex; gap:12px; align-items:center; }

/* small inline */
.inline { display:flex; gap:10px; align-items:center; }
.inline .input { width:120px; }

/* computed display */
.result-box { background:linear-gradient(90deg,#fff,#f6f0ff); border-radius:10px; padding:12px; text-align:center; border:1px solid rgba(106,17,203,0.08); }
.result-box .val { font-size:20px; font-weight:700; color:#5e35b1; }

/* tombol */
.actions { display:flex; gap:12px; justify-content:flex-end; margin-top:14px; }
.btn-primary { background:linear-gradient(135deg,#6a11cb,#f7971e); color:#fff; padding:10px 16px; border:none; border-radius:10px; cursor:pointer; font-weight:600; }
.btn-secondary { background:#f3f3f6; color:#333; padding:10px 14px; border-radius:10px; border:1px solid #e8e8ee; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; }

/* hint text */
.hint { font-size:12px; color:#666; margin-top:6px; }

.panduan-box {
  background: #f9f7ff;
  border: 1px solid rgba(106,17,203,0.1);
  border-radius: 10px;
  padding: 10px 14px;
  margin-top: 8px;
  font-size: 13px;
  color: #444;
}
.panduan-box strong {
  color: #5e35b1;
  font-size: 13px;
  display: block;
  margin-bottom: 6px;
}
.panduan-box ul {
  list-style: none;
  margin: 0;
  padding: 0;
}
.panduan-box li {
  margin-bottom: 4px;
  line-height: 1.4;
}
.panduan-box li span {
  font-weight: 600;
  color: #f7971e;
}

/* responsive */
@media (max-width:900px) {
  .form-grid { grid-template-columns: 1fr; }
  .inline .input { width:100px; }
  .main-content { margin-left:70px; padding:18px; }
}
</style>
</head>
<body>
<div class="main-content">
  <div class="content-card">
    <div class="form-header">
      <div>
        <h2><?= $editing ? "Edit Penilaian" : "Tambah Penilaian Baru" ?></h2>
        <div class="hint"><?= $pegawai ? htmlspecialchars($pegawai['nama_lengkap']) . " — NIP: " . htmlspecialchars($nip) : "Pegawai tidak ditemukan" ?></div>
      </div>
      <div>
        <button class="back-btn" onclick="window.location.href='detail_penilaian.php?nip=<?= urlencode($nip) ?>'">Kembali</button>
      </div>
    </div>

    <?php if (!empty($error)): ?>
      <div style="background:#ffecec;border-left:4px solid #e53935;padding:10px;border-radius:8px;margin-bottom:12px;color:#7b1b1b;">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" id="formNilai">
      <!-- kirim id jika edit -->
      <?php if ($editing): ?>
        <input type="hidden" name="id" value="<?= intval($id) ?>">
      <?php endif; ?>
      <input type="hidden" name="nip" value="<?= htmlspecialchars($nip) ?>">

      <div class="form-grid">
        <!-- Periode -->
        <div>
          <label class="label">Bulan</label>
          <select name="bulan" id="bulan" class="input" required>
            <option value="">-- Pilih Bulan --</option>
            <?php
            $bulanList = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            foreach ($bulanList as $b) {
              $sel = ($pref['bulan'] === $b) ? 'selected' : '';
              echo "<option value=\"$b\" $sel>$b</option>";
            }
            ?>
          </select>
        </div>

        <div>
          <label class="label">Tahun</label>
          <input name="tahun" type="number" min="2000" max="2100" class="input" value="<?= htmlspecialchars($pref['tahun']) ?>" required>
        </div>

        <!-- Kedisiplinan group -->
        <div class="section full-row">
          <strong style="display:block;margin-bottom:8px;">Kedisiplinan (DIS) — input data kehadiran</strong>
          <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <div>
              <label class="label">Jumlah Hari Efektif</label>
              <input name="jumlah_hari_efektif" id="jumlah_hari_efektif" type="number" class="input" min="0" value="<?= htmlspecialchars($pref['jumlah_hari_efektif']) ?>" required>
            </div>

            <div>
              <label class="label">Jumlah Hari Kerja</label>
              <input 
                id="jumlah_hari_kerja" 
                name="jumlah_hari_kerja" 
                type="number" 
                class="input" 
                min="0" 
                value="<?= htmlspecialchars($pref['jumlah_hari_kerja']) ?>" 
                readonly 
              >
            </div>

            <div>
              <label class="label">Lupa Absen</label>
              <input name="lupa_absen" id="lupa_absen" type="number" class="input" min="0" value="<?= htmlspecialchars($pref['lupa_absen']) ?>" required>
            </div>

            <div style="min-width:180px;">
              <label class="label">Hasil Nilai Kedisiplinan (DIS)</label>
              <div class="result-box">
                <div class="val" id="dis_value"><?= number_format($pref['nilai_kedisiplinan'], 2) ?></div>
                <div class="hint">
                  Nilai dihitung otomatis: (Hari Efektif − Lupa Absen = Jumlah Hari Kerja) → (Jumlah Hari Kerja / Hari Efektif) × 100% × 100
                </div>
              </div>
              <input type="hidden" name="nilai_kedisiplinan" id="nilai_kedisiplinan" value="<?= htmlspecialchars($pref['nilai_kedisiplinan']) ?>">
            </div>
          </div>
        </div>

<!-- Nilai lain -->
<div class="section">
  <label class="label">Kinerja (10-100)</label>
  <input name="kinerja" id="kinerja" type="number" class="input" min="10" max="100" value="<?= htmlspecialchars($pref['kinerja']) ?>" required>
  <div class="panduan-box">
    <strong>Panduan Penilaian:</strong>
    <ul>
      <li><span>10–30</span> — Kurang: Sering lalai dalam melaksanakan tugas dan butuh pengawasan intens.</li>
      <li><span>31–50</span> — Cukup: Mampu menyelesaikan tugas dasar, namun belum konsisten.</li>
      <li><span>51–70</span> — Baik: Bekerja dengan baik namun masih butuh arahan sesekali.</li>
      <li><span>71–85</span> — Sangat Baik: Bekerja dengan inisiatif tinggi dan hasil stabil.</li>
      <li><span>86–100</span> — Istimewa: Selalu melebihi target dan menjadi teladan bagi rekan kerja.</li>
    </ul>
  </div>
</div>

<div class="section">
  <label class="label">Sikap (10-100)</label>
  <input name="sikap" id="sikap" type="number" class="input" min="10" max="100" value="<?= htmlspecialchars($pref['sikap']) ?>" required>
  <div class="panduan-box">
    <strong>Panduan Penilaian:</strong>
    <ul>
      <li><span>10–30</span> — Kurang: Sering bersikap negatif atau tidak sopan.</li>
      <li><span>31–50</span> — Cukup: Sopan tapi belum mampu menjaga konsistensi etika kerja.</li>
      <li><span>51–70</span> — Baik: Berperilaku sopan dan menghargai rekan kerja.</li>
      <li><span>71–85</span> — Sangat Baik: Menunjukkan empati dan mampu menjaga hubungan kerja harmonis.</li>
      <li><span>86–100</span> — Istimewa: Menjadi contoh dalam etika, sopan santun, dan komunikasi kerja.</li>
    </ul>
  </div>
</div>

<div class="section">
  <label class="label">Kepemimpinan (10-100)</label>
  <input name="kepemimpinan" id="kepemimpinan" type="number" class="input" min="10" max="100" value="<?= htmlspecialchars($pref['kepemimpinan']) ?>" required>
  <div class="panduan-box">
    <strong>Panduan Penilaian:</strong>
    <ul>
      <li><span>10–30</span> — Kurang: Tidak mampu mengarahkan tim dan sulit mengambil keputusan.</li>
      <li><span>31–50</span> — Cukup: Kadang mampu memimpin tapi belum konsisten.</li>
      <li><span>51–70</span> — Baik: Dapat memimpin dengan arahan dasar dan menjaga kerja tim.</li>
      <li><span>71–85</span> — Sangat Baik: Mampu memberi contoh, memotivasi, dan mengatur tim secara efektif.</li>
      <li><span>86–100</span> — Istimewa: Menjadi pemimpin panutan, visioner, dan strategis.</li>
    </ul>
  </div>
</div>

<div class="section">
  <label class="label">Loyalitas (10-100)</label>
  <input name="loyalitas" id="loyalitas" type="number" class="input" min="10" max="100" value="<?= htmlspecialchars($pref['loyalitas']) ?>" required>
  <div class="panduan-box">
    <strong>Panduan Penilaian:</strong>
    <ul>
      <li><span>10–30</span> — Kurang: Kurang komitmen terhadap pekerjaan dan sering abai pada tanggung jawab.</li>
      <li><span>31–50</span> — Cukup: Melaksanakan pekerjaan tapi belum menunjukkan dedikasi tinggi.</li>
      <li><span>51–70</span> — Baik: Menunjukkan komitmen dan tanggung jawab pada pekerjaan.</li>
      <li><span>71–85</span> — Sangat Baik: Setia, berintegritas, dan berinisiatif dalam menjaga nama baik instansi.</li>
      <li><span>86–100</span> — Istimewa: Memiliki dedikasi luar biasa dan loyalitas tanpa syarat terhadap instansi.</li>
    </ul>
  </div>
</div>

<div class="section">
  <label class="label">IT (10-100)</label>
  <input name="it" id="it" type="number" class="input" min="10" max="100" value="<?= htmlspecialchars($pref['it']) ?>" required>
  <div class="panduan-box">
    <strong>Panduan Penilaian:</strong>
    <ul>
      <li><span>10–30</span> — Kurang: Kesulitan mengoperasikan perangkat dasar dan sering salah input.</li>
      <li><span>31–50</span> — Cukup: Dapat menggunakan aplikasi dasar namun butuh bantuan.</li>
      <li><span>51–70</span> — Baik: Mampu menjalankan fungsi utama perangkat kerja digital.</li>
      <li><span>71–85</span> — Sangat Baik: Cepat beradaptasi dengan sistem baru dan membantu rekan kerja.</li>
      <li><span>86–100</span> — Istimewa: Ahli dalam penggunaan teknologi, menjadi referensi bagi tim.</li>
    </ul>
  </div>
</div>

<div class="section full-row">
  <label class="label">Masukan Atasan</label>
  <textarea 
    name="masukan_atasan" 
    id="masukan_atasan" 
    class="input" 
    rows="4" 
    placeholder="Tuliskan masukan, catatan, atau rekomendasi dari atasan di sini..."
    required><?= htmlspecialchars($editData['masukan_atasan'] ?? '') ?></textarea>
</div>


        <!-- Rata-rata akhir -->
        <div class="full-row">
          <div style="flex:1">
            <label class="label">Rata-rata Akhir (otomatis)</label>
            <div class="result-box">
              <div style="font-size:13px;color:#666;">Rata-rata dari 6 aspek</div>
              <div class="val" id="rata_value"><?= number_format($pref['nilai_kedisiplinan'] + 0 ? round((($pref['nilai_kedisiplinan'] + $pref['kinerja'] + $pref['sikap'] + $pref['kepemimpinan'] + $pref['loyalitas'] + $pref['it'])/6),2) : 0, 2) ?></div>
            </div>
      </div>

      <div class="actions">
        <button type="submit" name="simpan" class="btn-primary"><?= $editing ? "Update Penilaian" : "Simpan Penilaian" ?></button>
        <a href="detail_penilaian.php?nip=<?= urlencode($nip) ?>" class="btn-secondary">Batal / Kembali</a>
      </div>
    </form>

  </div>
</div>

<script>
/* === JS: Hitung kedisiplinan & rata-rata === */
function clamp(n, a, b) {
  return Math.max(a, Math.min(b, n));
}

function toFixedNumber(n, decimals) {
  return Number(Math.round(n + 'e' + decimals) + 'e-' + decimals).toFixed(decimals);
}

function calcDIS() {
  const he = parseFloat(document.getElementById('jumlah_hari_efektif').value) || 0;
  const la = parseFloat(document.getElementById('lupa_absen').value) || 0;

  let dis = 0;
  let jumlahHariKerja = 0;

  // Hanya hitung kalau dua-duanya sudah diisi
  if (he > 0 && la >= 0 && document.getElementById('lupa_absen').value !== '') {
    jumlahHariKerja = he - la;
    dis = (jumlahHariKerja / he) * 100; // rumus sesuai contoh: hasil 94.95
    if (!isFinite(dis)) dis = 0;
  } else {
    dis = 0;
  }

  // update kolom jumlah hari kerja di tampilan
  const jhkInput = document.getElementById('jumlah_hari_kerja');
  if (jhkInput) {
    jhkInput.value = jumlahHariKerja > 0 ? jumlahHariKerja : '';
  }

  // batasi antara 0–100
  dis = clamp(dis, 0, 100);
  dis = parseFloat(dis.toFixed(2));

  document.getElementById('dis_value').textContent = toFixedNumber(dis, 2);
  document.getElementById('nilai_kedisiplinan').value = dis;
  return dis;
}


function calcRata() {
  const dis = parseFloat(document.getElementById('nilai_kedisiplinan').value) || 0;
  const kinerja = parseFloat(document.getElementById('kinerja').value) || 0;
  const sikap = parseFloat(document.getElementById('sikap').value) || 0;
  const kep = parseFloat(document.getElementById('kepemimpinan').value) || 0;
  const loy = parseFloat(document.getElementById('loyalitas').value) || 0;
  const it = parseFloat(document.getElementById('it').value) || 0;

  const rata = ((dis + kinerja + sikap + kep + loy + it) / 6) || 0;
  document.getElementById('rata_value').textContent = toFixedNumber(clamp(rata, 0, 100), 2);
  return rata;
}

// trigger on input
const inputs = ['jumlah_hari_efektif', 'lupa_absen', 'kinerja', 'sikap', 'kepemimpinan', 'loyalitas', 'it'];
inputs.forEach(id => {
  const el = document.getElementById(id);
  if (el) el.addEventListener('input', () => {
    calcDIS();
    calcRata();
  });
});

// initial calc on load (prefill)
window.addEventListener('load', () => {
  calcDIS();
  calcRata();
});

// form submit: validasi dasar
document.getElementById('formNilai').addEventListener('submit', function(e) {
  const he = parseFloat(document.getElementById('jumlah_hari_efektif').value) || 0;
  if (he <= 0) {
    alert('Jumlah Hari Efektif harus lebih dari 0.');
    e.preventDefault();
    return;
  }
  calcDIS();
  calcRata();
});
</script>
</body>
</html>
