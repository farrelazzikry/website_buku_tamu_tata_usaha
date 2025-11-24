<?php
session_start();
require_once '../config.php';

// Jika belum login, keluar
if (empty($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'Admin'; // default admin

    if ($name === '' || $username === '' || $password === '') {
        echo "Semua field harus diisi.";
        exit;
    }

    // Hash password
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Simpan ke database
    $stmt = $pdo->prepare("
        INSERT INTO admins (name, username, password, role)
        VALUES (:name, :username, :password, :role)
    ");

    $stmt->execute([
        ':name' => $name,
        ':username' => $username,
        ':password' => $hashed,
        ':role' => $role
    ]);

    header("Location: dashboard.php?success=1");
    exit;
}

echo "Akses langsung tidak diizinkan.";
