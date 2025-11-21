<?php
require '../config.php';
if (empty($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $stmt = $pdo->prepare("SELECT * FROM guests WHERE name LIKE :s OR email LIKE :s OR purpose LIKE :s ORDER BY created_at DESC");
    $stmt->execute([':s'=>'%'.$q.'%']);
} else {
    $stmt = $pdo->query("SELECT * FROM guests ORDER BY created_at DESC LIMIT 500");
}
$items = $stmt->fetchAll();
?>
<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Dashboard — Admin</h2>
    <div>
      <a href="export.php" class="btn btn-outline-success me-2">Export CSV</a>
      <a href="logout.php" class="btn btn-outline-secondary">Logout</a>
    </div>
  </div>
  <form class="mb-3 d-flex" method="get">
    <input name="q" value="<?=htmlspecialchars($q)?>" class="form-control me-2" placeholder="Cari...">
    <button class="btn btn-primary">Cari</button>
  </form>
  <table class="table table-striped table-bordered">
    <thead><tr><th>#</th><th>Nama</th><th>Email</th><th>Instansi</th><th>Keperluan</th><th>Waktu</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($items as $it): ?>
      <tr>
        <td><?= $it['id'] ?></td>
        <td><?= htmlspecialchars($it['name']) ?></td>
        <td><?= htmlspecialchars($it['email']) ?></td>
        <td><?= htmlspecialchars($it['institution']) ?></td>
        <td><?= nl2br(htmlspecialchars(substr($it['purpose'],0,150))) ?></td>
        <td><?= $it['created_at'] ?></td>
        <td>
          <a href="edit.php?id=<?=$it['id']?>" class="btn btn-sm btn-warning">Edit</a>
          <a href="delete.php?id=<?=$it['id']?>" onclick="return confirm('Hapus?')" class="btn btn-sm btn-danger">Hapus</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>