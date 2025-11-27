<?php
session_start();
require_once '../config.php';

$error = ''; // inisialisasi pesan error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  // Ambil data admin berdasarkan username saja
  $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = :username");
  $stmt->execute([':username' => $username]);
  $admin = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($admin) {
    // Jika password cocok (verifikasi hash)
    if (password_verify($password, $admin['password'])) {
      $_SESSION['admin_logged_in'] = true;
      $_SESSION['admin_id'] = $admin['id'];
      $_SESSION['admin_name'] = $admin['name'];
      $_SESSION['admin_username'] = $admin['username'];
      $_SESSION['admin_role'] = $admin['role'];

      header("Location: dashboard.php");
      exit;
    } else {
      $error = "Password salah!";
    }
  } else {
    $error = "Username tidak ditemukan!";
  }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Login Admin - Polibatam</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: url('../assets/images/polibatam-bg.jpg') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Poppins', sans-serif;
      position: relative;
      height: 100vh;
      overflow: hidden;
    }

    body::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(255, 255, 255, 0.25);
      backdrop-filter: blur(8px);
      z-index: 0;
      animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: scale(1.03);
      }

      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    .login-container {
      position: relative;
      z-index: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    .login-box {
      background: rgba(255, 255, 255, 0.25);
      color: #004aad;
      border-radius: 20px;
      padding: 40px;
      width: 380px;
      text-align: center;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.4);
      animation: slideUp 1.1s ease-out forwards;
      transform: translateY(30px);
      opacity: 0;
    }

    @keyframes slideUp {
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .login-box img {
      width: 100px;
      margin-bottom: 20px;
      animation: fadeIn 1.2s ease-in-out;
    }

    .login-box h4 {
      font-weight: 700;
      margin-bottom: 25px;
      color: #000000;
      text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.6);
      font: 25px sans-serif;
      text: center;
    }

    .form-control {
      background: rgba(255, 255, 255, 0.7);
      border: none;
      border-radius: 10px;
      margin-bottom: 15px;
      color: #004aad;
      font-weight: 500;
      transition: 0.3s;
      padding: 10px 15px;
    }

    .form-control:focus {
      box-shadow: 0 0 8px rgba(0, 74, 173, 0.4);
      transform: scale(1.02);
    }

    .form-control::placeholder {
      color: #5a5a5a;
    }

    .btn-login {
      background: #2a6cc2ff;
      color: white;
      font-weight: 600;
      border: none;
      border-radius: 25px;
      padding: 10px 0;
      width: 100%;
      transition: all 0.3s ease;
    }

    .btn-login:hover {
      background: #67c4f3ff;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    .btn-back {
      background: transparent;
      color: #333;
      font-weight: 600;
      border-radius: 25px;
      border: 2px solid #6c757d;
      width: 100%;
      padding: 8px 0;
      margin-top: 10px;
      transition: all 0.3s ease;
    }

    .btn-back:hover {
      background: #6c757d;
      color: #fff;
      transform: translateY(-2px);
    }

    .alert {
      background: rgba(255, 0, 0, 0.8);
      border: none;
      color: white;
      border-radius: 10px;
      font-weight: 500;
    }

    footer {
      position: absolute;
      bottom: 10px;
      width: 100%;
      text-align: center;
      color: #fff;
      font-size: 14px;
      text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
    }
  </style>
</head>

<body>
  <div class="login-container">
    <div class="login-box">
      <img src="../assets/images/kampus-poli.jpg.png" alt="Polibatam Logo">

      <?php if ($error): ?>
        <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <input type="text" name="username" class="form-control" placeholder="Masukkan Username" required>
        <input type="password" name="password" class="form-control" placeholder="Password" required>
        <button type="submit" class="btn-login mt-3">Masuk</button>
      </form>

      <!-- 🔙 Tombol Kembali ke Buku Tamu -->
      <a href="../index.php" class="btn btn-back mt-3">
        <i class="bi bi-house-door"></i> Kembali ke Buku Tamu
      </a>

    </div>
  </div>

  <footer>© 2025 Politeknik Negeri Batam</footer>
</body>

</html>