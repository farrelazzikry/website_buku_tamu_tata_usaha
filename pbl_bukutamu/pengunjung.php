<?php
include 'config.php'; // sesuaikan path jika file ini di folder lain

// Ambil semua data tamu dari database
$query = $pdo->query("SELECT * FROM tamu ORDER BY tanggal DESC");
$guests = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Pengunjung - Buku Tamu Polibatam</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f7f9fc;
      font-family: 'Segoe UI', sans-serif;
    }
    .navbar {
      background: #fff;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .navbar-brand {
      font-weight: bold;
      color: #0d6efd !important;
    }
    .card {
      border: none;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .table thead {
      background-color: #0d6efd;
      color: white;
    }
    .btn-back {
      background-color: #e9ecef;
      border: none;
      color: #0d6efd;
      font-weight: 500;
      transition: all 0.3s;
    }
    .btn-back:hover {
      background-color: #0d6efd;
      color: #fff;
    }
    footer {
      text-align: center;
      color: #6c757d;
      margin-top: 40px;
      font-size: 0.9rem;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="assets/images/logo.png" alt="Polibatam" class="me-2" style="height:40px;">
      <div>
        <div class="fw-bold text-primary">Politeknik Negeri Batam</div>
        <small class="text-muted">Buku Tamu — Tata Usaha</small>
      </div>
    </a>

    <div class="ms-auto">
      <a href="http://localhost:8080/pbl_bukutamu/index.php" class="nav-link d-inline text-secondary fw-semibold">Beranda</a> 
      <a href="#" class="nav-link d-inline fw-bold text-primary">Pengunjung</a>
      <a href="http://localhost:8080/pbl_bukutamu/tentang.php" class="nav-link d-inline text-secondary fw-semibold">Tentang</a>
      <a href="http://localhost:8080/pbl_bukutamu/admin/login.php" class="nav-link d-inline text-secondary fw-semibold">Admin</a>
    </div>
  </div>
</nav>

<div class="container my-5">
  <div class="card p-4">
    <h3 class="mb-4 fw-bold text-primary"><i class="bi bi-people-fill me-2"></i>Daftar Pengunjung</h3>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>Nama</th>
            <th>Instansi / Prodi</th> 
            <th>Keperluan</th>
            <th>Tanggal</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($guests) > 0): ?>
            <?php foreach ($guests as $row): ?>
              <tr>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= htmlspecialchars($row['instansi']) ?></td>
                <td><?= htmlspecialchars($row['keperluan']) ?></td>
                <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" class="text-center text-muted py-3">Belum ada data pengunjung.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="text-center mt-4">
    <a href="http://localhost:8080/pbl_bukutamu/index.php" class="btn btn-primary">← Kembali ke Beranda</a>
    </div>
  </div>
</div>

<footer>
  © <?= date('Y') ?> Politeknik Negeri Batam — Tata Usaha
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.js"></script>
</body>
</html>