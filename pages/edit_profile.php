<?php
require_once '../config/config.php';

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
    <title>Edit Profile - UKK Learning Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .edit-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .edit-card {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
        }

        .edit-card h2 {
            color: #2E86AB;
            margin-bottom: 30px;
            font-size: 28px;
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
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-cancel {
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
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-cancel:hover {
            background: rgba(46, 134, 171, 0.1);
        }

        .alert {
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-error {
            background: rgba(255, 107, 107, 0.2);
            color: #ff6b6b;
            border: 1px solid #ff6b6b;
        }

        .alert-success {
            background: rgba(51, 217, 178, 0.2);
            color: #33d9b2;
            border: 1px solid #33d9b2;
        }
    </style>
</head>
<body>
    <!-- Include Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="../index.php" class="navbar-brand">
                <img src="../assets/img/logo.png" alt="Logo">
                <span>UKK Learning</span>
            </a>
            
            <ul class="navbar-menu">
                <li><a href="../index.php#home"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="../index.php#mapel"><i class="fas fa-book"></i> Mapel</a></li>
                <li><a href="../index.php#gallery"><i class="fas fa-images"></i> Galeri</a></li>
                <li><a href="../index.php#contact"><i class="fas fa-envelope"></i> Kontak</a></li>
            </ul>
            
            <div class="navbar-user">
                <div class="user-profile">
                    <img src="../assets/img/<?php echo htmlspecialchars($user['foto_profile']); ?>" alt="Profile">
                    <span><?php echo htmlspecialchars(substr($user['nama_lengkap'], 0, 10)); ?></span>
                    
                    <div class="user-profile-dropdown">
                        <a href="profile.php">
                            <i class="fas fa-user-circle"></i> Profile
                        </a>
                        <a href="edit_profile.php">
                            <i class="fas fa-edit"></i> Edit Profile
                        </a>
                        <button onclick="location.href='../auth/logout.php'" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="edit-container">
        <div class="edit-card">
            <h2><i class="fas fa-user-edit"></i> Edit Profile</h2>
            
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
            
            <form method="POST" class="edit-form">
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
                    <a href="profile.php" class="btn-cancel">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <footer style="text-align: center; padding: 30px; color: #666; margin-top: 50px;">
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