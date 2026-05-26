<?php
require_once 'config/config.php';

if (!isLoggedIn()) {
    redirectToLogin();
}

$user = getUserData();
$mapel_id = intval($_GET['id'] ?? 0);

// Fetch mapel details
$mapel_query = "SELECT * FROM mapel WHERE id = $mapel_id OR id > 0";
if ($mapel_id > 0) {
    $mapel_query = "SELECT * FROM mapel WHERE id = $mapel_id";
}
$mapel_result = $conn->query($mapel_query);

if ($mapel_id > 0 && $mapel_result->num_rows === 0) {
    header('Location: dashboard.php');
    exit();
}

$mapels = [];
while ($row = $mapel_result->fetch_assoc()) {
    $mapels[] = $row;
}

$selected_mapel = $mapel_id > 0 ? $mapels[0] : null;

// Fetch materi for selected mapel
$materis = [];
if ($selected_mapel) {
    $materi_query = "SELECT * FROM materi WHERE mapel_id = {$selected_mapel['id']} ORDER BY id ASC";
    $materi_result = $conn->query($materi_query);
    while ($row = $materi_result->fetch_assoc()) {
        $materis[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapel - UKK Learning Platform</title>
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
            animation: slideDown 0.3s ease-out;
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

        /* ============== MAPEL CONTAINER ============== */
        .mapel-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
        }

        /* ============== MAPEL SIDEBAR ============== */
        .mapel-sidebar {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 25px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .sidebar-title {
            color: #2E86AB;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .mapel-list {
            list-style: none;
        }

        .mapel-list li {
            margin-bottom: 10px;
        }

        .mapel-list a {
            display: block;
            padding: 12px 15px;
            background: rgba(46, 134, 171, 0.1);
            color: #2E86AB;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .mapel-list a:hover,
        .mapel-list a.active {
            background: rgba(46, 134, 171, 0.3);
            padding-left: 20px;
            color: #764ba2;
        }

        /* ============== MAPEL CONTENT ============== */
        .mapel-content {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
        }

        .mapel-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }

        .mapel-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .mapel-header .icon {
            font-size: 40px;
        }

        .mapel-header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .mapel-info {
            background: rgba(46, 134, 171, 0.1);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #2E86AB;
            font-weight: 600;
        }

        .info-value {
            color: #666;
        }

        /* ============== MATERI SECTION ============== */
        .materi-section h2 {
            color: #2E86AB;
            margin-bottom: 20px;
            font-size: 22px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .materi-list {
            list-style: none;
        }

        .materi-item {
            background: rgba(46, 134, 171, 0.05);
            border-left: 4px solid #667eea;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 10px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .materi-item:hover {
            background: rgba(46, 134, 171, 0.1);
            transform: translateX(5px);
        }

        .materi-item h3 {
            color: #2E86AB;
            margin-bottom: 8px;
            font-size: 16px;
        }

        .materi-item p {
            color: #666;
            font-size: 13px;
            margin-bottom: 12px;
        }

        .materi-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
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

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #888;
        }

        .empty-state i {
            font-size: 50px;
            margin-bottom: 20px;
            opacity: 0.5;
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
            .mapel-container {
                grid-template-columns: 1fr;
            }
            
            .mapel-sidebar {
                position: static;
            }
            
            .mapel-content {
                padding: 20px;
            }
            
            .mapel-header h1 {
                font-size: 24px;
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

    <!-- MAPEL CONTAINER -->
    <div class="mapel-container">
        <!-- SIDEBAR -->
        <aside class="mapel-sidebar">
            <div class="sidebar-title">
                <i class="fas fa-book"></i> Daftar Mapel
            </div>
            
            <ul class="mapel-list">
                <?php foreach ($mapels as $m): ?>
                    <li>
                        <a href="mapel.php?id=<?php echo $m['id']; ?>" class="<?php echo $selected_mapel && $selected_mapel['id'] === $m['id'] ? 'active' : ''; ?>">
                            <span><?php echo htmlspecialchars($m['icon']); ?></span> <?php echo htmlspecialchars(substr($m['nama_mapel'], 0, 20)); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>

        <!-- CONTENT -->
        <div class="mapel-content">
            <?php if ($selected_mapel): ?>
                <!-- Mapel Header -->
                <div class="mapel-header">
                    <h1>
                        <span class="icon"><?php echo htmlspecialchars($selected_mapel['icon']); ?></span>
                        <?php echo htmlspecialchars($selected_mapel['nama_mapel']); ?>
                    </h1>
                    <p><?php echo htmlspecialchars($selected_mapel['deskripsi']); ?></p>
                </div>

                <!-- Mapel Info -->
                <div class="mapel-info">
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-user"></i> Guru Pengampu</span>
                        <span class="info-value"><?php echo htmlspecialchars($selected_mapel['guru_pengampu']); ?></span>
                    </div>
                </div>

                <!-- Materi Section -->
                <div class="materi-section">
                    <h2><i class="fas fa-list-ul"></i> Materi Pembelajaran</h2>
                    
                    <?php if (!empty($materis)): ?>
                        <ul class="materi-list">
                            <?php foreach ($materis as $materi): ?>
                                <li class="materi-item">
                                    <h3><i class="fas fa-file-alt"></i> <?php echo htmlspecialchars($materi['judul_materi']); ?></h3>
                                    <p><?php echo substr(htmlspecialchars($materi['konten']), 0, 120) . '...'; ?></p>
                                    
                                    <div class="materi-buttons">
                                        <?php if ($materi['video_url']): ?>
                                            <a href="<?php echo htmlspecialchars($materi['video_url']); ?>" target="_blank" class="btn-materi">
                                                <i class="fas fa-video"></i> Video
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($materi['file_pdf']): ?>
                                            <a href="assets/files/<?php echo htmlspecialchars($materi['file_pdf']); ?>" class="btn-materi" download>
                                                <i class="fas fa-download"></i> PDF
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-file-slash"></i>
                            <p>Belum ada materi untuk mapel ini</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-book"></i>
                    <p>Pilih mata pelajaran dari daftar di sebelah kiri</p>
                </div>
            <?php endif; ?>
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