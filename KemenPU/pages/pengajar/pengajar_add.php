<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';

$error_nip = ""; // buat nampung pesan error

// proses simpan data
if (isset($_POST['simpan'])) {
    $nip = trim($_POST['nip'] ?? '');
    $nama_lengkap = trim($_POST['nama_pengajar'] ?? '');
    $jk = $_POST['jenis_kelamin'] ?? '';
    $agama = $_POST['agama'] ?? '';
    $pendidikan = $_POST['pendidikan_terakhir'] ?? '';
    $golongan = $_POST['golongan'] ?? '';
    $tempat = trim($_POST['tempat_lahir'] ?? '');
    $tanggal = $_POST['tanggal_lahir'] ?? '';
    $nohp = trim($_POST['no_hp'] ?? '');
    $email = trim($_POST['email_pengajar'] ?? '');
    $jabatan = trim($_POST['jabatan'] ?? '');
    $unit = trim($_POST['unit_kerja'] ?? '');
    $instansi = trim($_POST['instansi'] ?? '');
    $alamat = trim($_POST['alamat_kantor'] ?? '');
    $npwp = trim($_POST['npwp'] ?? '');
    $status = $_POST['status'] ?? '';

    // Cek apakah NIP sudah ada
    $cek = $conn->prepare("SELECT COUNT(*) FROM pengajar WHERE nip = ?");
    $cek->bind_param("s", $nip);
    $cek->execute();
    $cek->bind_result($jumlah);
    $cek->fetch();
    $cek->close();

    if ($jumlah > 0) {
        $error_nip = "⚠️ NIP sudah terdaftar. Gunakan NIP lain.";
    } else {
    
        // upload foto
    $foto = "";
    if (!empty($_FILES['foto']['name'])) {
        $targetDir = "../../uploads/pengajar/";  
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $fileName = time() . "_" . basename($_FILES["foto"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFilePath);
        $foto = $fileName;
}


        $stmt = $conn->prepare("INSERT INTO pengajar 
            (nip, nama_pengajar, jenis_kelamin, agama, pendidikan_terakhir, golongan, tempat_lahir, tanggal_lahir, no_hp, email_pengajar, jabatan, unit_kerja, instansi, alamat_kantor, npwp, foto, status) 
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssssssssssssss", $nip, $nama_lengkap, $jk, $agama, $pendidikan, $golongan, $tempat, $tanggal, $nohp, $email, $jabatan, $unit, $instansi, $alamat, $npwp, $foto, $status);

        if ($stmt->execute()) {
            header("Location: pengajar.php?success=1");
            exit;
        } else {
            echo "<script>alert('Gagal menyimpan data');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengajar | KemenPU DataSystem</title>
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
    <h2>Tambah Biodata Pengajar</h2>

    <form action="" method="POST" enctype="multipart/form-data" id="formPengajar">
        <div class="form-grid">
            <div class="form-group">
                <label>NIP</label>
                <input 
                    type="text" 
                    name="nip" 
                    maxlength="18"
                    pattern="[0-9]{1,18}" 
                    placeholder="Maksimal 18 digit angka"
                    title="Hanya angka tanpa simbol"
                    oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                    value="<?= htmlspecialchars($_POST['nip'] ?? '') ?>"
                    required>
                <?php if ($error_nip): ?>
                    <div class="error-msg"><?= $error_nip ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_pengajar" required>
            </div>

            <div class="form-group">
                <label>Jenis Kelamin</label>
                <select name="jenis_kelamin" required>
                    <option value="">-- Pilih --</option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </div>

            <div class="form-group">
                <label>Agama</label>
                <select name="agama" required>
                    <option value="">-- Pilih --</option>
                    <option value="ISLAM">ISLAM</option>
                    <option value="KRISTEN KATOLIK">KRISTEN KATOLIK</option>
                    <option value="KRISTEN PROTESTAN">KRISTEN PROTESTAN</option>
                    <option value="HINDU">HINDU</option>
                    <option value="BUDDHA">BUDDHA</option>
                    <option value="KONGHUCU">KONGHUCU</option>
                </select>
            </div>

            <div class="form-group">
                <label>Pendidikan Terakhir</label>
                <select name="pendidikan_terakhir" required>
                    <option value="">-- Pilih --</option>
                    <option value="SMA/SMK">SMA/SMK</option>
                    <option value="Diploma D1">Diploma (D1)</option>
                    <option value="Diploma D2">Diploma (D2)</option>
                    <option value="Diploma D3">Diploma (D3)</option>
                    <option value="Sarjana Terapan D4">Sarjana Terapan (D4)</option>
                    <option value="Sarjana S1">Sarjana (S1)</option>
                    <option value="Magister S2">Magister (S2)</option>
                    <option value="Doctor S3">Doctor (S3)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Golongan</label>
                <select name="golongan" required>
                    <option value="">-- Pilih --</option>
                    <option value="III">III</option>
                    <option value="III/A">III/A</option>
                    <option value="III/B">III/B</option>
                    <option value="III/C">III/C</option>
                    <option value="III/D">III/D</option>
                    <option value="IV">IV</option>
                    <option value="IV/A">IV/A</option>
                    <option value="IV/B">IV/B</option>
                    <option value="IV/C">IV/C</option>
                    <option value="IV/D">IV/D</option>
                    <option value="IV/E">IV/E</option>
                </select>
            </div>

            <div class="form-group">
                <label>Tempat Lahir</label>
                <input type="text" name="tempat_lahir">
            </div>

            <div class="form-group">
                <label>Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir">
            </div>

            <div class="form-group">
                <label>No HP</label>
                <input type="text" name="no_hp" oninput="this.value=this.value.replace(/[^0-9]/g,'')" placeholder="Hanya angka">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email_pengajar">
            </div>

            <div class="form-group">
                <label>Jabatan</label>
                <input type="text" name="jabatan">
            </div>

            <div class="form-group">
                <label>Unit Kerja</label>
                <input type="text" name="unit_kerja">
            </div>

            <div class="form-group">
                <label>Instansi</label>
                <input type="text" name="instansi">
            </div>

            <div class="form-group wide">
                <label>Alamat Kantor</label>
                <textarea name="alamat_kantor" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label>Nomor NPWP</label>
                <input type="text" name="npwp" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>

            <div class="form-group wide">
                <label>Foto</label>
                <input type="file" name="foto" accept="image/*" onchange="previewImage(event)">
                <div class="preview-box">
                    <img id="preview" src="#" alt="Preview" style="display:none;">
                </div>
            </div>
        </div>

        <div class="button-group">
            <button type="submit" name="simpan" class="btn-primary">Simpan</button>
            <a href="pengajar.php" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>

<script>
function previewImage(event) {
    const img = document.getElementById('preview');
    img.src = URL.createObjectURL(event.target.files[0]);
    img.style.display = 'block';
}

// === Cek NIP Real-time ===
document.addEventListener('DOMContentLoaded', () => {
    const nipInput = document.querySelector('input[name="nip"]');
    const errorBox = document.createElement('div');
    errorBox.classList.add('error-msg');
    nipInput.insertAdjacentElement('afterend', errorBox);

    nipInput.addEventListener('input', () => {
        const nip = nipInput.value.trim();

        // kosongin pesan kalau belum cukup panjang
        if (nip.length < 5) {
            errorBox.textContent = "";
            return;
        }

        fetch('cek_nip.php?nip=' + nip)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    errorBox.textContent = "⚠️ NIP sudah terdaftar";
                    errorBox.style.color = "#b30000";
                } else {
                    errorBox.textContent = "✅ NIP tersedia";
                    errorBox.style.color = "#007a00";
                }
            })
            .catch(err => console.error('Error:', err));
    });
});
</script>

</body>
</html>
