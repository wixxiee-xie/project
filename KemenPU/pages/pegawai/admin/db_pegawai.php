<?php
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';
require INCLUDE_PATH . 'sidebar.php';

// Fungsi aman query
function safe_query($conn, $sql) {
    $res = $conn->query($sql);
    if (!$res) {
        die("Query error: " . $conn->error);
    }
    return $res;
}

// Statistik dasar
$totalPegawai = safe_query($conn, "SELECT COUNT(*) AS total FROM pegawai")->fetch_assoc()['total'];

// Rata-rata keseluruhan
$rataTotal = safe_query($conn, "
    SELECT ROUND(AVG((nilai_kedisiplinan + kinerja + kepemimpinan + loyalitas + it)/5), 2) AS rata 
    FROM penilaian
")->fetch_assoc()['rata'];

// Nilai tertinggi & terendah
$tertinggi = safe_query($conn, "
    SELECT p.nama_lengkap, ROUND(AVG((n.nilai_kedisiplinan + n.kinerja + n.kepemimpinan + n.loyalitas + n.it)/5), 2) AS rata
    FROM pegawai p
    JOIN penilaian n ON p.nip = n.nip
    GROUP BY p.nip
    ORDER BY rata DESC
    LIMIT 1
")->fetch_assoc();

$terendah = safe_query($conn, "
    SELECT p.nama_lengkap, ROUND(AVG((n.nilai_kedisiplinan + n.kinerja + n.kepemimpinan + n.loyalitas + n.it)/5), 2) AS rata
    FROM pegawai p
    JOIN penilaian n ON p.nip = n.nip
    GROUP BY p.nip
    ORDER BY rata ASC
    LIMIT 1
")->fetch_assoc();

// Data untuk chart
$chartData = safe_query($conn, "
    SELECT bulan, ROUND(AVG((nilai_kedisiplinan + kinerja + kepemimpinan + loyalitas + it)/5), 2) AS rata_bulanan
    FROM penilaian
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
    <title>Dashboard Penilaian Pegawai</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>1_css/sidebar.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f9f9ff, #f0e5ff);
            color: #333;
            margin: 0;
            display: flex;
        }

        .main-content {
            flex: 1;
            padding: 30px 40px;
            transition: 0.3s;
            min-height: 100vh;
            background: linear-gradient(135deg, #ffffff, #f8f2ff);
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        header h1 {
            font-weight: 600;
            font-size: 26px;
            color: #5e35b1;
        }


        .insight-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .card {
            background: linear-gradient(145deg, #ffffff, #f3e9ff);
            border-radius: 18px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(150, 75, 255, 0.1);
            text-align: center;
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(150, 75, 255, 0.2);
        }

        .card h3 {
            font-size: 18px;
            color: #7b1fa2;
            margin-bottom: 8px;
        }

        .card p {
            font-size: 28px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .card small {
            color: #777;
            font-size: 14px;
        }

        .chart-container {
            background: linear-gradient(145deg, #fff, #f5e9ff);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(100, 50, 200, 0.1);
        }

        canvas {
            max-width: 100%;
            height: 400px !important;
        }

    </style>
</head>
<body>

<div class="main-content">
    <header>
        <h1>Dashboard Penilaian Pegawai</h1>
    </header>

    <div class="insight-cards">
        <div class="card">
            <h3>Total Pegawai</h3>
            <p><?= $totalPegawai ?: 0 ?></p>
        </div>
        <div class="card">
            <h3>Rata-rata Nilai</h3>
            <p><?= $rataTotal ?: '-' ?></p>
        </div>
        <div class="card">
            <h3>Nilai Tertinggi</h3>
            <p><?= $tertinggi['nama_lengkap'] ?? '-' ?></p>
            <small><?= $tertinggi['rata'] ?? '-' ?></small>
        </div>
        <div class="card">
            <h3>Nilai Terendah</h3>
            <p><?= $terendah['nama_lengkap'] ?? '-' ?></p>
            <small><?= $terendah['rata'] ?? '-' ?></small>
        </div>
    </div>

    <div class="chart-container">
        <h3 style="margin-bottom:20px; color:#5e35b1;">Statistik Penilaian Bulanan</h3>
        <canvas id="chartNilai"></canvas>
    </div>
</div>

<script>
function toggleDarkMode() {
    document.body.classList.toggle('dark');
}

// Chart data dari PHP
const labels = <?= json_encode($bulan) ?>;
const dataValues = <?= json_encode($rata) ?>;

// Gradasi warna chart
const ctx = document.getElementById('chartNilai').getContext('2d');
const gradient = ctx.createLinearGradient(0, 0, 0, 400);
gradient.addColorStop(0, 'rgba(155, 89, 182, 0.8)');
gradient.addColorStop(1, 'rgba(255, 159, 67, 0.4)');

const config = {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Rata-rata Nilai Bulanan',
            data: dataValues,
            backgroundColor: gradient,
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#6a11cb',
                titleFont: { size: 14 },
                bodyFont: { size: 13 },
                padding: 10,
                cornerRadius: 8
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(200,200,200,0.2)' },
                ticks: { color: '#666' }
            },
            x: {
                grid: { display: false },
                ticks: { color: '#666' }
            }
        }
    }
};

new Chart(ctx, config);
</script>

</body>
</html>
