<?php
require_once 'config/config.php';

if (!isLoggedIn()) {
    redirectToLogin();
}

$user = getUserData();
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitize($_POST['nama'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $no_telp = sanitize($_POST['no_telp'] ?? '');
    $pesan = sanitize($_POST['pesan'] ?? '');
    
    if (!empty($nama) && !empty($email) && !empty($pesan)) {
        $insert_query = "INSERT INTO kontak (nama_pengirim, email_pengirim, no_telp, pesan) 
                       VALUES ('$nama', '$email', '$no_telp', '$pesan')";
        
        if ($conn->query($insert_query)) {
            $success = 'Pesan Anda telah terkirim! Kami akan menghubungi Anda segera.';
        } else {
            $error = 'Gagal mengirim pesan: ' . $conn->error;
        }
    } else {
        $error = 'Semua field harus diisi!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak - UKK Learning Platform</title>
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

        /* ============== CONTACT CONTAINER ============== */
        .contact-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .contact-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .contact-header h1 {
            color: #2E86AB;
            font-size: 36px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .contact-header p {
            color: #666;
            font-size: 16px;
        }

        /* ============== CONTACT CARDS ============== */
        .contact-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 50px;
        }

        .contact-card {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
        }

        .contact-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.35);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }

        .contact-card-icon {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .contact-card h3 {
            color: #2E86AB;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .contact-card p {
            color: #666;
            font-size: 14px;
            word-break: break-all;
        }

        /* ============== CONTACT FORM ============== */
        .contact-form-section {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
        }

        .contact-form-section h2 {
            color: #2E86AB;
            margin-bottom: 30px;
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .contact-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            color: #2E86AB;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group input,
        .form-group textarea {
            padding: 12px 15px;
            border: 1px solid rgba(46, 134, 171, 0.3);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.95);
            border-color: #2E86AB;
            box-shadow: 0 0 0 3px rgba(46, 134, 171, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 150px;
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
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
            .contact-header h1 {
                font-size: 28px;
            }
            
            .contact-form-section {
                padding: 25px;
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

    <!-- CONTACT CONTENT -->
    <div class="contact-container">
        <!-- Header -->
        <div class="contact-header">
            <h1><i class="fas fa-envelope" style="color: #4ECDC4;"></i> Hubungi Kami</h1>
            <p>Kami siap membantu Anda. Silakan hubungi kami melalui formulir di bawah ini atau gunakan informasi kontak yang tersedia.</p>
        </div>

        <!-- Contact Cards -->
        <div class="contact-cards">
            <div class="contact-card">
                <div class="contact-card-icon">📧</div>
                <h3>Email</h3>
                <p>info@ukklearning.com</p>
            </div>
            
            <div class="contact-card">
                <div class="contact-card-icon">📞</div>
                <h3>Telepon</h3>
                <p>+62 812-3456-7890</p>
            </div>
            
            <div class="contact-card">
                <div class="contact-card-icon">📍</div>
                <h3>Alamat</h3>
                <p>Jl. Pendidikan No. 123, Kota Anda</p>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="contact-form-section">
            <h2><i class="fas fa-paper-plane"></i> Kirim Pesan</h2>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form class="contact-form" method="POST">
                <div class="form-group">
                    <label for="nama"><i class="fas fa-user"></i> Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" required>
                </div>

                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="no_telp"><i class="fas fa-phone"></i> Nomor Telepon</label>
                    <input type="tel" id="no_telp" name="no_telp">
                </div>

                <div class="form-group">
                    <label for="pesan"><i class="fas fa-message"></i> Pesan</label>
                    <textarea id="pesan" name="pesan" required></textarea>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Kirim Pesan
                </button>
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
        
        // Sound effect for submit button
        document.querySelector('.btn-submit').addEventListener('click', function() {
            playSound();
        });
        
        function playSound() {
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.frequency.value = 800;
                oscillator.type = 'sine';
                
                gainNode.gain.setValueAtTime(0.2, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
                
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.1);
            } catch(e) {
                console.log('Audio tidak didukung');
            }
        }
    </script>
</body>
</html>