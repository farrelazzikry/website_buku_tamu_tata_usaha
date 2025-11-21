<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php'); exit;
}
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$institution = trim($_POST['institution'] ?? '');
$purpose = trim($_POST['purpose'] ?? '');
if ($name === '' || $purpose === '') {
    $_SESSION['error'] = 'Nama dan keperluan harus diisi.';
    header('Location: index.php'); exit;
}
$stmt = $pdo->prepare("INSERT INTO guests (name,email,phone,institution,purpose) VALUES (:name,:email,:phone,:institution,:purpose)");
$stmt->execute([
    ':name' => $name,
    ':email' => $email?:null,
    ':phone' => $phone?:null,
    ':institution' => $institution?:null,
    ':purpose' => $purpose,
]);
header('Location: thankyou.php'); exit;
?>
khbiyvfyf8