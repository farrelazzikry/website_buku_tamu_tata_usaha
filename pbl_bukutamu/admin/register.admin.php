<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $nama = $_POST['nama_lengkap'] ?? '';

    if ($username && $password) {
        // Enkripsi password dengan aman
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Masukkan ke database
        $stmt = $pdo->prepare("INSERT INTO admins (username, password, nama_lengkap) VALUES (:username, :password, :nama)");
        $stmt->execute([
            ':username' => $username,
            ':password' => $hashedPassword,
            ':nama' => $nama
        ]);

        echo "<script>alert('Admin berhasil dibuat! Silakan login.'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Harap isi semua kolom!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Register Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="card mx-auto" style="max-width: 400px;">
      <div class="card-body">
        <h4 class="text-center mb-3">Buat Akun Admin</h4>
        <form method="POST">
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" class="form-control">
          </div>
          <button type="submit" class="btn btn-primary w-100">Daftar</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
