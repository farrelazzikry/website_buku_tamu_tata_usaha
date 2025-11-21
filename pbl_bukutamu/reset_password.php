<?php
require 'config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $new_pass = $_POST["password"];

    // Cek apakah username ada
    $sql = "SELECT * FROM admins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Hash password baru
        $hashed = password_hash($new_pass, PASSWORD_BCRYPT);

        // Update password_hash
        $update = $conn->prepare("UPDATE admins SET password_hash=? WHERE username=?");
        $update->bind_param("ss", $hashed, $username);
        $update->execute();

        $message = "<div class='alert alert-success'>✅ Password berhasil direset!</div>";
    } else {
        $message = "<div class='alert alert-danger'>❌ Username tidak ditemukan!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #0056b3, #007bff);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-family: 'Poppins', sans-serif;
    }
    .card {
      width: 400px;
      background: white;
      color: black;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
      padding: 25px;
    }
  </style>
</head>
<body>
  <div class="card text-center">
    <h3 class="mb-3">🔒 Reset Password Admin</h3>
    <?= $message ?>
    <form method="POST">
      <div class="mb-3">
        <input type="text" class="form-control" name="username" placeholder="Masukkan Username" required>
      </div>
      <div class="mb-3">
        <input type="password" class="form-control" name="password" placeholder="Password Baru" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Reset Password</button>
    </form>
    <p class="mt-3 text-muted small">Setelah reset, kembali ke <a href="login.php">halaman login</a>.</p>
  </div>
</body>
</html>
