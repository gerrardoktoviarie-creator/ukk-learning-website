<?php
require_once '../config/config.php';

if (!isLoggedIn()) {
    redirectToLogin();
}

$mapel_id = intval($_GET['id'] ?? 0);

if ($mapel_id === 0) {
    redirectToDashboard();
}

// Fetch mapel details
$mapel_query = "SELECT * FROM mapel WHERE id = $mapel_id";
$mapel_result = $conn->query($mapel_query);
if ($mapel_result->num_rows === 0) {
    redirectToDashboard();
}
$mapel = $mapel_result->fetch_assoc();

// Fetch materi for this mapel
$materi_query = "SELECT * FROM materi WHERE mapel_id = $mapel_id ORDER BY id ASC";
$materi_result = $conn->query($materi_query);
$materis = [];
if ($materi_result) {
    while ($row = $materi_result->fetch_assoc()) {
        $materis[] = $row;
    }
}

$user = getUserData();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($mapel['nama_mapel']); ?> - UKK Learning Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .mapel-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
            margin-bottom: 40px;
        }

        .mapel-header h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }

        .mapel-header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .materi-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px 40px;
        }

        .materi-item {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 25px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .materi-item:hover {
            background: rgba(255, 255, 255, 0.35);
            transform: translateX(10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .materi-item h3 {
            color: #2E86AB;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .materi-item p {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .materi-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-materi {
            padding: 8px 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-materi:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .empty-materi {
            text-align: center;
            padding: 60px 20px;
            color: #888;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background: rgba(46, 134, 171, 0.1);
            color: #2E86AB;
            border: 1px solid #2E86AB;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: rgba(46, 134, 171, 0.2);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
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

    <!-- Mapel Header -->
    <div class="mapel-header">
        <h1><?php echo htmlspecialchars($mapel['icon']); ?> <?php echo htmlspecialchars($mapel['nama_mapel']); ?></h1>
        <p><?php echo htmlspecialchars($mapel['deskripsi']); ?></p>
        <p>Guru Pengampu: <?php echo htmlspecialchars($mapel['guru_pengampu']); ?></p>
    </div>

    <!-- Materi Content -->
    <div class="materi-container">
        <a href="../index.php#mapel" class="back-btn">
            <i class="fas fa-arrow-left"></i> Kembali ke Mapel
        </a>

        <h2 style="color: #2E86AB; margin-bottom: 30px;">
            <i class="fas fa-list"></i> Materi Pembelajaran
        </h2>
        
        <?php if (!empty($materis)): ?>
            <?php foreach ($materis as $materi): ?>
                <div class="materi-item">
                    <h3><i class="fas fa-file-alt"></i> <?php echo htmlspecialchars($materi['judul_materi']); ?></h3>
                    <p><?php echo substr(htmlspecialchars($materi['konten']), 0, 100) . '...'; ?></p>
                    
                    <div class="materi-buttons">
                        <?php if ($materi['video_url']): ?>
                            <a href="<?php echo htmlspecialchars($materi['video_url']); ?>" target="_blank" class="btn-materi">
                                <i class="fas fa-video"></i> Lihat Video
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($materi['file_pdf']): ?>
                            <a href="../assets/files/<?php echo htmlspecialchars($materi['file_pdf']); ?>" class="btn-materi" download>
                                <i class="fas fa-download"></i> Download PDF
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-materi">
                <i class="fas fa-file-slash" style="font-size: 40px; margin-bottom: 10px;"></i>
                <p>Belum ada materi untuk mapel ini</p>
            </div>
        <?php endif; ?>
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