<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';
require INCLUDE_PATH . 'sidebar.php';


// Ambil filter dari query string
$filter_nama = isset($_GET['nama']) ? trim($_GET['nama']) : '';
$filter_nip  = isset($_GET['nip'])  ? trim($_GET['nip'])  : '';
$from        = isset($_GET['from']) ? trim($_GET['from']) : '';
$to          = isset($_GET['to'])   ? trim($_GET['to'])   : '';

// build where
$where = [];
$params = [];
$types = '';

if ($filter_nama !== '') {
    $where[]  = "p.nama_lengkap LIKE ?";
    $params[] = '%' . $filter_nama . '%';
    $types   .= 's';
}
if ($filter_nip !== '') {
    $where[]  = "peng.nip = ?";
    $params[] = $filter_nip;
    $types   .= 's';
}
if ($from !== '') {
    $where[]  = "peng.tanggal_kejadian >= ?";
    $params[] = $from;
    $types   .= 's';
}
if ($to !== '') {
    $where[]  = "peng.tanggal_kejadian <= ?";
    $params[] = $to;
    $types   .= 's';
}

$sql = "SELECT peng.*, p.nama_lengkap 
        FROM pengaduan peng
        LEFT JOIN pegawai p ON p.nip = peng.nip";

if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY peng.tanggal_pengaduan DESC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

function e($v){ return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Admin • Kelola Pengaduan</title>
<link rel="stylesheet" href="<?= BASE_URL ?>1_css/sidebar.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
:root{
  --bg-start: #f9f9ff;
  --bg-end: #f0e5ff;
  --accent-1: #5e35b1;
  --accent-2: #6a11cb;
  --accent-3: #2575fc;
  --muted: #6c757d;
  --card-grad: linear-gradient(145deg,#ffffff,#f3e9ff);
  --glass: rgba(255,255,255,0.9);
  --shadow-1: 0 8px 30px rgba(100,50,200,0.06);
}

*{box-sizing:border-box}
body{
  font-family:'Poppins',sans-serif;
  margin:0;
  background: linear-gradient(135deg,var(--bg-start),var(--bg-end));
  color:#222;
  -webkit-font-smoothing:antialiased;
  -moz-osx-font-smoothing:grayscale;
}
.main-content{
  margin-left:240px;
  padding:34px;
  min-height:100vh;
}

/* Header */
.header{
  display:flex;
  justify-content:space-between;
  align-items:flex-start;
  gap:16px;
  margin-bottom:22px;
}
.header-left h1{
  margin:0;
  font-size:26px;
  color:var(--accent-1);
  font-weight:700;
}
.header-left p{ margin:6px 0 0; color:var(--muted); }

/* primary generate button */
.btn-generate{
  background: linear-gradient(90deg,var(--accent-2),var(--accent-3));
  color:#fff;
  border:none;
  padding:10px 16px;
  border-radius:12px;
  box-shadow:0 10px 30px rgba(106,17,203,0.12);
  cursor:pointer;
  font-weight:600;
}

/* Card */
.card{
  background: var(--card-grad);
  border-radius:18px;
  padding:18px;
  box-shadow: var(--shadow-1);
}

/* Filter row */
.filter-row{
  display:flex;
  gap:12px;
  align-items:center;
  flex-wrap:wrap;
}
.input {
  padding:10px 12px;
  border-radius:10px;
  border:1px solid #e6e9ef;
  background:#fff;
  font-size:14px;
  min-width:160px;
  transition:.18s;
}
.input:focus{ outline:none; box-shadow:0 8px 24px rgba(69,123,157,0.08); border-color:var(--accent-3); }

/* Buttons */
.btn{
  padding:10px 14px;
  border-radius:10px;
  border:none;
  cursor:pointer;
  font-weight:700;
}
.btn-filter{ background: linear-gradient(135deg,#6a11cb,#2575fc); color:#fff; box-shadow:0 8px 22px rgba(69,123,157,0.08); }
.btn-reset{ background:#adb5bd; color:#fff; }

/* Table */
.table-wrap{ margin-top:18px; overflow:auto; border-radius:12px; }
.table{
  width:100%;
  border-collapse:collapse;
  min-width:1000px;
  font-size:14px;
}
.table thead th{
  text-align:left;
  padding:14px;
  background: linear-gradient(90deg,var(--accent-1),#2b1b5b);
  color:#fff;
  position:sticky; top:0;
  font-weight:700;
}
.table tbody td{
  padding:12px 14px;
  vertical-align:middle;
  border-bottom:1px solid #f0eef6;
  background:transparent;
}
.table tbody tr{
  transition: all .18s ease;
  background:#fff;
  border-radius:10px;
}
.table tbody tr:hover{
  background: linear-gradient(90deg,#fbfbff,#f6f0ff);
  transform: translateY(-6px);
  box-shadow:0 16px 40px rgba(80,40,160,0.05);
}

/* Badge */
.badge{
  display:inline-block;
  padding:6px 12px;
  border-radius:999px;
  font-weight:700;
  font-size:12px;
  color:#fff;
}
.badge-pending{ background:#f9a825; }    /* gold */
.badge-approved{ background:#43a047; }   /* green */
.badge-rejected{ background:#e53946; }   /* red */

/* Action buttons */
.action-btn{
  border-radius:12px;
  padding:8px 12px;
  font-weight:700;
  border:none;
  cursor:pointer;
  color:#fff;
  margin-right:8px;
  font-size:13px;
}
.action-approve{ background: linear-gradient(90deg,#2ebf9b,#2a9d8f); box-shadow:0 8px 20px rgba(42,157,143,0.08); }
.action-reject { background: linear-gradient(90deg,#ff5f6d,#e63946); box-shadow:0 8px 20px rgba(230,57,70,0.08); }

/* small muted */
.small-muted{ color:var(--muted); font-size:13px; }

/* Modal */
.modal-bg{
  position:fixed; inset:0; display:none;
  background:rgba(8,10,20,0.42);
  align-items:center; justify-content:center; z-index:2000;
}
.modal-card{
  width:460px; max-width:94%;
  border-radius:14px;
  padding:18px;
  background: linear-gradient(145deg,#ffffff,#f6f0ff);
  box-shadow:0 20px 50px rgba(30,10,80,0.2);
}
.modal-card h3{ margin:0 0 10px; color:var(--accent-1); }
.modal-card textarea{ width:100%; min-height:120px; padding:12px; border-radius:10px; border:1px solid #e6e9ef; resize:vertical; }

/* responsive */
@media(max-width:900px){
  .header{ flex-direction:column; align-items:flex-start; gap:10px; }
  .table{ font-size:13px; min-width:800px; }
  .input{ min-width:120px; }
}
</style>
</head>
<body>

<div class="main-content">
  <div class="header">
    <div class="header-left">
      <h1>Kelola Pengaduan</h1>
      <p class="small-muted">Tinjau laporan lupa absen, setujui atau tolak dengan alasan yang jelas.</p>
    </div>
    <div class="header-right">
      <button class="btn-generate" onclick="window.location.href='pengaduan_export.php?<?= htmlentities($_SERVER['QUERY_STRING']) ?>'">Buat Dokumen</button>
    </div>
  </div>

  <div class="card">
    <form method="get" class="filter-row" style="align-items:center;">
      <input class="input" type="text" name="nama" placeholder="Cari nama pegawai..." value="<?= e($filter_nama) ?>">
      <input class="input" type="text" name="nip" placeholder="Cari NIP..." value="<?= e($filter_nip) ?>">
      <label class="small-muted">From <input class="input" type="date" name="from" value="<?= e($from) ?>"></label>
      <label class="small-muted">To <input class="input" type="date" name="to" value="<?= e($to) ?>"></label>

      <button type="submit" class="btn btn-filter">Filter</button>
      <a href="../pengaduan_action.php" class="btn btn-reset" style="text-decoration:none;">Reset</a>

      <div style="flex:1"></div>
      <div class="small-muted" style="font-size:13px;">
        Menampilkan hasil: <strong><?= $result->num_rows ?></strong>
      </div>
    </form>

    <div class="table-wrap" style="margin-top:18px;">
      <table class="table" aria-describedby="Daftar pengaduan">
        <thead>
          <tr>
            <th style="width:48px">#</th>
            <th>Nama / NIP</th>
            <th style="min-width:220px">Jenis Laporan</th>
            <th style="width:140px">Tgl Kejadian</th>
            <th>Keterangan</th>
            <th style="width:90px">Bukti</th>
            <th style="width:130px">Status</th>
            <th style="width:180px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $no=1; while($row=$result->fetch_assoc()): ?>
            <tr>
              <td><?= $no++ ?></td>

              <td>
                <div style="font-weight:700; color:#2b1b5b;"><?= e($row['nama_lengkap'] ?: '-') ?></div>
                <div class="small-muted"><?= e($row['nip']) ?></div>
              </td>

              <td>
                <?php if($row['jenis_laporan'] === 'Lainnya'): ?>
                  <div style="font-weight:600">Lainnya</div>
                  <div class="small-muted"><?= e($row['jenis_laporan_custom']) ?></div>
                <?php else: ?>
                  <?= e($row['jenis_laporan']) ?>
                <?php endif; ?>
              </td>

              <td><?= e($row['tanggal_kejadian']) ?></td>

              <td style="max-width:360px; white-space:pre-wrap;"><?= nl2br(e($row['keterangan'])) ?></td>

              <td>
                <?php if($row['bukti']): ?>
                  <a href="../uploads/pengaduan/<?= rawurlencode($row['bukti']) ?>" target="_blank" class="small-muted">Lihat</a>
                <?php else: echo '-'; endif; ?>
              </td>

              <td>
                <?php if($row['status'] === 'pending'): ?>
                  <span class="badge badge-pending">Pending</span>
                <?php elseif($row['status'] === 'approved'): ?>
                  <span class="badge badge-approved">Approved</span>
                <?php else: ?>
                  <span class="badge badge-rejected">Rejected</span><br>
                  <div class="small-muted" style="margin-top:6px; max-width:160px;"><?= e($row['alasan_penolakan']) ?></div>
                <?php endif; ?>
              </td>

              <td>
                <?php if($row['status'] === 'pending'): ?>
                  <button class="action-btn action-approve" onclick="confirmApprove(<?= $row['id'] ?>)">Approve</button>
                  <button class="action-btn action-reject" onclick="openRejectModal(<?= $row['id'] ?>)">Reject</button>
                <?php else: ?>
                  <div class="small-muted">—</div>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>

<!-- Modal Reject -->
<div class="modal-bg" id="modalReject" role="dialog" aria-modal="true" aria-hidden="true">
  <div class="modal-card" role="document">
    <h3>Masukkan Alasan Penolakan</h3>
    <p class="small-muted" style="margin-top:6px; margin-bottom:12px;">
      Alasan ini akan terlihat oleh pegawai.
    </p>
    <textarea 
      id="rejectReason" 
      placeholder="Jelaskan alasan penolakan..." 
      required>
    </textarea>

    <div style="text-align:right; margin-top:12px;">
      <button class="btn btn-reset" onclick="closeRejectModal()">Batal</button>
      <button class="btn btn-filter" onclick="submitReject()">Kirim</button>
    </div>
  </div>
</div>

<script>
let rejectId = null;

/* ========== APPROVE ACTION ========== */
function confirmApprove(id){
  if (!confirm('Yakin menyetujui pengaduan ini?')) return;
  sendAction(id, 'approve');
}

/* ========== REJECT MODAL OPEN/CLOSE ========== */
function openRejectModal(id){
  rejectId = id;
  document.getElementById('rejectReason').value = '';
  const modal = document.getElementById('modalReject');
  modal.style.display = 'flex';
  modal.setAttribute('aria-hidden', 'false');
}

function closeRejectModal(){
  rejectId = null;
  const modal = document.getElementById('modalReject');
  modal.style.display = 'none';
  modal.setAttribute('aria-hidden', 'true');
}

/* ========== SUBMIT REJECT ========== */
function submitReject(){
  const alasan = document.getElementById('rejectReason').value.trim();

  if(!alasan){
    alert('Alasan penolakan wajib diisi.');
    return;
  }

  sendAction(rejectId, 'reject', alasan);
  closeRejectModal();
}

/* ========== AJAX: APPROVE / REJECT (FINAL FIX) ========== */
function sendAction(id, action, alasan = '') {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../pengaduan_action.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {

                let res;
                try {
                    res = JSON.parse(xhr.responseText);
                } catch (e) {
                    alert("Respon server tidak valid.");
                    console.error(xhr.responseText);
                    return;
                }

                alert(res.message);

                // reload jika responsenya sukses
                if (res.success === true) {
                    location.reload();
                }

            } else {
                alert("Terjadi kesalahan server.");
            }
        }
    };

    // build data
    let data = `id=${id}&action=${action}`;
    if (action === 'reject') {
        data += `&alasan=${encodeURIComponent(alasan)}`;
    }

    xhr.send(data);
  }

  function confirmApprove(id) {
    if (confirm("Setujui pengaduan ini?")) {
        sendAction(id, 'approve');
    }
}

function confirmReject(id) {
    const alasan = prompt("Masukkan alasan penolakan:");
    if (alasan && alasan.trim() !== "") {
        sendAction(id, 'reject', alasan.trim());
    } else {
        alert("Alasan penolakan wajib diisi.");
    }
}

</script>

</body>
</html>
