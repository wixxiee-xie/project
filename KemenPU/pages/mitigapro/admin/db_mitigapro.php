<?php
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';
require INCLUDE_PATH . 'sidebar_mitigapro.php';


// Fungsi aman query
function safe_query($conn, $sql) {
    $res = $conn->query($sql);
    if (!$res) {
        die("Query error: " . $conn->error);
    }
    return $res;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin MitigaPro</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>1_css/sidebar_mitigapro.css">
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
</style>

</head>

<body>

<div class="main-content" id="mainContent">
    <h1 class="page-title">Dashboard Admin MitigaPro</h1>   
    <p>Selamat datang di panel Admin. Silakan pilih menu di sebelah kiri.</p>
</div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.getElementById("mainContent");

    sidebar.classList.toggle("collapsed");
    mainContent.classList.toggle("expanded");
}
</script>

</body>
</html>
