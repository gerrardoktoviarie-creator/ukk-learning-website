<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ukk_learning_db');

// Base URL
define('BASE_URL', 'http://localhost/ukk-learning-website/');

// Create Database Connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check Connection
    if ($conn->connect_error) {
        die("Connection Failed: " . $conn->connect_error);
    }
    
    // Set Charset
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Session Configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserData() {
    global $conn;
    if (isLoggedIn()) {
        $user_id = $_SESSION['user_id'];
        $query = "SELECT * FROM users WHERE id = $user_id";
        $result = $conn->query($query);
        return $result->fetch_assoc();
    }
    return null;
}

function redirectToLogin() {
    header('Location: ' . BASE_URL . 'auth/login.php');
    exit();
}

function redirectToDashboard() {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

function sanitize($data) {
    global $conn;
    return $conn->real_escape_string(htmlspecialchars($data, ENT_QUOTES, 'UTF-8'));
}

function passwordHash($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function passwordVerify($password, $hash) {
    return password_verify($password, $hash);
}

?>