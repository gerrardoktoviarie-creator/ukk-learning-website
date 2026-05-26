<?php
require_once '../config/config.php';

if (!isLoggedIn()) {
    redirectToLogin();
}

$user = getUserData();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - UKK Learning Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .profile-container {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .profile-card {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 30px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
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

        .profile-info {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
        }

        .profile-info h3 {
            color: #2E86AB;
            margin-bottom: 20px;
            font-size: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #666;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-value {
            color: #2E86AB;
            font-weight: 500;
        }

        .btn-edit {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        @media (max-width: 768px) {
            .profile-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Include Navbar from main page -->
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

    <div class="profile-container">
        <div class="profile-card">
            <img src="../assets/img/<?php echo htmlspecialchars($user['foto_profile']); ?>" alt="Profile">
            <h2><?php echo htmlspecialchars($user['nama_lengkap']); ?></h2>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
            <p style="color: #2E86AB; font-weight: 600;"><?php echo htmlspecialchars($user['kelas']); ?></p>
            <a href="edit_profile.php" class="btn-edit">
                <i class="fas fa-edit"></i> Edit Profile
            </a>
        </div>

        <div class="profile-info">
            <h3><i class="fas fa-info-circle"></i> Informasi Lengkap</h3>
            
            <div class="info-row">
                <span class="info-label"><i class="fas fa-user"></i> Nama Lengkap</span>
                <span class="info-value"><?php echo htmlspecialchars($user['nama_lengkap']); ?></span>
            </div>
            
            <div class="info-row">
                <span class="info-label"><i class="fas fa-envelope"></i> Email</span>
                <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
            </div>
            
            <div class="info-row">
                <span class="info-label"><i class="fas fa-id-card"></i> Nomor Induk</span>
                <span class="info-value"><?php echo htmlspecialchars($user['no_induk'] ?: '-'); ?></span>
            </div>
            
            <div class="info-row">
                <span class="info-label"><i class="fas fa-graduation-cap"></i> Kelas</span>
                <span class="info-value"><?php echo htmlspecialchars($user['kelas'] ?: '-'); ?></span>
            </div>
            
            <div class="info-row">
                <span class="info-label"><i class="fas fa-phone"></i> Nomor Telepon</span>
                <span class="info-value"><?php echo htmlspecialchars($user['no_telp'] ?: '-'); ?></span>
            </div>
            
            <div class="info-row">
                <span class="info-label"><i class="fas fa-map-marker-alt"></i> Alamat</span>
                <span class="info-value"><?php echo htmlspecialchars($user['alamat'] ?: '-'); ?></span>
            </div>
            
            <div class="info-row">
                <span class="info-label"><i class="fas fa-calendar"></i> Terdaftar Sejak</span>
                <span class="info-value"><?php echo date('d M Y', strtotime($user['created_at'])); ?></span>
            </div>
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