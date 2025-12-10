<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Buku Tamu - Polibatam</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Confetti -->
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

  <!-- Style -->
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      color: #333;
      background: url('assets/images/polibatam-bg.jpg') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
      position: relative;
    }

    body::before {
      content: "";
      position: fixed;
      inset: 0;
      backdrop-filter: blur(8px);
      background-color: rgba(255, 255, 255, 0.7);
      z-index: -1;
    }

    /* Background navbar disesuaikan dengan footer */
    nav.navbar {
      background: #0168bdff; /* warna biru keabu-abuan footer */
      backdrop-filter: blur(6px);
      box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
    }
    /* Warna teks navbar jadi putih */
    nav.navbar a,
    .navbar-brand,
    .navbar-nav .nav-link,
    .navbar-brand span {
      color: #fff !important;
   }
    /* Buat aturan baru */
    .title-text {
      color: #fff !important;   /* Putih */
    font-weight: 700;
    }

    .subtitle-text {
      color: #000 !important;   /* Hitam */
    }
    .navbar-brand img {
      height: 40px;
    }

    .card-custom {
      border: none;
      border-radius: 20px;
      background: rgba(255, 255, 255, 0.92);
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
      animation: fadeInSmooth 0.8s ease-out;
    }

    @keyframes fadeInSmooth {
      from {
        opacity: 0;
        transform: translateY(15px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .form-control {
      border-radius: 12px;
      padding-left: 2.5rem;
      border: 1px solid #ddd;
      transition: border-color 0.2s ease;
    }

    .form-control:focus {
      border-color: #004aad;
      box-shadow: 0 0 0 0.2rem rgba(0, 74, 173, 0.25);
    }

    /* Floating Placeholder */
    .form-control::placeholder {
      transition: 0.3s ease;
      opacity: 0.6;
    }

    .form-control:focus::placeholder,
    .form-control:not(:placeholder-shown)::placeholder {
      transform: translateY(-14px);
      font-size: 0.8rem;
      opacity: 0.4;
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

    h4 {
      color: #004aad;
      font-weight: 600;
    }

    footer {
      background-color:  #0168bdff;
      color: #fff;
      padding: 40px 0;
      margin-top: 50px;
    }

    footer .container {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 20px;
    }

    footer .logo-section img {
      width: 120px;
      margin-bottom: 15px;
    }

    footer .logo-section h3 {
      letter-spacing: 4px;
      font-weight: 700;
      margin-bottom: 10px;
      color: #fff;
      border-bottom: 2px solid #fff;
      display: inline-block;
      padding-bottom: 5px;
    }

    footer .contact-section,
    footer .faq-section {
      max-width: 400px;
    }

    footer .contact-section p,
    footer .faq-section p {
      margin-bottom: 10px;
      font-size: 0.95rem;
      color: #e0e0e0;
    }

    footer .faq-section h4 {
      font-weight: 700;
      margin-bottom: 10px;
      color: #fff;
    }

    footer .faq-section button {
      background: #fff;
      border: none;
      padding: 10px 18px;
      font-weight: 700;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    footer .faq-section button:hover {
      background: #d9d9d9;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="assets/images/kampus-poli.jpg.png" alt="Polibatam" class="me-2" />
      <div>
        <div class="fw-bold title-text">Politeknik Negeri Batam</div>
        <small class="subtitle-text">Buku Tamu — Tata Usaha</small>
      </div>
    </a>
    <ul class="navbar-nav ms-auto">
      <li class="nav-item"><a class="nav-link fw-semibold" href="index.php">Beranda</a></li>
      <li class="nav-item"><a class="nav-link fw-semibold" href="tentang.php">Tentang</a></li>
      <li class="nav-item"><a class="nav-link fw-semibold" href="admin/login.php">Login</a></li>
    </ul>
  </div>
</nav>

<!-- Isi Halaman -->
<div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
  <div class="card card-custom p-4" style="max-width: 500px; width: 100%;">
    <h4 class="mb-3 text-center">
      <i class="bi bi-pencil-square me-2"></i>Isi Buku Tamu
    </h4>

    <!-- Notifikasi Status Deteksi -->
    <div id="deteksiStatus" 
         class="alert alert-info py-2 px-3 mb-3 d-none" 
         style="border-radius:10px; font-size: 0.9rem;">
    </div>

    <form id="formTamu" method="POST" action="simpan_tamu.php">

      <!-- Nama -->
      <div class="mb-3 position-relative">
        <i class="bi bi-person form-icon"></i>
        <input type="text" name="nama" class="form-control" placeholder="Nama *" required />
      </div>

      <!-- NIM/NIK -->
      <div class="mb-3 position-relative">
        <i class="bi bi-person-vcard form-icon"></i>
        <input type="text" name="nim_nik" class="form-control" placeholder="NIM / NIK *" required />
      </div>

      <!-- Email -->
      <div class="mb-3 position-relative">
        <i class="bi bi-envelope form-icon"></i>
        <input type="email" name="email" class="form-control" placeholder="Email" />
      </div>

      <!-- Telepon -->
      <div class="mb-3 position-relative">
        <i class="bi bi-telephone form-icon"></i>
        <input type="text" name="telepon" class="form-control" placeholder="No. Telepon" />
      </div>

      <!-- Instansi -->
      <div class="mb-3 position-relative">
        <i class="bi bi-building form-icon"></i>
        <input type="text" name="instansi" class="form-control" placeholder="Masukkan Prodi atau Instansi" />
      </div>

      <!-- Keperluan -->
      <div class="mb-3 position-relative">
        <i class="bi bi-chat-left-text form-icon"></i>
        <textarea name="keperluan" class="form-control" placeholder="Keperluan *" rows="3" required></textarea>
      </div>

      <!-- Tombol -->
      <button type="submit" class="btn btn-primary w-100 py-2" id="btnSubmit">
        <i class="bi bi-send me-2"></i> Kirim
      </button>
    </form>
  </div>
</div>

<!-- Footer -->
<footer>
  <div class="container">
    <div class="logo-section">
      <img src="assets/images/kampus-poli.jpg.png" alt="Polibatam Logo" />
      <h3>B U K U  T A M U</h3>
    </div>

    <div class="contact-section">
      <p>Jl. Ahmad Yani Batam Kota, Kota Batam, Kepulauan Riau, Indonesia</p>
      <p>Email : info@polibatam.ac.id</p>
      <p>Email : helpdesk1074@polibatam.ac.id</p>
      <p>Phone : +62-778-469858 Ext.1017</p>
    </div>
    <div class="faq-section">
      <h4>Frequently Ask Questions</h4>
      <p>Untuk informasi lebih lanjut, silahkan klik tombol dibawah ini</p>
      <button onclick="window.location.href='tentang.php'">Lihat tentang</button>
    </div>
  </div>
</footer>

<!-- SUCCESS ANIMASI KEREN -->
<?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
<script>
Swal.fire({
    title: "Data Tersimpan!",
    text: "Terima kasih telah mengisi buku tamu 🙌",
    icon: "success",
    background: "rgba(255,255,255,0.95)",
    showConfirmButton: false,
    timer: 2500
});


</script>
<?php endif; ?>

<!-- SCRIPT UTAMA -->
<script>
const form = document.getElementById("formTamu");
const btn = document.getElementById("btnSubmit");
const nimField = document.querySelector("input[name='nim_nik']");
const emailField = document.querySelector("input[name='email']");
const telpField = document.querySelector("input[name='telepon']");
const instansiField = document.querySelector("input[name='instansi']");
const statusBox = document.getElementById("deteksiStatus");

/* VALIDASI REALTIME */
document.querySelectorAll("input, textarea").forEach(field => {
  field.addEventListener("input", function () {
    if (this.checkValidity()) {
      this.style.borderColor = "#28a745";
    } else {
      this.style.borderColor = "#dc3545";
    }
  });
});

/* HANYA ANGKA UNTUK NIM/NIK DAN TELEPON */
nimField.addEventListener("input", () => {
  nimField.value = nimField.value.replace(/[^0-9]/g, "");
});
telpField.addEventListener("input", () => {
  telpField.value = telpField.value.replace(/[^0-9]/g, "");
});

/* 🧠 AUTO-DETECT MAHASISWA */
nimField.addEventListener("input", function () {
    const nim = nimField.value.trim();
    const isMahasiswa = /^[3]\d{10}$/.test(nim);

    if (isMahasiswa) {
        statusBox.classList.remove("d-none");
        statusBox.classList.add("alert-success");
        statusBox.textContent = "Mahasiswa Polibatam terdeteksi ✓";

        emailField.parentElement.style.display = "none";
        telpField.parentElement.style.display = "none";
        instansiField.parentElement.style.display = "none";

        instansiField.value = "Politeknik Negeri Batam";
    } else {
        statusBox.classList.add("d-none");

        emailField.parentElement.style.display = "block";
        telpField.parentElement.style.display = "block";
        instansiField.parentElement.style.display = "block";

        instansiField.value = "";
    }
});

/* KONFIRMASI SEBELUM SUBMIT */
form.addEventListener("submit", function(e) {
  e.preventDefault();

  Swal.fire({
    title: "Kirim Data?",
    text: "Pastikan semua data sudah benar.",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#2a6cc2",
    cancelButtonColor: "#aaa",
    confirmButtonText: "Ya, kirim!",
    cancelButtonText: "Batal"
  }).then((result) => {
    if (result.isConfirmed) {
      btn.disabled = true;
      btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Mengirim...`;
      form.submit();
    }
  });
});
</script>
</body>
</html>
