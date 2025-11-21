<?php
require '../config.php';
if (empty($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: dashboard.php'); exit; }
$stmt = $pdo->prepare('SELECT * FROM guests WHERE id = :id');
$stmt->execute([':id'=>$id]);
$g = $stmt->fetch();
if (!$g) { header('Location: dashboard.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']); $email = trim($_POST['email']); $institution = trim($_POST['institution']); $purpose = trim($_POST['purpose']);
    $stmt = $pdo->prepare('UPDATE guests SET name=:name,email=:email,institution=:inst,purpose=:pur WHERE id=:id');
    $stmt->execute([':name'=>$name,':email'=>$email,':inst'=>$institution,':pur'=>$purpose,':id'=>$id]);
    header('Location: dashboard.php'); exit;
}
?>
<!doctype html><html><head><meta charset="utf-8"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="p-4">
<div class="container"><h3>Edit Tamu</h3>
<form method="post">
  <div class="mb-2"><input name="name" value="<?=htmlspecialchars($g['name'])?>" class="form-control"></div>
  <div class="mb-2"><input name="email" value="<?=htmlspecialchars($g['email'])?>" class="form-control"></div>
  <div class="mb-2"><input name="institution" value="<?=htmlspecialchars($g['institution'])?>" class="form-control"></div>
  <div class="mb-2"><textarea name="purpose" class="form-control"><?=htmlspecialchars($g['purpose'])?></textarea></div>
  <button class="btn btn-primary">Simpan</button>
  <a href="dashboard.php" class="btn btn-secondary">Batal</a>
</form></div>
</body></html>