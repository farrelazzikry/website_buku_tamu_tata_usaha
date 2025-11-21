<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Terima Kasih - Buku Tamu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      height: 100vh;
      margin: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #004aad, #67c4f3);
      color: white;
      font-family: "Poppins", sans-serif;
      overflow: hidden;
    }

    .card {
      background: white;
      color: #333;
      padding: 40px;
      border-radius: 20px;
      text-align: center;
      box-shadow: 0 8px 25px rgba(0,0,0,0.2);
      max-width: 400px;
      animation: fadeIn 0.6s ease;
    }

    .spinner {
      border: 5px solid #eee;
      border-top: 5px solid #004aad;
      border-radius: 50%;
      width: 50px;
      height: 50px;
      animation: spin 1s linear infinite;
      margin: 20px auto;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .fade-out {
      animation: fadeOut 0.5s forwards;
    }

    @keyframes fadeOut {
      to { opacity: 0; transform: scale(0.95); }
    }
  </style>

  <script>
    setTimeout(() => {
      document.querySelector('.card').classList.add('fade-out');
      setTimeout(() => {
        window.location.href = "index.php";
      }, 500);
    }, 3000);
  </script>
</head>
<body>
  <div class="card">
    <h3>✅ Data Berhasil Dikirim!</h3>
    <div class="spinner"></div>
    <p>Terima kasih telah mengisi buku tamu Tata Usaha Polibatam.</p>
    <p><small>Anda akan diarahkan kembali ke beranda...</small></p>
  </div>
</body>
</html>
