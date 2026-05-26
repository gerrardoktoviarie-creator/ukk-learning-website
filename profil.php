<?php
require_once 'config/config.php';

if (!isLoggedIn()) {
    redirectToLogin();
}

$user = getUserData();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = sanitize($_POST['nama_lengkap'] ?? '');
    $no_induk = sanitize($_POST['no_induk'] ?? '');
    $kelas = sanitize($_POST['kelas'] ?? '');
    $no_telp = sanitize($_POST['no_telp'] ?? '');
    $alamat = sanitize($_POST['alamat'] ?? '');
    
    $user_id = $_SESSION['user_id'];
    
    $update_query = "UPDATE users SET 
                    nama_lengkap = '$nama_lengkap',
                    no_induk = '$no_induk',
                    kelas = '$kelas',
                    no_telp = '$no_telp',
                    alamat = '$alamat'
                    WHERE id = $user_id";
    
    if ($conn->query($update_query)) {
        $success = 'Data berhasil diperbarui!';
        $_SESSION['user_nama'] = $nama_lengkap;
        $user = getUserData();
    } else {
        $error = 'Gagal memperbarui data: ' . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - UKK Learning Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #e0f4ff 0%, #d4e8ff 100%);
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
        }

        /* ============== NAVBAR ============== */
        .navbar {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 15px 30px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            color: #2E86AB;
            font-weight: 700;
            font-size: 20px;
        }

        .navbar-brand img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(46, 134, 171, 0.1);
            padding: 5px;
            border: 2px solid #2E86AB;
        }

        .navbar-menu {
            display: flex;
            align-items: center;
            gap: 30px;
            list-style: none;
        }

        .navbar-menu a {
            color: #2E86AB;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            padding: 5px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .navbar-menu a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transition: width 0.3s ease;
        }

        .navbar-menu a:hover::after {
            width: 100%;
        }

        .navbar-menu a:hover {
            color: #764ba2;
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 8px 15px;
            background: rgba(46, 134, 171, 0.1);
            border-radius: 20px;
            transition: all 0.3s ease;
            position: relative;
        }

        .user-profile:hover {
            background: rgba(46, 134, 171, 0.2);
        }

        .user-profile img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: 2px solid #2E86AB;
        }

        .user-profile-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid rgba(46, 134, 171, 0.2);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
            display: none;
            min-width: 200px;
            margin-top: 10px;
        }

        .user-profile-dropdown.show {
            display: block;
        }

        .user-profile-dropdown a,
        .user-profile-dropdown button {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 12px 20px;
            border: none;
            background: none;
            color: #2E86AB;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .user-profile-dropdown a:hover,
        .user-profile-dropdown button:hover {
            background: rgba(46, 134, 171, 0.1);
            padding-left: 25px;
        }

        .logout-btn {
            color: #ff6b6b !important;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* ============== PROFILE CONTAINER ============== */
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
        }

        /* ============== PROFILE CARD ============== */
        .profile-card {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 30px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .profile-card img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto 20px;
            border: 3px solid #2E86AB;
            object-fit: cover;
        }

        .profile-card h2 {
            color: #2E86AB;
            margin-bottom: 10px;
            font-size: 22px;
        }

        .profile-card p {
            color: #666;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .profile-card .status {
            display: inline-block;
            padding: 6px 12px;
            background: rgba(51, 217, 178, 0.2);
            color: #33d9b2;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 15px;
        }

        /* ============== PROFILE FORM ============== */
        .profile-form-section {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
        }

        .profile-form-section h3 {
            color: #2E86AB;
            margin-bottom: 30px;
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #2E86AB;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid rgba(46, 134, 171, 0.3);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.95);
            border-color: #2E86AB;
            box-shadow: 0 0 0 3px rgba(46, 134, 171, 0.1);
        }

        .form-group select option {
            background: #333;
            color: #fff;
        }

        .form-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn-save {
            flex: 1;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-reset {
            flex: 1;
            padding: 12px 30px;
            background: rgba(255, 255, 255, 0.2);
            color: #2E86AB;
            border: 1px solid #2E86AB;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-reset:hover {
            background: rgba(46, 134, 171, 0.1);
        }

        /* ============== ALERTS ============== */
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease-out;
        }

        .alert-success {
            background: rgba(51, 217, 178, 0.2);
            color: #33d9b2;
            border: 1px solid #33d9b2;
        }

        .alert-error {
            background: rgba(255, 107, 107, 0.2);
            color: #ff6b6b;
            border: 1px solid #ff6b6b;
        }

        /* ============== FOOTER ============== */
        footer {
            background: rgba(46, 134, 171, 0.1);
            border-top: 1px solid rgba(46, 134, 171, 0.2);
            padding: 30px;
            text-align: center;
            color: #666;
            margin-top: 50px;
        }

        /* ============== ANIMATIONS ============== */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ============== RESPONSIVE ============== */
        @media (max-width: 768px) {
            .profile-container {
                grid-template-columns: 1fr;
            }
            
            .profile-card {
                position: static;
            }
            
            .profile-form-section {
                padding: 25px;
            }
            
            .form-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="dashboard.php" class="navbar-brand">
                <img src="assets/img/logo.png" alt="Logo">
                <span>UKK Learning</span>
            </a>
            
            <ul class="navbar-menu">
                <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="mapel.php"><i class="fas fa-book"></i> Mapel</a></li>
                <li><a href="kontak.php"><i class="fas fa-envelope"></i> Kontak</a></li>
            </ul>
            
            <div class="navbar-user">
                <div class="user-profile">
                    <img src="assets/img/<?php echo htmlspecialchars($user['foto_profile']); ?>" alt="Profile">
                    <span><?php echo htmlspecialchars(substr($user['nama_lengkap'], 0, 10)); ?></span>
                    
                    <div class="user-profile-dropdown">
                        <a href="profil.php">
                            <i class="fas fa-user-circle"></i> Profil
                        </a>
                        <a href="auth/logout.php" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- PROFILE CONTAINER -->
    <div class="profile-container">
        <!-- Profile Card -->
        <div class="profile-card">
            <img src="assets/img/<?php echo htmlspecialchars($user['foto_profile']); ?>" alt="Profile">
            <h2><?php echo htmlspecialchars($user['nama_lengkap']); ?></h2>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
            <p style="color: #888; font-size: 13px;">
                <i class="fas fa-calendar"></i> Terdaftar: <?php echo date('d M Y', strtotime($user['created_at'])); ?>
            </p>
            <div class="status">
                <i class="fas fa-check-circle"></i> Aktif
            </div>
        </div>

        <!-- Profile Form -->
        <div class="profile-form-section">
            <h3><i class="fas fa-user-edit"></i> Edit Profil</h3>
            
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
            
            <form method="POST" class="profile-form">
                <div class="form-group">
                    <label for="nama_lengkap"><i class="fas fa-user"></i> Nama Lengkap</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($user['nama_lengkap']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="no_induk"><i class="fas fa-id-card"></i> Nomor Induk Siswa</label>
                    <input type="text" id="no_induk" name="no_induk" value="<?php echo htmlspecialchars($user['no_induk'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="kelas"><i class="fas fa-graduation-cap"></i> Kelas</label>
                    <select id="kelas" name="kelas">
                        <option value="">-- Pilih Kelas --</option>
                        <option value="X" <?php echo $user['kelas'] === 'X' ? 'selected' : ''; ?>>Kelas X</option>
                        <option value="XI" <?php echo $user['kelas'] === 'XI' ? 'selected' : ''; ?>>Kelas XI</option>
                        <option value="XII" <?php echo $user['kelas'] === 'XII' ? 'selected' : ''; ?>>Kelas XII</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="no_telp"><i class="fas fa-phone"></i> Nomor Telepon</label>
                    <input type="tel" id="no_telp" name="no_telp" value="<?php echo htmlspecialchars($user['no_telp'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="alamat"><i class="fas fa-map-marker-alt"></i> Alamat</label>
                    <textarea id="alamat" name="alamat" rows="4"><?php echo htmlspecialchars($user['alamat'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-buttons">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                    <button type="reset" class="btn-reset">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- FOOTER -->
    <footer>
        <p>&copy; 2024 UKK Learning Platform. All rights reserved.</p>
    </footer>

    <script>
        const userProfile = document.querySelector('.user-profile');
        const profileDropdown = document.querySelector('.user-profile-dropdown');
        
        if (userProfile && profileDropdown) {
            userProfile.addEventListener('click', function(e) {
                e.stopPropagation();
                profileDropdown.classList.toggle('show');
            });
            
            document.addEventListener('click', function() {
                profileDropdown.classList.remove('show');
            });
        }
    </script>
</body>
</html>