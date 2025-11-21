<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tentang - Buku Tamu Polibatam</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      color: #333;
      background: url('assets/images/polibatam-bg.jpg') no-repeat center center fixed;
      background-size: cover;
      position: relative;
      min-height: 100vh;
    }
    body::before {
      content: "";
      position: fixed;
      inset: 0;
      backdrop-filter: blur(8px);
      background-color: rgba(255, 255, 255, 0.7);
      z-index: -1;
    }
    nav.navbar {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(6px);
      box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
    }
    footer {
      text-align: center;
      margin-top: 50px;
      padding: 20px 0;
      color: #666;
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

    <ul class="navbar-nav ms-auto">
      <li class="nav-item"><a class="nav-link fw-semibold" href="index.php">Beranda</a></li>
      <li class="nav-item"><a class="nav-link fw-semibold active" href="tentang.php">Tentang</a></li>
      <li class="nav-item"><a class="nav-link fw-semibold" href="admin/login.php">Admin</a></li>
    </ul>
  </div>
</nav>

<!-- Hero Section (yang tadi dipindah dari beranda) -->
<section class="text-center py-5 bg-light bg-opacity-75 rounded-4 shadow-sm mx-3 mt-4">
  <div class="container">
    <h1 class="fw-bold display-5 mb-3">
      <i class="bi bi-journal-text"></i> Tentang Aplikasi Buku tamu
    </h1>
    <p class="fs-5">
      Aplikasi Buku Tamu Polibatam dibuat untuk mendigitalisasi proses pencatatan tamu di Tata Usaha <br>
      Politeknik Negeri Batam agar lebih efisien, akurat, dan mudah diakses oleh seluruh civitas akademika.<br>
      Silakan isi data kunjungan Anda dengan mudah dan cepat.
    </p>
    <a href="index.php" class="btn btn-dark btn-lg mt-3">
      <i class="bi bi-pencil-square"></i> Isi Buku Tamu Sekarang
    </a>
  </div>
</section>

<footer>
  © 2025 Politeknik Negeri Batam — Tata Usaha
</footer>

</body>
</html>
