<?php
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';
require INCLUDE_PATH . 'sidebar.php';

session_start();


// Ambil data rata-rata penilaian per pegawai
$sql = "
    SELECT 
        pg.nip,
        pg.nama_lengkap,
        COALESCE(ROUND(AVG((p.nilai_kedisiplinan + p.kinerja + p.kepemimpinan + p.loyalitas + p.it)/5), 2), 0) AS rata_rata,
        COUNT(p.id_penilaian) AS jumlah_bulan
    FROM pegawai pg
    LEFT JOIN penilaian p ON pg.nip = p.nip
    GROUP BY pg.nip, pg.nama_lengkap
    ORDER BY pg.nama_lengkap ASC
";
$result = $conn->query($sql);
if (!$result) {
    die('Query error: ' . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Penilaian Pegawai</title>
<link rel="stylesheet" href="<?= BASE_URL ?>1_css/sidebar.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #f3e7ff, #ffe3f4);
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

.main-content.collapsed {
  margin-left: 70px;
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

.content-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
}

.content-header h2 {
  font-size: 24px;
  color: #5e35b1;
  font-weight: 600;
}

.btn-tambah {
  background: linear-gradient(135deg, #6a11cb, #f7971e);
  color: #fff;
  border: none;
  padding: 10px 18px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  font-size: 14px;
  transition: all 0.3s ease;
}

.btn-tambah:hover {
  transform: scale(1.05);
  box-shadow: 0 3px 10px rgba(106, 17, 203, 0.3);
}

/* Table modern */
table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
  overflow: hidden;
  border-radius: 10px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.05);
}

thead {
  background: linear-gradient(135deg, #6a11cb, #f7971e);
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

/* Tombol aksi */
.aksi-btn {
  display: flex;
  justify-content: center;
  gap: 8px;
}

.btn-lihat, .btn-tambah-penilaian {
  padding: 6px 12px;
  border: none;
  border-radius: 6px;
  color: white;
  font-size: 13px;
  cursor: pointer;
  transition: 0.2s;
}

.btn-lihat {
  background-color: #6a11cb;
}
.btn-lihat:hover {
  background-color: #4a0f8f;
}

.btn-tambah-penilaian {
  background-color: #f7971e;
}
.btn-tambah-penilaian:hover {
  background-color: #e07b00;
}

/* Responsive */
@media (max-width: 900px) {
  .main-content {
    margin-left: 70px;
    padding: 20px;
  }

  table {
    font-size: 12px;
  }

  .content-header h2 {
    font-size: 20px;
  }
}
</style>
</head>
<body>
<div class="main-content">
  <div class="content-wrapper">
    <div class="content-header">
      <h2>Data Penilaian Pegawai</h2>
    </div>

    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>NIP</th>
          <th>Nama Lengkap</th>
          <th>Jumlah Bulan Dinilai</th>
          <th>Rata-rata Nilai</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($result->num_rows > 0) {
            $no = 1;
            while ($row = $result->fetch_assoc()) {
                $rata_tampil = $row['rata_rata'] > 0 ? $row['rata_rata'] : '-';
                echo "
                <tr>
                  <td>{$no}</td>
                  <td>{$row['nip']}</td>
                  <td>{$row['nama_lengkap']}</td>
                  <td>{$row['jumlah_bulan']} Bulan</td>
                  <td><strong style='color:#5e35b1;'>{$rata_tampil}</strong></td>
                  <td class='aksi-btn'>
                    <button class='btn-lihat' onclick=\"window.location.href='detail_penilaian.php?nip={$row['nip']}'\">Lihat Data</button>
                    <button class='btn-tambah-penilaian' onclick=\"window.location.href='form_penilaian.php?nip={$row['nip']}'\">+ Tambah</button>
                  </td>
                </tr>";
                $no++;
            }
        } else {
            echo "<tr><td colspan='6'>Belum ada data pegawai</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>
