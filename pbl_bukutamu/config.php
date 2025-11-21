<?php
// config.php - koneksi database
session_start();
$DB_HOST = '127.0.0.1';
$DB_NAME = 'bukutamu_pnbatam';
$DB_USER = 'root';
$DB_PASS = ''; // XAMPP default: kosong

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    die('Koneksi DB gagal: ' . $e->getMessage());
}
?>