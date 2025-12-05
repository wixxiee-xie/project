<?php
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';
require INCLUDE_PATH . 'fpdf.php';

if (isset($_GET['nip'])) {

    $nip = $_GET['nip'];
    $result = $conn->query("SELECT * FROM pengajar WHERE nip='$nip'");
    $data = $result->fetch_assoc();

    // Ambil data tambahan
    $nama_lengkap_pelatihan = $_GET['nama_lengkap_pelatihan'] ?? '';
    $instansi = $_GET['instansi'] ?? '';
    $tanggal = $_GET['tanggal'] ?? '';

    // Mulai PDF
    $pdf = new FPDF();
    $pdf->AddPage();

    // Header Tetap
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 7, 'DAFTAR BIODATA', 0, 1, 'C');
    $pdf->Cell(0, 7, 'WIDYAISWARA / NARASUMBER / PENCERAMAH', 0, 1, 'C');
    $pdf->Ln(1);

    // Header Dinamis
    foreach ([$nama_lengkap_pelatihan, $instansi, $tanggal] as $text) {
        if ($text) {
            $pdf->Cell(0, 7, strtoupper($text), 0, 1, 'C');
        }
    }
    $pdf->Ln(6);

    // FOTO Pengajar
    if (!empty($data['foto'])) {
        $fotoPath = ROOT_PATH . "uploads/pengajar/" . $data['foto']; // FIXED PATH

        if (file_exists($fotoPath)) {
            $pdf->Image($fotoPath, 160, 40, 30); 
        }
    }

    // Data biodata
    foreach ($data as $key => $value) {
        if (!in_array($key, ['foto', 'created_at', 'updated_at'])) {
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(60, 8, ucwords(str_replace('_', ' ', $key)), 0, 0);

            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(0, 8, ': ' . $value, 0, 1);
        }
    }

    $pdf->Output('D', 'Biodata_' . $data['nama_pengajar'] . '.pdf');
}
?>
