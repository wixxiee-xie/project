<?php
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';

$query = $conn->query("SELECT * FROM dinas");
?>

<h2>Daftar Dinas</h2>
<ul>
<?php while ($row = $query->fetch_assoc()): ?>
    <li>
        <a href="detail_dinas.php?id=<?= $row['id'] ?>">
            <?= $row['nama_dinas'] ?>
        </a>
    </li>
<?php endwhile; ?>
</ul>
