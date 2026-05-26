<?php
require_once '../config/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = sanitize($_POST['nama_lengkap'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $no_induk = sanitize($_POST['no_induk'] ?? '');
    $kelas = sanitize($_POST['kelas'] ?? '');
    
    // Validation
    if (empty($nama_lengkap) || empty($email) || empty($password)) {
        $error = 'Semua field harus diisi!';
    } elseif ($password !== $password_confirm) {
        $error = 'Password tidak cocok!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } else {
        // Check if email already exists
        $check_query = "SELECT * FROM users WHERE email = '$email'";
        $check_result = $conn->query($check_query);
        
        if ($check_result->num_rows > 0) {
            $error = 'Email sudah terdaftar!';
        } else {
            // Insert new user
            $hashed_password = passwordHash($password);
            $insert_query = "INSERT INTO users (nama_lengkap, email, password, no_induk, kelas) 
                           VALUES ('$nama_lengkap', '$email', '$hashed_password', '$no_induk', '$kelas')";
            
            if ($conn->query($insert_query)) {
                $success = 'Registrasi berhasil! Silakan login.';
                sleep(2);
                header('Location: login.php');
                exit();
            } else {
                $error = 'Registrasi gagal! ' . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - UKK Learning Platform</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <img src="../assets/img/logo.png" alt="Logo Sekolah" class="logo">
                <h1>Daftar Akun</h1>
                <p>Buat akun baru untuk memulai belajar</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="nama_lengkap"><i class="fas fa-user"></i> Nama Lengkap</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" required>
                </div>

                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="no_induk"><i class="fas fa-id-card"></i> Nomor Induk Siswa</label>
                    <input type="text" id="no_induk" name="no_induk">
                </div>

                <div class="form-group">
                    <label for="kelas"><i class="fas fa-graduation-cap"></i> Kelas</label>
                    <select id="kelas" name="kelas">
                        <option value="">-- Pilih Kelas --</option>
                        <option value="X">Kelas X</option>
                        <option value="XI">Kelas XI</option>
                        <option value="XII">Kelas XII</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="password_confirm"><i class="fas fa-lock"></i> Konfirmasi Password</label>
                    <input type="password" id="password_confirm" name="password_confirm" required>
                </div>

                <button type="submit" class="btn btn-register">
                    <i class="fas fa-user-plus"></i> Daftar
                </button>
            </form>

            <p class="auth-footer">
                Sudah punya akun? <a href="login.php">Login di sini</a>
            </p>
        </div>
    </div>
    <script src="../assets/js/auth.js"></script>
</body>
</html>