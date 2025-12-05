<?php
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';
require INCLUDE_PATH . 'sidebar_mitigaprouser.php'; // sidebar

// Ambil slug menu dari URL
$slug = $_GET['menu'] ?? 'beranda';

// Ambil data menu berdasarkan slug
$stmtMenu = $conn->prepare("SELECT id, title FROM mitigapro_menus WHERE slug = ?");
$stmtMenu->bind_param("s", $slug);
$stmtMenu->execute();
$menuData = $stmtMenu->get_result()->fetch_assoc();
$stmtMenu->close();

if (!$menuData) {
    die("Menu tidak ditemukan!");
}

$menu_id = $menuData['id'];
$menu_title = $menuData['title'];

// Ambil data handler yang terhubung dengan menu
$handler_sql = "
    SELECT h.*
    FROM mitigapro_handlers h
    JOIN mitigapro_handler_menu hm ON hm.handler_id = h.id
    WHERE hm.menu_id = ?
";

$stmt = $conn->prepare($handler_sql);
$stmt->bind_param("i", $menu_id);
$stmt->execute();
$handlers = $stmt->get_result();
$stmt->close();

// Ambil data konten untuk menu
$stmtC = $conn->prepare("SELECT * FROM mitigapro_contents WHERE menu_id = ? ORDER BY priority ASC");
$stmtC->bind_param("i", $menu_id);
$stmtC->execute();
$contents = $stmtC->get_result();
$stmtC->close();

?>
<!DOCTYPE html>
<html>
<head>
<title><?= htmlspecialchars($menu_title) ?> - MitigaPro</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>1_css/sidebar_mitigapro.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #eef3f9;
    margin: 0;
}

.main {
    margin-left: 280px;
    transition: margin-left 0.3s ease;
}

/* === MAIN CONTENT === */
.main-content {
  margin-left: 240px;
  padding: 30px;
  transition: margin-left 0.3s ease;
}

.main-content.expanded {
  margin-left: 40px; /* agar konten tetap sejajar rapi ketika sidebar tertutup */
}

/* Judul halaman */
.page-title {
    font-size: 28px;
    font-weight: 600;
    margin-bottom: 24px;
}

/* Card pemisah konten */
.content-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border-left: 6px solid #3b4eff;
}

/* Judul konten */
.content-card h3 {
    margin: 0 0 10px;
    font-size: 20px;
    font-weight: 600;
}

/* Deskripsi */
.content-card p {
    line-height: 1.6;
    margin-bottom: 14px;
}

/* Gambar konten */
.content-image {
    display: block;
    max-width: 70%;
    margin: 14px auto;
    border-radius: 10px;
}

/* Link Button */
.link-btn {
    display: inline-block;
    padding: 10px 16px;
    background: #3b4eff;
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
}
.link-btn:hover {
    background: #2a39c4;
}

/* GRID HANDLER */
.handler-section-title {
    font-size: 22px;
    font-weight: 600;
    margin-bottom: 14px;
}

.handler-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 18px;
}

.handler-card {
    background: #fff;
    padding: 16px;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.07);
    text-align: center;
}

.profile-photo {
    width: 90px;
    height: 90px;
    object-fit: cover;
    border-radius: 50%;
    margin-bottom: 10px;
    border: 3px solid #3b4eff;
}

.handler-name {
    font-size: 16px;
    font-weight: 600;
}

.handler-text {
    font-size: 13px;
    color: #555;
}
</style>

</head>
<body>

<div class="main" id="main-content">

    <!-- JUDUL MENU -->
    <div class="page-title"><?= htmlspecialchars($menu_title) ?></div>

    <!-- ================================
        BAGIAN PROFIL PEGAWAI (HANDLER)
    ================================= -->
    <div class="content-card">
        <h3>Daftar Pengendali Risiko</h3>

        <div class="handler-grid">

            <?php if ($handlers->num_rows == 0): ?>
                <p style="color:#777; margin:0;">Belum ada pengendali risiko untuk menu ini.</p>
            <?php endif; ?>

            <?php while ($h = $handlers->fetch_assoc()): ?>
                <div class="handler-card">

                    <!-- FOTO -->
                    <img class="profile-photo"
                         src="<?= $h['photo'] ?: '/uploads/mitigapro/default.jpg' ?>">

                    <!-- NAMA -->
                    <div class="handler-name"><?= htmlspecialchars($h['name']) ?></div>

                    <div class="handler-text">NIP: <?= htmlspecialchars($h['nip']) ?></div>
                    <div class="handler-text">Jabatan: <?= htmlspecialchars($h['jabatan']) ?></div>
                    <div class="handler-text">Tugas: <?= htmlspecialchars($h['tugas']) ?></div>

                </div>
            <?php endwhile; ?>

        </div>
    </div>

    <!-- ================================
            BAGIAN KONTEN
    ================================= -->
    <?php if ($contents->num_rows == 0): ?>
        <p>Tidak ada konten untuk menu ini.</p>
    <?php endif; ?>

    <?php while ($c = $contents->fetch_assoc()): ?>
        <div class="content-card">

            <!-- JUDUL KONTEN -->
            <h3><?= htmlspecialchars($c['title']) ?></h3>

            <!-- DESKRIPSI -->
            <?php if (!empty($c['description'])): ?>
                <p><?= nl2br(htmlspecialchars($c['description'])) ?></p>
            <?php endif; ?>

            <!-- GAMBAR -->
            <?php if (!empty($c['image'])): ?>
                <img class="content-image"
                     src="<?= htmlspecialchars($c['image']) ?>"
                >
            <?php endif; ?>

            <!-- LINK -->
            <?php if (!empty($c['link'])): ?>
                <a href="<?= htmlspecialchars($c['link']) ?>" target="_blank"
                   class="link-btn">
                    <?= htmlspecialchars($c['link_label'] ?: 'Kunjungi') ?>
                </a>
            <?php endif; ?>

        </div>
    <?php endwhile; ?>

</div>
<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main-content');

    sidebar.classList.toggle('collapsed');
    main.classList.toggle('expanded'); // ← ini membuat main ikut mengecil
}
</script>

</body>
</html>
