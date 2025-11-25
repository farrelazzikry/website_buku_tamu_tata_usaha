<?php
require_once '../config.php';

// Pastikan parameter ID ada
if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$id = (int) $_GET['id'];

// Hapus data dari tabel tamu
$stmt = $pdo->prepare("DELETE FROM tamu WHERE id = :id");
$stmt->execute(['id' => $id]);

// Kembali ke dashboard setelah hapus
header('Location: dashboard.php');
exit;
?>