<?php
require 'config.php';
$perPage = 6;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$params = [];
$where = '';
if ($search !== '') {
    $where = "WHERE name LIKE :s OR email LIKE :s OR purpose LIKE :s OR institution LIKE :s";
    $params[':s'] = "%$search%";
}
$stmt = $pdo->prepare("SELECT COUNT(*) FROM guests $where");
$stmt->execute($params);
$total = $stmt->fetchColumn();
$pages = (int)ceil($total / $perPage);
$offset = ($page - 1) * $perPage;
$stmt = $pdo->prepare("SELECT * FROM guests $where ORDER BY created_at DESC LIMIT :off, :lim");
foreach ($params as $k=>$v) $stmt->bindValue($k, $v);
$stmt->bindValue(':off', (int)$offset, PDO::PARAM_INT);
$stmt->bindValue(':lim', (int)$perPage, PDO::PARAM_INT);
$stmt->execute();
$guests = $stmt->fetchAll();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Buku Tamu - Tata Usaha Politeknik Negeri Batam</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="/pbl_bukutamu/">
      <img src="assets/images/logo.png" alt="Polibatam" height="48" style="margin-right:12px">
      <div>
        <div class="fw-bold" style="color:#0b3b5b">Politeknik Negeri Batam</div>
        <small class="text-muted">Buku Tamu — Tata Usaha</small>
      </div>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navmenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navmenu">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
        <li class="nav-item"><a class="nav-link" href="tentang.php">Tentang</a></li>
        <li class="nav-item"><a class="nav-link" href="admin/login.php">Admin</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-4">
  <div class="row">
    <div class="col-md-6">
      <div class="card mb-3">
        <div class="card-body">
          <h5>Isi Buku Tamu</h5>
          <form action="submit.php" method="post" id="guestForm">
            <div class="mb-3">
              <label class="form-label">Nama *</label>
              <input name="name" class="form-control" required maxlength="200">
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input name="email" type="email" class="form-control" maxlength="200">
            </div>
            <div class="mb-3">
              <label class="form-label">No. Telepon</label>
              <input name="phone" class="form-control" maxlength="50">
            </div>
            <div class="mb-3">
              <label class="form-label">Instansi / Jurusan</label>
              <input name="institution" class="form-control" maxlength="200">
            </div>
            <div class="mb-3">
              <label class="form-label">Keperluan *</label>
              <textarea name="purpose" class="form-control" required rows="3"></textarea>
            </div>
            <button class="btn btn-primary">Kirim</button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card mb-3">
        <div class="card-body">
          <h5>Daftar Pengunjung</h5>
          <form class="d-flex mb-2" method="get">
            <input name="q" value="<?=htmlspecialchars($search)?>" class="form-control me-2" placeholder="Cari nama, email, keperluan...">
            <button class="btn btn-outline-secondary">Cari</button>
          </form>

          <?php if (!$guests): ?>
            <p class="text-muted">Belum ada tamu.</p>
          <?php else: ?>
            <ul class="list-group">
              <?php foreach ($guests as $g): ?>
                <li class="list-group-item">
                  <div class="fw-bold"><?=htmlspecialchars($g['name'])?></div>
                  <div class="small text-muted"><?=date('d M Y H:i', strtotime($g['created_at']))?> — <?=htmlspecialchars($g['institution'])?></div>
                  <div><?=nl2br(htmlspecialchars($g['purpose']))?></div>
                </li>
              <?php endforeach; ?>
            </ul>

            <nav class="mt-3">
              <ul class="pagination">
                <?php for ($p=1;$p<=$pages;$p++): ?>
                  <li class="page-item <?=($p==$page)?'active':''?>"><a class="page-link" href="?page=<?=$p?>&q=<?=urlencode($search)?>"><?=$p?></a></li>
                <?php endfor; ?>
              </ul>
            </nav>

          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <footer class="text-center text-muted small mt-4">Politeknik Negeri Batam — Tata Usaha</footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
