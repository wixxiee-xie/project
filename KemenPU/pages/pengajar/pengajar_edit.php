<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';

// Pastikan NIP dikirim lewat URL
if (!isset($_GET['nip'])) {
    header("Location: pengajar.php");
    exit;
}

$nip = $_GET['nip'];

// Ambil data lama pengajar
$stmt = $conn->prepare("SELECT * FROM pengajar WHERE nip = ?");
$stmt->bind_param("s", $nip);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "<h3>Data tidak ditemukan.</h3>";
    exit;
}

// Proses update data
if (isset($_POST['update'])) {
    $nama_lengkap = $_POST['nama_pengajar'];
    $jk = $_POST['jenis_kelamin'];
    $agama = $_POST['agama'];
    $pendidikan = $_POST['pendidikan_terakhir'];
    $golongan = $_POST['golongan'];
    $tempat = $_POST['tempat_lahir'];
    $tanggal = $_POST['tanggal_lahir'];
    $nohp = $_POST['no_hp'];
    $email = $_POST['email_pengajar'];
    $jabatan = $_POST['jabatan'];
    $unit = $_POST['unit_kerja'];
    $instansi = $_POST['instansi'];
    $alamat = $_POST['alamat_kantor'];
    $npwp = $_POST['npwp'];
    $status = $_POST['status'];

    $foto = $data['foto']; // pakai foto lama dulu

    if (!empty($_FILES['foto']['name'])) {

        // path absolut (fix)
        $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/kemenPU/uploads/pengajar/";

        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES["foto"]["name"]);
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFilePath)) {
            $foto = $fileName; // simpan nama foto
        }
    }


    $stmt = $conn->prepare("UPDATE pengajar SET 
        nama_pengajar=?, jenis_kelamin=?, agama=?, pendidikan_terakhir=?, golongan=?, tempat_lahir=?, tanggal_lahir=?, 
        no_hp=?, email_pengajar=?, jabatan=?, unit_kerja=?, instansi=?, alamat_kantor=?, 
        npwp=?, foto=?, status=? 
        WHERE nip=?");

    $stmt->bind_param("sssssssssssssssss", 
        $nama_lengkap, $jk, $agama, $pendidikan, $golongan, $tempat, $tanggal, 
        $nohp, $email, $jabatan, $unit, $instansi, $alamat, 
        $npwp, $foto, $status, $nip
    );

    if ($stmt->execute()) {
        header("Location: pengajar_view.php?nip=$nip&updated=1");
        exit;
    } else {
        echo "<script>alert('Gagal memperbarui data');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengajar | DATA PENGAJAR PELATIHAN</title>
    <style>
            body {
        background-color: #f4f7fb;
        font-family: 'Poppins', sans-serif;
        color: #2c3e50;
    }

    .container {
        width: 90%;
        max-width: 1250px;   /* lebih kecil dari 1250px agar lebih rapi */
        margin: 40px auto;
        padding: clamp(20px, 3vw, 35px); 
        background: white;
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        animation: fadeIn 0.6s ease;
    }

    .container h2 {
        text-align: center;
        color: #225f9c;
        margin-bottom: 30px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px 40px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group.wide {
        grid-column: span 2;
    }

    label {
        font-weight: 600;
        margin-bottom: 8px;
    }

    input, select, textarea {
        border: 1.5px solid #cbd6e2;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 15px;
        outline: none;
        transition: all 0.2s ease-in-out;
    }

    input:focus, select:focus, textarea:focus {
        border-color: #225f9c;
        box-shadow: 0 0 6px rgba(34,95,156,0.25);
    }

    .preview-box {
        margin-top: 10px;
    }

    .preview-box img {
        width: 160px;
        height: 160px;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid #ddd;
    }

    .button-group {
        text-align: center;
        margin-top: 30px;
    }

    .btn-primary, .btn-secondary {
        padding: 12px 30px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        transition: 0.3s ease;
        text-decoration: none;
    }

    .btn-primary {
        background-color: #225f9c;
        color: white;
    }

    .btn-primary:hover {
        background-color: #1a4d7f;
    }

    .btn-secondary {
        background-color: #cbd6e2;
        color: #2c3e50;
        margin-left: 15px;
    }

    .btn-secondary:hover {
        background-color: #b7c5d6;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

        .error-msg {
            color: #b30000;
            font-size: 0.9em;
            margin-top: 5px;
        }
        input[type="text"], input[type="email"], select, textarea {
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 6px 8px;
            width: 100%;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Edit Biodata Pengajar</h2>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="form-group">
                <label>NIP</label>
                <input type="text" name="nip" maxlength="18" value="<?php echo $data['nip']; ?>" readonly>
            </div>

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_pengajar" value="<?php echo htmlspecialchars($data['nama_pengajar']); ?>" required>
            </div>

            <div class="form-group">
                <label>Jenis Kelamin</label>
                <select name="jenis_kelamin" required>
                    <option value="">-- Pilih --</option>
                    <option value="Laki-laki" <?php if($data['jenis_kelamin']=='Laki-laki') echo 'selected'; ?>>Laki-laki</option>
                    <option value="Perempuan" <?php if($data['jenis_kelamin']=='Perempuan') echo 'selected'; ?>>Perempuan</option>
                </select>
            </div>

            <div class="form-group">
                <label>Agama</label>
                <select name="agama" required>
                    <option value="ISLAM" <?php if($data['agama']=='ISLAM') echo 'selected'; ?>>ISLAM</option>
                    <option value="KRISTEN KATOLIK" <?php if($data['agama']=='KRISTEN KATOLIK') echo 'selected'; ?>>KRISTEN KATOLIK</option>
                    <option value="KRISTEN PROTESTAN" <?php if($data['agama']=='KRISTEN PROTESTAN') echo 'selected'; ?>>KRISTEN PROTESTAN</option>
                    <option value="HINDU" <?php if($data['agama']=='HINDU') echo 'selected'; ?>>HINDU</option>
                    <option value="BUDDHA" <?php if($data['agama']=='BUDDHA') echo 'selected'; ?>>BUDDHA</option>
                    <option value="KONGHUCU" <?php if($data['agama']=='KONGHUCU') echo 'selected'; ?>>KONGHUCU</option>
                </select> 
            </div>

            <div class="form-group">
                <label>Pendidikan Terakhir</label>
                <select name= "pendidikan_terakhir" reuqired>
                    <option value="SMA/SMK" <?php if($data['pendidikan_terakhir']=='SMA/SMK') echo 'selected'; ?>>SMA/SMK</option>
                    <option value="Diploma D1" <?php if($data['pendidikan_terakhir']=='Diploma D1') echo 'selected'; ?>>Diploma (D1)</option>
                    <option value="Diploma D2" <?php if($data['pendidikan_terakhir']=='Diploma D2') echo 'selected'; ?>>Diploma (D2)</option>
                    <option value="Diploma D3" <?php if($data['pendidikan_terakhir']=='Diploma D3') echo 'selected'; ?>>Diploma (D3)</option>
                    <option value="Sarjana Terapan D4" <?php if($data['pendidikan_terakhir']=='Sarjana Terapan D4') echo 'selected'; ?>>Sarjana Terapan (D4)</option>
                    <option value="Sarjana S1" <?php if($data['pendidikan_terakhir']=='Sarjana S1') echo 'selected'; ?>>Sarjana (S1)</option>
                    <option value="Magister S2"<?php if($data['pendidikan_terakhir']=='Magister S2') echo 'selected'; ?>>Magister (S2)</option>
                    <option value="Doctor S3" <?php if($data['pendidikan_terakhir']=='Doctor S3') echo 'selected'; ?>>Doctor (S3)</option>
                </select>
                </div>

            <div class="form-group">
                <label>Golongan</label>
                <select name="golongan" required>
                    <option value="III/A" <?php if ($data['golongan']=='III/A') echo 'selected'; ?>>III/A</option>
                    <option value="III/B" <?php if ($data['golongan']=='III/B') echo 'selected'; ?>>III/B</option>
                    <option value="III/C" <?php if ($data['golongan']=='III/C') echo 'selected'; ?>>III/C</option>
                    <option value="III/D" <?php if ($data['golongan']=='III/D') echo 'selected'; ?>>III/D</option>
                    <option value="IV/A" <?php if ($data['golongan']=='IV/A') echo 'selected'; ?>>IV/A</option>
                    <option value="IV/B" <?php if ($data['golongan']=='IV/B') echo 'selected'; ?>>IV/B</option>
                    <option value="IV/C" <?php if ($data['golongan']=='IV/C') echo 'selected'; ?>>IV/C</option>
                    <option value="IV/D" <?php if ($data['golongan']=='IV/D') echo 'selected'; ?>>IV/D/</option>
                    <option value="IV/E" <?php if ($data['golongan']=='IV/E') echo 'selected'; ?>>IV/E</option>
                </select>
            </div>

            <div class="form-group">
                <label>Tempat Lahir</label>
                <input type="text" name="tempat_lahir" value="<?php echo $data['tempat_lahir']; ?>">
            </div>

            <div class="form-group">
                <label>Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" value="<?php echo $data['tanggal_lahir']; ?>">
            </div>

            <div class="form-group">
                <label>No HP</label>
                <input type="text" name="no_hp" value="<?php echo $data['no_hp']; ?>">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email_pengajar" value="<?php echo $data['email_pengajar']; ?>">
            </div>

            <div class="form-group">
                <label>Jabatan</label>
                <input type="text" name="jabatan" value="<?php echo $data['jabatan']; ?>">
            </div>

            <div class="form-group">
                <label>Unit Kerja</label>
                <input type="text" name="unit_kerja" value="<?php echo $data['unit_kerja']; ?>">
            </div>

            <div class="form-group">
                <label>Instansi</label>
                <input type="text" name="instansi" value="<?php echo $data['instansi']; ?>">
            </div>

            <div class="form-group wide">
                <label>Alamat Kantor</label>
                <textarea name="alamat_kantor" rows="3"><?php echo $data['alamat_kantor']; ?></textarea>
            </div>

            <div class="form-group">
                <label>Nomor NPWP</label>
                <input type="text" name="npwp" value="<?php echo $data['npwp']; ?>">
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="aktif" <?php if($data['status']=='aktif') echo 'selected'; ?>>Aktif</option>
                    <option value="nonaktif" <?php if($data['status']=='nonaktif') echo 'selected'; ?>>Nonaktif</option>
                </select>
            </div>

            <div class="form-group wide">
                <label>Foto</label>
                <input type="file" name="foto" accept="image/*" onchange="previewImage(event)">

                <div class="preview-box">
                    <img 
                        id="preview" 
                        src="<?= BASE_URL ?>uploads/pengajar/<?= $data['foto'] ?: 'default.png' ?>"
                        alt="Preview"
                        style="display:block;">
                </div>
            </div>



        <div class="button-group">
            <button type="submit" name="update" class="btn-primary">Perbarui</button>
            <a href="pengajar_view.php?nip=<?php echo $nip; ?>" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>

<script>
function previewImage(event) {
    const img = document.getElementById('preview');
    img.src = URL.createObjectURL(event.target.files[0]);
}
</script>


</body>
</html>
