<?php
require '../config.php';
if (empty($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $stmt = $pdo->prepare('DELETE FROM guests WHERE id = :id');
    $stmt->execute([':id'=>$id]);
}
header('Location: dashboard.php'); exit;
?>