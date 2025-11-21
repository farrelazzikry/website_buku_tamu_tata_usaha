<?php
require '../config.php';
if (!empty($_SESSION['admin_id'])) { header('Location: dashboard.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    if ($u === '' || $p === '') $error = 'Isi username & password.';
    else {
        $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = :u LIMIT 1');
        $stmt->execute([':u'=>$u]);
        $admin = $stmt->fetch();
        if ($admin && password_verify($p, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            header('Location: dashboard.php'); exit;
        } else $error = 'Login gagal.';
    }
}
?>
<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light d-flex align-items-center" style="height:100vh;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-4">
        <div class="card p-3">
          <h5 class="mb-3">Login Admin</h5>
          <?php if ($error): ?><div class="alert alert-danger"><?=htmlspecialchars($error)?></div><?php endif; ?>
          <form method="post">
            <div class="mb-2"><input name="username" class="form-control" placeholder="Username"></div>
            <div class="mb-3"><input type="password" name="password" class="form-control" placeholder="Password"></div>
            <button class="btn btn-primary w-100">Masuk</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>