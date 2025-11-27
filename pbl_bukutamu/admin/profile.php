<?php
session_start();
require_once '../config.php';

// 🔒 Cek login
if (empty($_SESSION['admin_logged_in']) || empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$admin_id = $_SESSION['admin_id'];

// 🔍 Ambil data admin dari DB
$stmt = $pdo->prepare("SELECT * FROM admin WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    echo "<script>alert('Data admin tidak ditemukan!'); window.location='logout.php';</script>";
    exit;
}

// Data untuk tampilan
$admin_name = $admin['nama_lengkap'] ?? '';
$admin_username = $admin['username'] ?? '';

// 📸 Foto profil
$uploadDir = '../uploads/';
$photoPath = $uploadDir . $admin_username . '.jpg';
$photoURL = file_exists($photoPath) ? $photoPath : '../assets/images/default-profile.png';

/* ============================================================
   🔧 PROSES UPDATE PROFIL
   ============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {

    $new_name = trim($_POST['name'] ?? '');
    $new_username = trim($_POST['username'] ?? '');

    if ($new_name === '' || $new_username === '') {
        $error = "Nama dan Username tidak boleh kosong!";
    } else {

        // 📸 Upload foto (jika ada)
        if (!empty($_FILES['photo']['name'])) {
            $targetFile = $uploadDir . $new_username . '.jpg';
            move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile);
            $photoURL = $targetFile;
        }

        // 💾 Update DB
        $stmt = $pdo->prepare("UPDATE admin SET nama_lengkap = ?, username = ? WHERE id = ?");
        $stmt->execute([$new_name, $new_username, $admin_id]);

        // 🔄 Update session
        $_SESSION['admin_username'] = $new_username;
        $_SESSION['admin_name'] = $new_name;

        echo "<script>alert('Profil berhasil diperbarui!'); window.location='profile.php';</script>";
        exit;
    }
}

/* ============================================================
   🔒 UPDATE PASSWORD
   ============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $old_pass = $_POST['old_password'] ?? '';
    $new_pass = $_POST['new_password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    if (!password_verify($old_pass, $admin['password'])) {
        $error = "Password lama salah!";
    } elseif ($new_pass !== $confirm_pass) {
        $error = "Password baru tidak cocok!";
    } else {
        $hash = password_hash($new_pass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE admin SET password = ? WHERE id = ?");
        $stmt->execute([$hash, $admin_id]);
        $success = "Password berhasil diubah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { background-color: #f8f9fa; }
.profile-card { background: #fff; border-radius: 20px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
.profile-photo { width: 140px; height: 140px; object-fit: cover; border-radius: 50%; border: 4px solid #007bff; }
.btn-back { background: #007bff; color: white; border-radius: 25px; font-weight: 600; }
.btn-back:hover { background: #0056b3; }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold text-primary" href="dashboard.php">
      <i class="bi bi-card-checklist me-2"></i> Buku Tamu Admin
    </a>
  </div>
</nav>

<!-- PROFIL -->
<div class="container mt-4 mb-5">
  <a href="dashboard.php" class="btn btn-back mb-3">
    <i class="bi bi-arrow-left-circle me-1"></i> Kembali ke Dashboard
  </a>

  <div class="profile-card p-4">
    <div class="row align-items-center">
      <div class="col-md-4 text-center">
        <img src="<?= htmlspecialchars($photoURL ?? '') ?>" class="profile-photo mb-3">
        <h5 class="fw-bold text-primary"><?= htmlspecialchars($admin_name ?? '') ?></h5>
        <p class="text-muted">@<?= htmlspecialchars($admin_username ?? '') ?></p>
      </div>

      <div class="col-md-8">
        <h5 class="fw-bold mb-3"><i class="bi bi-gear-fill me-2"></i> Pengaturan Profil</h5>

        <?php if (!empty($error)) : ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error ?? '') ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
          <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="name" value="<?= htmlspecialchars($admin_name ?? '') ?>" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" value="<?= htmlspecialchars($admin_username ?? '') ?>" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Foto Profil (Opsional)</label>
            <input type="file" name="photo" class="form-control">
          </div>

          <button type="submit" name="update_profile" class="btn btn-primary w-100">
            <i class="bi bi-save me-1"></i> Simpan Perubahan
          </button>
        </form>
      </div>
    </div>

    <hr class="my-4">

    <!-- GANTI PASSWORD -->
    <div class="col-md-6 mx-auto">
      <h5 class="fw-bold text-center mb-3"><i class="bi bi-lock-fill me-2"></i> Ganti Password</h5>

      <?php if (!empty($success)) : ?>
          <div class="alert alert-success"><?= htmlspecialchars($success ?? '') ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Password Lama</label>
          <input type="password" name="old_password" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Password Baru</label>
          <input type="password" name="new_password" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Konfirmasi Password</label>
          <input type="password" name="confirm_password" class="form-control" required>
        </div>

        <button type="submit" name="update_password" class="btn btn-outline-primary w-100">
          <i class="bi bi-shield-lock me-1"></i> Ubah Password
        </button>
      </form>
    </div>

  </div>
</div>

</body>
</html>
