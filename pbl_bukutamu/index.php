<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buku Tamu - Polibatam</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Style -->
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

    .navbar-brand img { height: 40px; }

    .card-custom {
      border: none;
      border-radius: 20px;
      background: rgba(255, 255, 255, 0.92);
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
      transition: transform 0.2s ease;
      animation: fadeIn 1s ease;
    }

    .card-custom:hover { transform: translateY(-5px); }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .form-control {
      border-radius: 12px;
      padding-left: 2.5rem;
      border: 1px solid #ddd;
    }

    .form-control:focus {
      border-color: #004aad;
      box-shadow: 0 0 0 0.2rem rgba(0, 74, 173, 0.25);
    }

    .form-icon {
      position: absolute;
      left: 15px;
      top: 11px;
      color: #6c757d;
    }

    .btn-primary {
      background-color: #2a6cc2ff;
      border: none;
      border-radius: 12px;
      transition: 0.3s;
    }

    .btn-primary:hover {
      background-color: #67c4f3ff;
      transform: scale(1.03);
    }

    h4 { color: #004aad; font-weight: 600; }

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
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="assets/images/logo.png" alt="Polibatam" class="me-2">
      <div>
        <div class="fw-bold text-primary">Politeknik Negeri Batam</div>
        <small class="text-muted">Buku Tamu — Tata Usaha</small>
      </div>
    </a>

    <ul class="navbar-nav ms-auto">
      <li class="nav-item"><a class="nav-link fw-semibold" href="index.php">Beranda</a></li>
      <li class="nav-item"><a class="nav-link fw-semibold" href="tentang.php">Tentang</a></li>
      <li class="nav-item"><a class="nav-link fw-semibold" href="admin/login.php">Admin</a></li>
    </ul>
  </div>
</nav>

<!-- Isi Halaman -->
<div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
  <div class="card card-custom p-4" style="max-width: 500px; width: 100%;">
    <h4 class="mb-3 text-center">
      <i class="bi bi-pencil-square me-2"></i>Isi Buku Tamu
    </h4>

    <form method="POST" action="simpan_tamu.php">
      <!-- Nama -->
      <div class="mb-3 position-relative">
        <i class="bi bi-person form-icon"></i>
        <input type="text" name="nama" class="form-control" placeholder="Nama *" required>
      </div>

      <!-- NIM/NIK -->
      <div class="mb-3 position-relative">
        <i class="bi bi-person-vcard form-icon"></i>
        <input type="text" name="nim_nik" class="form-control" placeholder="NIM / NIK *" required>
      </div>

      <!-- Email -->
      <div class="mb-3 position-relative">
        <i class="bi bi-envelope form-icon"></i>
        <input type="email" name="email" class="form-control" placeholder="Email *" required>
      </div>

      <!-- Telepon -->
      <div class="mb-3 position-relative">
        <i class="bi bi-telephone form-icon"></i>
        <input type="number" name="telepon" class="form-control" placeholder="No. Telepon *" required>
      </div>

      <!-- Instansi -->
      <div class="mb-3 position-relative">
        <i class="bi bi-building form-icon"></i>
        <input type="text" name="instansi" class="form-control" placeholder="Masukkan Prodi atau Instansi *" required>
      </div>

      <!-- Keperluan -->
      <div class="mb-3 position-relative">
        <i class="bi bi-chat-left-text form-icon"></i>
        <textarea name="keperluan" class="form-control" placeholder="Keperluan *" rows="3" required></textarea>
      </div>

      <!-- Tombol -->
      <button type="submit" class="btn btn-primary w-100 py-2">
        <i class="bi bi-send me-2"></i>Kirim
      </button>
    </form>
  </div>
</div>

<footer>
  © 2025 Politeknik Negeri Batam — Tata Usaha
</footer>

<!-- ✅ SweetAlert Notifikasi -->
<?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
<script>
Swal.fire({
  icon: 'success',
  title: 'Data Berhasil Disimpan!',
  text: 'Terima kasih telah mengisi buku tamu 😊',
  showConfirmButton: false,
  timer: 2500
});
</script>
<?php endif; ?>

</body>
</html>
