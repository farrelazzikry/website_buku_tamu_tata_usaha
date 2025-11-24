<?php
session_start();
require_once '../config.php'; // pastikan path ini sesuai lokasi config.php

// --- Ganti sesuai admin baru yang kamu mau ---
$username = 'nurferli';
$password = '12345';
$nama_lengkap = 'Mohammad Nur Ferli';
$role = 'admin'; // bisa 'admin' atau 'superadmin'
// --------------------------------------------

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    $sql = "INSERT INTO admins (username, password, nama_lengkap, role)
            VALUES (:username, :password, :nama_lengkap, :role)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':username' => $username,
        ':password' => $hashedPassword,
        ':nama_lengkap' => $nama_lengkap,
        ':role' => $role
    ]);

    echo "Admin baru berhasil dibuat!<br>";
    echo "Username: $username<br>";
    echo "Password: $password<br>";
    echo "<a href='login.php'>Klik di sini untuk login</a>";
} catch (PDOException $e) {
    echo "Terjadi error: " . $e->getMessage();
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Create Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h4 class="card-title mb-3">Buat Admin Baru</h4>

          <?php if (!empty($msg)): ?>
            <div class="alert alert-info"><?php echo $msg; ?></div>
          <?php endif; ?>

          <form method="post">
            <div class="mb-2">
              <label class="form-label">Username</label>
              <input name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>
            <div class="mb-2">
              <label class="form-label">Password</label>
              <input name="password" type="password" class="form-control" value="<?php echo htmlspecialchars($password); ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Nama (display)</label>
              <input name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>">
            </div>
            <div class="d-flex justify-content-between">
              <button type="submit" class="btn btn-primary">Buat Admin</button>
              <a href="admin/login.php" class="btn btn-outline-secondary">Ke Halaman Login</a>
            </div>
          </form>

          <hr>
          <small class="text-muted">Setelah akun berhasil dibuat: <strong>HAPUS</strong> file <code>create_admin.php</code> dari server (C:\xampp\htdocs\pbl_bukutamu\create_admin.php).</small>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
 