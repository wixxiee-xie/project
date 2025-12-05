<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';

header('Content-Type: application/json');

// 1. Validasi method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$action = isset($_POST['action']) ? $_POST['action'] : ''; // FIXED

if (!$id || !in_array($action, ['approve', 'reject'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

// 2. Approve
if ($action === 'approve') {

    $stmt = $conn->prepare("
        UPDATE pengaduan 
        SET status = 'approved', alasan_penolakan = NULL 
        WHERE id = ?
    ");

    $stmt->bind_param("i", $id);
    $ok = $stmt->execute();
    $stmt->close();

    echo json_encode([
        'success' => $ok,
        'message' => $ok ? 'Pengaduan berhasil disetujui.' : 'Gagal melakukan approve.'
    ]);
    exit;
}

// 3. Reject
$alasan = isset($_POST['alasan']) ? trim($_POST['alasan']) : '';

if ($alasan === '') {
    echo json_encode(['success' => false, 'message' => 'Alasan penolakan wajib diisi']);
    exit;
}

$stmt = $conn->prepare("
    UPDATE pengaduan 
    SET status = 'rejected', alasan_penolakan = ? 
    WHERE id = ?
");

$stmt->bind_param("si", $alasan, $id);
$ok = $stmt->execute();
$stmt->close();

echo json_encode([
    'success' => $ok,
    'message' => $ok ? 'Pengaduan berhasil ditolak.' : 'Gagal melakukan reject.'
]);
exit;
