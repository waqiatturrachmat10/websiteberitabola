<?php
$host = 'localhost';
$db   = 'skygoal_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (\PDOException $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}

// --- FUNGSI GLOBAL (Letakkan di sini agar terbaca semua file) ---

function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Fungsi untuk menentukan apakah gambar itu URL eksternal atau file lokal
 */
function getImageUrl($path) {
    // Jika path kosong/null, kembalikan placeholder
    if (empty($path)) {
        return 'https://via.placeholder.com/800x400?text=No+Image';
    }

    // Cek apakah path adalah URL (dimulai dengan http:// atau https://)
    if (filter_var($path, FILTER_VALIDATE_URL)) {
        return $path; 
    }
    
    // Jika path lokal, pastikan tidak ada slash ganda
    return $path; 
}
?>