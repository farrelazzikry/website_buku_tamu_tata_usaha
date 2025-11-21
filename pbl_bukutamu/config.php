<?php
// CONFIG UNTUK LARAGON
$host = 'localhost';   // Laragon WAJIB pakai localhost
$port = 3306;          // port default Laragon MySQL
$db   = 'pbl_bukutamu';
$user = 'root';        // default user Laragon
$pass = '';            // default password Laragon kosong
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>