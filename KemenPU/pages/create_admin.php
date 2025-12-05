<?php
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';

$nama = 'admin_pengajar';
$email = 'bapekom8mks@gmail.com';
$password = password_hash('123456', PASSWORD_DEFAULT);
$role = 'pengajar';

$stmt = mysqli_prepare($conn, "INSERT INTO users (nama,email,password,role) VALUES (?,?,?,?)");
mysqli_stmt_bind_param($stmt, "ssss", $nama, $email, $password, $role);

if (mysqli_stmt_execute($stmt)) {
    echo "Pengajar dibuat: $email (password: 123456).";
} else {
    echo "Gagal: " . mysqli_error($conn);
}
?>
