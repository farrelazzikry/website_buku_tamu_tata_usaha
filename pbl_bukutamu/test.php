<?php
$host = '127.0.0.1';
$port = 3306;
$db   = 'pbl_bukutamu';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    echo "Koneksi BERHASIL!";
} catch (PDOException $e) {
    echo "Koneksi gagal: " . $e->getMessage();
}
