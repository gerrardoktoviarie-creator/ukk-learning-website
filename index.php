<?php
require_once 'config/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirectToLogin();
}

$user = getUserData();

// Fetch all mapel
$mapel_query = "SELECT * FROM mapel ORDER BY id ASC";
$mapel_result = $conn->query($mapel_query);
$mapels = [];
if ($mapel_result) {
    while ($row = $mapel_result->fetch_assoc()) {
        $mapels[] = $row;
    }
}

// Fetch gallery images
$gallery_query = "SELECT * FROM galeri ORDER BY created_at DESC LIMIT 9";
$gallery_result = $conn->query($gallery_query);
$galleries = [];
if ($gallery_result) {
    while ($row = $gallery_result->fetch_assoc()) {
        $galleries[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - UKK Learning Platform</title>
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

        /* ============== MAIN CONTENT ============== */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .welcome-section {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 40px;
            margin-bottom: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
            animation: slideInDown 0.5s ease-out;
        }

        .welcome-section h1 {
            color: #2E86AB;
            font-size: 36px;
            margin-bottom: 10px;
        }

        .welcome-section p {
            color: #666;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .page-title {
            font-size: 32px;
            color: #2E86AB;
            margin-bottom: 30px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* ============== SEARCH & FILTER ============== */
        .search-section {
            margin-bottom: 40px;
        }

        .search-container {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            min-width: 250px;
            padding: 12px 18px;
            border: 1px solid rgba(46, 134, 171, 0.3);
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.95);
            border-color: #2E86AB;
            box-shadow: 0 0 0 3px rgba(46, 134, 171, 0.1);
        }

        /* ============== MAPEL CARDS GRID ============== */
        .mapel-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .mapel-card {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .mapel-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0));
            transition: left 0.5s ease;
        }

        .mapel-card:hover::before {
            left: 100%;
        }

        .mapel-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.35);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }

        .mapel-icon {
            font-size: 50px;
            margin-bottom: 15px;
        }

        .mapel-name {
            font-size: 18px;
            font-weight: 700;
            color: #2E86AB;
            margin-bottom: 10px;
        }

        .mapel-description {
            font-size: 13px;
            color: #555;
            margin-bottom: 15px;
            min-height: 40px;
            line-height: 1.5;
        }

        .mapel-teacher {
            font-size: 12px;
            color: #888;
            margin-bottom: 15px;
            font-style: italic;
        }

        .btn-mapel {
            display: inline-block;
            padding: 10px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-mapel:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        /* ============== GALLERY SECTION ============== */
        .gallery-section {
            margin: 50px 0;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .gallery-item {
            border-radius: 15px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.2);
            aspect-ratio: 1;
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .gallery-item:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .empty-gallery {
            text-align: center;
            padding: 40px;
            color: #888;
        }

        /* ============== CONTACT SECTION ============== */
        .contact-section {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 40px;
            margin: 50px 0;
        }

        .contact-section h2 {
            color: #2E86AB;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .contact-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .contact-form input,
        .contact-form textarea {
            padding: 12px 15px;
            border: 1px solid rgba(46, 134, 171, 0.3);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        .contact-form input:focus,
        .contact-form textarea:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.95);
            border-color: #2E86AB;
            box-shadow: 0 0 0 3px rgba(46, 134, 171, 0.1);
        }

        .btn-submit {
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

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
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
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .mapel-card {
            animation: fadeIn 0.5s ease-out;
        }

        /* ============== RESPONSIVE ============== */
        @media (max-width: 768px) {
            .navbar-menu {
                gap: 15px;
                font-size: 14px;
            }
            
            .page-title {
                font-size: 24px;
            }
            
            .mapel-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
            
            .search-container {
                flex-direction: column;
            }
            
            .search-input {
                min-width: 100%;
            }
            
            .welcome-section {
                padding: 30px 20px;
            }
            
            .welcome-section h1 {
                font-size: 28px;
            }
            
            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
        }

        @media (max-width: 480px) {
            .navbar-container {
                flex-direction: column;
                gap: 15px;
            }
            
            .navbar-menu {
                flex-direction: column;
                gap: 10px;
                width: 100%;
            }
            
            .mapel-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="index.php" class="navbar-brand">
                <img src="assets/img/logo.png" alt="Logo">
                <span>UKK Learning</span>
            </a>
            
            <ul class="navbar-menu">
                <li><a href="#home"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="#mapel"><i class="fas fa-book"></i> Mapel</a></li>
                <li><a href="#gallery"><i class="fas fa-images"></i> Galeri</a></li>
                <li><a href="#contact"><i class="fas fa-envelope"></i> Kontak</a></li>
            </ul>
            
            <div class="navbar-user">
                <div class="user-profile">
                    <img src="assets/img/<?php echo htmlspecialchars($user['foto_profile']); ?>" alt="Profile">
                    <span><?php echo htmlspecialchars(substr($user['nama_lengkap'], 0, 10)); ?></span>
                    
                    <div class="user-profile-dropdown">
                        <a href="pages/profile.php">
                            <i class="fas fa-user-circle"></i> Profile
                        </a>
                        <a href="pages/edit_profile.php">
                            <i class="fas fa-edit"></i> Edit Profile
                        </a>
                        <button onclick="location.href='auth/logout.php'" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <div class="main-container">
        <!-- Welcome Section -->
        <section class="welcome-section" id="home">
            <h1><i class="fas fa-wave-hand" style="color: #FFD93D;"></i> Selamat Datang, <?php echo htmlspecialchars($user['nama_lengkap']); ?>!</h1>
            <p>Selamat datang di platform pembelajaran UKK. Pilih mata pelajaran yang ingin Anda pelajari dan mulai petualangan belajar Anda hari ini!</p>
        </section>

        <!-- Search Section -->
        <section class="search-section">
            <div class="search-container">
                <input type="text" class="search-input" id="searchInput" placeholder="🔍 Cari mata pelajaran...">
            </div>
        </section>

        <!-- Mapel Section -->
        <section id="mapel">
            <h2 class="page-title"><i class="fas fa-book-open" style="color: #667eea;"></i> Mata Pelajaran</h2>
            
            <div class="mapel-grid" id="mapelGrid">
                <?php foreach ($mapels as $mapel): ?>
                    <div class="mapel-card" data-mapel="<?php echo htmlspecialchars(strtolower($mapel['nama_mapel'])); ?>">
                        <div class="mapel-icon"><?php echo htmlspecialchars($mapel['icon']); ?></div>
                        <div class="mapel-name"><?php echo htmlspecialchars($mapel['nama_mapel']); ?></div>
                        <div class="mapel-description"><?php echo htmlspecialchars($mapel['deskripsi']); ?></div>
                        <div class="mapel-teacher">
                            <i class="fas fa-chalkboard-user"></i> <?php echo htmlspecialchars($mapel['guru_pengampu']); ?>
                        </div>
                        <a href="pages/mapel_detail.php?id=<?php echo $mapel['id']; ?>" class="btn-mapel">
                            <i class="fas fa-arrow-right"></i> Buka Mapel
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Gallery Section -->
        <section id="gallery">
            <h2 class="page-title"><i class="fas fa-images" style="color: #FF6B6B;"></i> Galeri Sekolah</h2>
            
            <?php if (!empty($galleries)): ?>
                <div class="gallery-grid">
                    <?php foreach ($galleries as $gallery): ?>
                        <div class="gallery-item">
                            <img src="assets/img/gallery/<?php echo htmlspecialchars($gallery['foto']); ?>" alt="<?php echo htmlspecialchars($gallery['judul']); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-gallery">
                    <i class="fas fa-image" style="font-size: 40px; color: #ccc; margin-bottom: 10px;"></i>
                    <p>Belum ada foto di galeri</p>
                </div>
            <?php endif; ?>
        </section>

        <!-- Contact Section -->
        <section class="contact-section" id="contact">
            <h2><i class="fas fa-envelope" style="color: #4ECDC4;"></i> Hubungi Kami</h2>
            
            <form class="contact-form" method="POST" action="pages/send_contact.php">
                <input type="text" name="nama" placeholder="Nama Lengkap" required>
                <input type="email" name="email" placeholder="Email Anda" required>
                <input type="tel" name="no_telp" placeholder="Nomor Telepon">
                <textarea name="pesan" placeholder="Pesan Anda..." rows="5" required></textarea>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Kirim Pesan
                </button>
            </form>
        </section>
    </div>

    <!-- FOOTER -->
    <footer>
        <p>&copy; 2024 UKK Learning Platform. All rights reserved.</p>
    </footer>

    <script>
        // User Profile Dropdown
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
        
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                filterMapels(this.value);
            });
        }
        
        function filterMapels(searchTerm) {
            const mapelCards = document.querySelectorAll('.mapel-card');
            
            mapelCards.forEach(card => {
                const mapelName = card.querySelector('.mapel-name').textContent.toLowerCase();
                const mapelDesc = card.querySelector('.mapel-description').textContent.toLowerCase();
                
                if (mapelName.includes(searchTerm.toLowerCase()) || mapelDesc.includes(searchTerm.toLowerCase())) {
                    card.style.display = '';
                    card.style.animation = 'fadeIn 0.3s ease-in';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        
        // Sound effect for buttons
        document.querySelectorAll('.btn-mapel, .btn-submit').forEach(button => {
            button.addEventListener('click', function() {
                playSound();
            });
        });
        
        function playSound() {
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.frequency.value = 600;
                oscillator.type = 'sine';
                
                gainNode.gain.setValueAtTime(0.2, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.15);
                
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.15);
            } catch(e) {
                console.log('Audio tidak didukung');
            }
        }
    </script>
</body>
</html>