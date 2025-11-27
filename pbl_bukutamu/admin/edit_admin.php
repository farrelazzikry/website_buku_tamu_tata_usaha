<?php
session_start();
require_once '../config.php'; // Pastikan path sesuai lokasi config.php

// Cek login admin
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Pastikan ada ID admin
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    header('Location: manage_admin.php');
    exit;
}

$id = $_GET['id'];

// Ambil data admin sesuai ID
$stmt = $pdo->prepare("SELECT * FROM admin WHERE id = ?");
$stmt->execute([$id]);
$admin = $stmt->fetch();

if(!$admin){
    header('Location: manage_admin.php');
    exit;
}

// Update data jika form disubmit
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = $_POST['name'] ?? '';
    $username = $_POST['username'] ?? '';
    $role = $_POST['role'] ?? '';

    if(!empty($_POST['password'])){
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE admin SET name=?, username=?, password=?, role=? WHERE id=?");
        $stmt->execute([$name, $username, $password, $role, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE admin SET name=?, username=?, role=? WHERE id=?");
        $stmt->execute([$name, $username, $role, $id]);
    }

    header('Location: manage_admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Admin</title>
<link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<style>
body{font-family:'Segoe UI', Tahoma, Geneva, Verdana,sans-serif;background:#f4f6f8;margin:0;color:#333;}
.container{max-width:500px;margin:50px auto;background:white;padding:30px;border-radius:12px;box-shadow:0 6px 18px rgba(0,0,0,0.08);}
h2{color:#1a73e8;margin-bottom:20px;}
form input, form select, form button{width:100%;margin-bottom:15px;padding:12px;border:1px solid #ccc;border-radius:8px;font-size:1rem;}
form input:focus, form select:focus{outline:none;border-color:#1a73e8;box-shadow:0 0 5px rgba(26,115,232,0.4);}
form button{background:#1a73e8;color:white;border:none;cursor:pointer;padding:12px;font-size:1rem;border-radius:8px;transition:0.3s;}
form button:hover{background:#155ab6;}
.btn-back{display:inline-block;margin-bottom:20px;color:#1a73e8;font-weight:bold;}
</style>
</head>
<body>

<div class="container">
    <a href="manage_admin.php" class="btn-back"><i class='bx bx-left-arrow-alt'></i> Kembali</a>
    <h2>Edit Admin</h2>
    <form method="POST">
        <input type="text" name="name" placeholder="Nama" value="<?= htmlspecialchars($admin['name'] ?? '') ?>" required>
        <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($admin['username'] ?? '') ?>" required>
        <input type="password" name="password" placeholder="Password (isi jika ingin diubah)">
        <select name="role">
            <option value="admin" <?= ($admin['role'] ?? '')=='admin'?'selected':'' ?>>Admin</option>
            <option value="superadmin" <?= ($admin['role'] ?? '')=='superadmin'?'selected':'' ?>>Superadmin</option>
        </select>
        <button type="submit"><i class='bx bx-save'></i> Simpan Perubahan</button>
    </form>
</div>

</body>
</html>
