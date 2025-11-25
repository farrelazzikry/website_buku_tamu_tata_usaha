<?php
session_start();
require '../config.php';

// Cek apakah admin sudah login (gunakan session yang benar)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->query('SELECT * FROM guests ORDER BY created_at DESC');
$rows = $stmt->fetchAll();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=bukutamu_export_' . date('Ymd_His') . '.csv');

$out = fopen('php://output', 'w');
fputcsv($out, ['id','name','email','phone','institution','purpose','created_at']);

foreach ($rows as $r) {
    fputcsv($out, [$r['id'],$r['name'],$r['email'],$r['phone'],$r['institution'],$r['purpose'],$r['created_at']]);
}

fclose($out);
exit;
?>
