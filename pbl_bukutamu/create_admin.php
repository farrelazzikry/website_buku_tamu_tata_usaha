<?php
// create_admin.php
// Simpan di C:\xampp\htdocs\pbl_bukutamu\create_admin.php
// Setelah sukses: HAPUS file ini!

require_once __DIR__ . '/config.php'; // pastikan path ini benar

// Ganti nilai berikut sebelum buka di browser (atau biarkan kosong untuk input form)
$default_username = '';
$default_password = '';
$default_name = 'Administrator';

// Jika form disubmit, gunakan nilai dari form. Jika tidak, pakai default.
$username = $_POST['username'] ?? $default_username;
$password = $_POST['password'] ?? $default_password;
$name     = $_POST['name']     ?? $default_name;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (trim($username) === '' || trim($password) === '') {
        $msg = "Username dan password wajib diisi.";
    } else {
        try {
            // cek apakah username sudah ada
            $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = :u LIMIT 1");
            $stmt->execute(['u' => $username]);
            $exists = $stmt->fetch();

            if ($exists) {
                $msg = "Username sudah ada. Pilih username lain atau hapus user lama.";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $ins = $pdo->prepare("INSERT INTO admins (username, password_hash, name) VALUES (:u, :ph, :n)");
                $ins->execute([
                    'u'  => $username,
                    'ph' => $hash,
                    'n'  => $name
                ]);
                $msg = "Akun admin berhasil dibuat. Username: <strong>" . htmlspecialchars($username) . "</strong>";
            }
        } catch (Exception $e) {
            $msg = "Terjadi error: " . $e->getMessage();
        }
    }
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
