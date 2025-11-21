<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'] ?? '';
    $nim_nik = $_POST['nim_nik'] ?? '';
    $email = $_POST['email'] ?? '';
    $telepon = $_POST['telepon'] ?? '';
    $instansi = $_POST['instansi'] ?? '';
    $keperluan = $_POST['keperluan'] ?? '';
    $tanggal = date('Y-m-d H:i:s');

    try {
        $sql = "INSERT INTO tamu (nama, nim_nik, email, telepon, instansi, keperluan, tanggal)
                VALUES (:nama, :nim_nik, :email, :telepon, :instansi, :keperluan, :tanggal)";
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':nim_nik', $nim_nik);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telepon', $telepon);
        $stmt->bindParam(':instansi', $instansi);
        $stmt->bindParam(':keperluan', $keperluan);
        $stmt->bindParam(':tanggal', $tanggal);

        $stmt->execute();

        // kembali ke halaman utama dengan pesan sukses
        header("Location: index.php?status=success");
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: index.php");
    exit;
}
?>

