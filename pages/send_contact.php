<?php
require_once '../config/config.php';

if (!isLoggedIn()) {
    redirectToLogin();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitize($_POST['nama'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $no_telp = sanitize($_POST['no_telp'] ?? '');
    $pesan = sanitize($_POST['pesan'] ?? '');
    
    if (!empty($nama) && !empty($email) && !empty($pesan)) {
        $insert_query = "INSERT INTO kontak (nama_pengirim, email_pengirim, no_telp, pesan) 
                       VALUES ('$nama', '$email', '$no_telp', '$pesan')";
        
        if ($conn->query($insert_query)) {
            header('Location: ../index.php?contact=success');
            exit();
        }
    }
}

header('Location: ../index.php');
exit();
?>