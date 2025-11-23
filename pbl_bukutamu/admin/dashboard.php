<?php
session_start();
require_once '../config.php';

// Cek login admin
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$admin_username = $_SESSION['admin_username'];
$admin_name = $_SESSION['admin_name'];
$admin_role = $_SESSION['admin_role'];

// Cek foto profil
$uploadDir = '../uploads/';
$photoPath = $uploadDir . $admin_username . '.jpg';
$photoURL = file_exists($photoPath) ? $photoPath : '../assets/images/default-profile.png';

// === Ekspor Otomatis Mingguan & Bulanan ===
date_default_timezone_set('Asia/Jakarta');
$today = date('Y-m-d');
$day = date('N'); // 1 = Senin
$date = date('j'); // 1 = tanggal 1

$exportDir = __DIR__ . '/../exports';
if (!file_exists($exportDir)) {
    mkdir($exportDir, 0777, true);
}

function exportData($pdo, $filename, $start, $end) {
    $stmt = $pdo->prepare("SELECT nama, nim_nik, instansi, keperluan, tanggal, jam_keluar 
                           FROM tamu 
                           WHERE tanggal BETWEEN :start AND :end 
                           ORDER BY tanggal ASC");
    $stmt->execute([':start' => $start, ':end' => $end]);

    $path = __DIR__ . '/../exports/' . $filename;
    $f = fopen($path, 'w');
    fputcsv($f, ['Nama','NIM/NIK','Instansi','Keperluan','Tanggal','Jam Keluar']);
    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($f, [$r['nama'],$r['nim_nik'],$r['instansi'],$r['keperluan'],$r['tanggal'],$r['jam_keluar']]);
    }
    fclose($f);
}

// Ekspor mingguan tiap Senin (data 7 hari sebelumnya)
if ($day == 1) {
    $start = date('Y-m-d', strtotime('-7 days'));
    $end = date('Y-m-d', strtotime('-1 day'));
    $filename = 'export_mingguan_' . $today . '.csv';
    $path = __DIR__ . '/../exports/' . $filename;
    if (!file_exists($path)) {
        exportData($pdo, $filename, $start . ' 00:00:00', $end . ' 23:59:59');
    }
}

// Ekspor bulanan tiap tanggal 1 (data bulan sebelumnya)
if ($date == 1) {
    $start = date('Y-m-01', strtotime('-1 month'));
    $end = date('Y-m-t', strtotime('-1 month'));
    $filename = 'export_bulanan_' . $today . '.csv';
    $path = __DIR__ . '/../exports/' . $filename;
    if (!file_exists($path)) {
        exportData($pdo, $filename, $start . ' 00:00:00', $end . ' 23:59:59');
    }
}

// === Statistik Laporan Otomatis ===
$today = new DateTime();
$weekStart = (clone $today)->modify('monday this week')->format('Y-m-d 00:00:00');
$weekEnd   = (clone $today)->modify('sunday this week')->format('Y-m-d 23:59:59');
$monthStart = $today->format('Y-m-01 00:00:00');
$monthEnd   = $today->format('Y-m-t 23:59:59');

$stat = [];

// Hitung mingguan
$stmt = $pdo->prepare("SELECT COUNT(*) FROM tamu WHERE tanggal BETWEEN :start AND :end");
$stmt->execute([':start' => $weekStart, ':end' => $weekEnd]);
$stat['mingguan'] = $stmt->fetchColumn();

// Hitung bulanan
$stmt = $pdo->prepare("SELECT COUNT(*) FROM tamu WHERE tanggal BETWEEN :start AND :end");
$stmt->execute([':start' => $monthStart, ':end' => $monthEnd]);
$stat['bulanan'] = $stmt->fetchColumn();

/* =========================================================
   5️⃣ Tambah Admin Baru
========================================================= */
if (isset($_POST['action']) && $_POST['action'] === 'add_admin') {
    $username = trim($_POST['username']);
    $name     = trim($_POST['name']);
    $password = trim($_POST['password']);
    $role     = trim($_POST['role']);

    if ($username && $name && $password) {
        $check = $pdo->prepare("SELECT id FROM admins WHERE username = :u");
        $check->execute([':u' => $username]);

        if ($check->rowCount() == 0) {
            $insert = $pdo->prepare("
                INSERT INTO admins (username, name, password, role)
                VALUES (:u, :n, :p, :r)
            ");
            $insert->execute([
                ':u' => $username,
                ':n' => $name,
                ':p' => password_hash($password, PASSWORD_DEFAULT),
                ':r' => $role
            ]);
            $msg_admin = "Admin berhasil ditambahkan!";
        } else {
            $msg_admin = "Username sudah digunakan!";
        }
    } else {
        $msg_admin = "Semua field wajib diisi!";
    }
}

/* =========================================================
   1️⃣ Export CSV
========================================================= */
if (isset($_GET['action']) && $_GET['action'] === 'export_csv') {
    $start = $_GET['start'] ?? null;
    $end = $_GET['end'] ?? null;
    $prodi = $_GET['prodi'] ?? null;

    $sql = "SELECT nama, nim_nik, instansi, keperluan, tanggal, jam_keluar FROM tamu WHERE 1=1";
    $params = [];
    if ($start && $end) {
        $sql .= " AND tanggal BETWEEN :start AND :end";
        $params[':start'] = $start . ' 00:00:00';
        $params[':end'] = $end . ' 23:59:59';
    }
    if ($prodi) {
        $sql .= " AND instansi = :prodi";
        $params[':prodi'] = $prodi;
    }
    $sql .= " ORDER BY tanggal DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=tamu_export.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Nama','NIM/NIK','Prodi/Instansi','Keperluan','Tanggal','Jam Keluar']);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($out, [
            $row['nama'], $row['nim_nik'], $row['instansi'],
            $row['keperluan'], $row['tanggal'], $row['jam_keluar']
        ]);
    }
    fclose($out);
    exit;
}

/* =========================================================
   2️⃣ AJAX: Set jam keluar
========================================================= */
if (isset($_POST['action']) && $_POST['action'] === 'set_keluar') {
    $id = intval($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE tamu SET jam_keluar = NOW() WHERE id = :id");
        $stmt->execute([':id' => $id]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'msg' => 'ID tidak valid']);
    }
    exit;
}

/* =========================================================
   3️⃣ AJAX: Statistik Chart
========================================================= */
if (isset($_GET['action']) && $_GET['action'] === 'get_stats') {
    $start = $_GET['start'] ?? null;
    $end = $_GET['end'] ?? null;
    $prodi = $_GET['prodi'] ?? null;

    $sql = "SELECT DATE(tanggal) as tgl, COUNT(*) as cnt FROM tamu WHERE 1=1";
    $params = [];
    if ($start && $end) {
        $sql .= " AND tanggal BETWEEN :start AND :end";
        $params[':start'] = $start . ' 00:00:00';
        $params[':end'] = $end . ' 23:59:59';
    }
    if ($prodi) {
        $sql .= " AND instansi = :prodi";
        $params[':prodi'] = $prodi;
    }
    $sql .= " GROUP BY DATE(tanggal) ORDER BY DATE(tanggal) ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $labels = [];
    $data = [];
    if ($start && $end) {
        $period = new DatePeriod(
            new DateTime($start),
            new DateInterval('P1D'),
            (new DateTime($end))->modify('+1 day')
        );
        $map = [];
        foreach ($rows as $r) $map[$r['tgl']] = intval($r['cnt']);
        foreach ($period as $d) {
            $k = $d->format('Y-m-d');
            $labels[] = $k;
            $data[] = $map[$k] ?? 0;
        }
    } else {
        for ($i = 6; $i >= 0; $i--) {
            $d = (new DateTime())->modify("-{$i} days")->format('Y-m-d');
            $labels[] = $d;
            $data[] = 0;
        }
        foreach ($rows as $r) {
            $idx = array_search($r['tgl'], $labels);
            if ($idx !== false) $data[$idx] = intval($r['cnt']);
        }
    }
    echo json_encode(['labels' => $labels, 'data' => $data]);
    exit;
}

/* =========================================================
   4️⃣ Ambil data untuk tabel dashboard
========================================================= */
$start = $_GET['start'] ?? '';
$end = $_GET['end'] ?? '';
$prodi = $_GET['prodi'] ?? '';
$search = $_GET['search'] ?? '';

$prodiStmt = $pdo->query("SELECT DISTINCT instansi FROM tamu ORDER BY instansi ASC");
$prodiList = $prodiStmt->fetchAll(PDO::FETCH_COLUMN);

$page = max(1, intval($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM tamu WHERE 1=1 ";
$params = [];
if ($start && $end) {
    $sql .= " AND tanggal BETWEEN :start AND :end";
    $params[':start'] = $start . ' 00:00:00';
    $params[':end'] = $end . ' 23:59:59';
}
if ($prodi) {
    $sql .= " AND instansi = :prodi";
    $params[':prodi'] = $prodi;
}
if ($search) {
    $sql .= " AND (nama LIKE :s OR nim_nik LIKE :s OR keperluan LIKE :s)";
    $params[':s'] = "%$search%";
}

$totalStmt = $pdo->prepare(str_replace('*', 'COUNT(*) as cnt', $sql));
$totalStmt->execute($params);
$total = $totalStmt->fetchColumn();

$sql .= " ORDER BY tanggal DESC LIMIT :lim OFFSET :off";
$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) $stmt->bindValue($k, $v);
$stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
$stmt->bindValue(':off', (int)$offset, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalPages = max(1, ceil($total / $limit));

$totalAll = $pdo->query("SELECT COUNT(*) FROM tamu")->fetchColumn();
$totalToday = $pdo->prepare("SELECT COUNT(*) FROM tamu WHERE DATE(tanggal) = CURDATE()");
$totalToday->execute();
$totalToday = $totalToday->fetchColumn();
$keluarToday = $pdo->prepare("SELECT COUNT(*) FROM tamu WHERE DATE(tanggal) = CURDATE() AND jam_keluar IS NOT NULL");
$keluarToday->execute();
$keluarToday = $keluarToday->fetchColumn();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard Admin — Buku Tamu</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
  :root { --card-bg: #ffffff; --muted: #6c757d; }
  .card-stats { border-radius: 12px; box-shadow: 0 6px 18px rgba(0,0,0,0.06); }
  .stat-value { font-size: 28px; font-weight:700; }
  .table thead th { background:#f8f9fa; }
  .avatar-xs { width:34px; height:34px; border-radius:50%; object-fit:cover; }
  .btn-yellow { background:#ffc107; color:#000; border: none; }
  body.dark { background: #0f1724; color: #e6eef8; }
  body.dark .card { background: #0b1220; color: #e6eef8; }
  body.dark .table thead th { background: #071028; color: #ddd; }
  body.dark .table { color: #e6eef8; }

  /* ✅ Fix pagination not clickable (z-index issue) */
  canvas#chartPengunjung {
      position: relative;
      z-index: 1;
  }
  .table, .pagination, footer, .card {
      position: relative;
      z-index: 2;
  }
</style>
</head>
<body>
  
<!-- ✅ Navbar Baru -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold text-primary d-flex align-items-center" href="dashboard.php">
      <i class="bi bi-card-checklist me-2"></i> Buku Tamu Admin
    </a>

    <div class="d-flex align-items-center ms-auto gap-2">
      <!-- Tombol tema -->
      <button id="themeToggle" class="btn btn-sm btn-outline-secondary" title="Ganti Tema">🌙</button>

      <!-- Tautan ke halaman utama -->
      <a class="btn btn-sm btn-outline-primary" href="../index.php" target="_blank">
        <i class="bi bi-house"></i> Lihat Situs
      </a>

      <!-- Dropdown profil -->
      <div class="dropdown ms-2">
        <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="profileDrop" data-bs-toggle="dropdown" aria-expanded="false">
          <img src="<?= htmlspecialchars($photoURL) ?>" 
               alt="Foto Profil" 
               style="width:38px; height:38px; border-radius:50%; object-fit:cover; border:2px solid #007bff;" 
               class="me-2">
          <span class="fw-semibold"><?= htmlspecialchars($admin_name ?? 'Admin', ENT_QUOTES, 'UTF-8') ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
          <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person-circle me-2"></i> Profil</a></li>
<li><hr class="dropdown-divider"></li>
<li><a class="dropdown-item" href="manage_admin.php">
    <i class="bi bi-people-fill me-2"></i> Kelola Admin
</a></li>
          <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>

<div class="container mt-4 mb-5">
  <!-- Statistik cards -->
  <div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card p-3 card-stats"><div class="text-muted">Total Pengunjung</div><div class="stat-value"><?= $totalAll ?></div></div></div>
    <div class="col-md-3"><div class="card p-3 card-stats"><div class="text-muted">Hari Ini</div><div class="stat-value text-success"><?= $totalToday ?></div></div></div>
    <div class="col-md-3"><div class="card p-3 card-stats"><div class="text-muted">Sudah Keluar Hari Ini</div><div class="stat-value text-danger"><?= $keluarToday ?></div></div></div>
    <div class="col-md-3"><div class="card p-3 card-stats text-center"><div class="text-muted">Admin Aktif</div><div class="stat-value text-warning"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Administrator') ?></div></div></div>
  </div>

  <!-- Filter + Chart -->
  <div class="card mb-4">
    <div class="card-body">
      <form id="filterForm" class="row g-2 align-items-end">
        <div class="col-md-3"><label class="form-label">Dari</label><input type="date" name="start" class="form-control" value="<?= htmlspecialchars($start) ?>"></div>
        <div class="col-md-3"><label class="form-label">Sampai</label><input type="date" name="end" class="form-control" value="<?= htmlspecialchars($end) ?>"></div>
        <div class="col-md-3"><label class="form-label">Prodi / Instansi</label>
          <select name="prodi" class="form-select">
            <option value="">Semua</option>
            <?php foreach ($prodiList as $p): ?>
              <option value="<?= htmlspecialchars($p) ?>" <?= $p===$prodi?'selected':'' ?>><?= htmlspecialchars($p) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Terapkan</button>
          <a href="dashboard.php" class="btn btn-outline-secondary">Reset</a>
          <a href="?action=export_csv" class="btn btn-outline-success"><i class="bi bi-file-earmark-spreadsheet"></i> CSV</a>
        </div>
      </form>
      <hr>
      <canvas id="chartPengunjung" height="80"></canvas>
    </div>
  </div>

  <!-- Tabel -->
  <div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <div><i class="bi bi-people"></i> Daftar Pengunjung</div>
      <div class="d-flex gap-2 align-items-center">
        <input id="searchInput" class="form-control form-control-sm" placeholder="Cari..." value="<?= htmlspecialchars($search) ?>" style="width:200px">
        <a class="btn btn-sm btn-light" href="add.php"><i class="bi bi-plus"></i> Tambah</a>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table mb-0 align-middle">
          <thead><tr><th>No</th><th>Nama</th><th>NIM/NIK</th><th>Instansi</th><th>Keperluan</th><th>Tanggal</th><th>Jam Keluar</th><th>Aksi</th></tr></thead>
          <tbody>
          <?php if (count($rows)===0): ?>
            <tr><td colspan="8" class="text-center p-4">Tidak ada data</td></tr>
          <?php else: foreach ($rows as $i=>$r): ?>
            <tr data-id="<?= $r['id'] ?>">
              <td><?= $offset+$i+1 ?></td>
              <td><strong><?= htmlspecialchars($r['nama']) ?></strong></td>
              <td><?= htmlspecialchars($r['nim_nik']) ?></td>
              <td><?= htmlspecialchars($r['instansi']) ?></td>
              <td><?= htmlspecialchars($r['keperluan']) ?></td>
              <td><?= htmlspecialchars($r['tanggal']) ?></td>
              <td class="jam-keluar"><?= $r['jam_keluar'] ?? '-' ?></td>
              <td>
                <?php if (empty($r['jam_keluar'])): ?>
                  <button class="btn btn-sm btn-yellow btn-set-keluar"><i class="bi bi-door-closed"></i> Keluar</button>
                <?php else: ?>
                  <button class="btn btn-sm btn-secondary" disabled><i class="bi bi-check2"></i> Selesai</button>
                <?php endif; ?>
                <a href="delete.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data?')"><i class="bi bi-trash"></i></a>
              </td>
            </tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="p-3 d-flex justify-content-between align-items-center">
        <small class="text-muted">Halaman <?= $page ?> dari <?= $totalPages ?> — total <?= $total ?> entri</small>
        <ul class="pagination mb-0">
          <li class="page-item <?= $page<=1?'disabled':'' ?>"><a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$page-1])) ?>">&laquo; Sebelumnya</a></li>
          <li class="page-item disabled"><a class="page-link">Hal <?= $page ?></a></li>
          <li class="page-item <?= $page>=$totalPages?'disabled':'' ?>"><a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$page+1])) ?>">Berikutnya &raquo;</a></li>
        </ul>
      </div>
    </div>
  </div>
  <footer class="text-center text-muted mt-4">&copy; <?= date('Y') ?> — Buku Tamu Digital</footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function(){
  // dark mode
  const btn=document.getElementById('themeToggle');
  const set=(m)=>{if(m==='dark'){document.body.classList.add('dark');btn.textContent='☀️';}else{document.body.classList.remove('dark');btn.textContent='🌙';}localStorage.setItem('theme',m);};
  set(localStorage.getItem('theme')||'light');
  btn.onclick=()=>set(document.body.classList.contains('dark')?'light':'dark');

  // search
  document.getElementById('searchInput').onkeydown=e=>{
    if(e.key==='Enter'){
      e.preventDefault();
      const s=e.target.value.trim();
      const url=new URL(window.location);
      if(s)url.searchParams.set('search',s);else url.searchParams.delete('search');
      url.searchParams.set('page',1);
      location.href=url;
    }
  };

  // tombol keluar
  document.querySelectorAll('.btn-set-keluar').forEach(btn=>{
    btn.onclick=()=>{
      const tr=btn.closest('tr');
      const id=tr.dataset.id;
      if(confirm('Tandai pengunjung ini sudah keluar?')){
        fetch('dashboard.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'action=set_keluar&id='+id})
          .then(r=>r.json()).then(j=>{
            if(j.success){tr.querySelector('.jam-keluar').textContent='Baru Keluar';btn.disabled=true;btn.textContent='Selesai';}
          });
      }
    };
  });

  // chart
  const ctx=document.getElementById('chartPengunjung');
  const params=new URLSearchParams(window.location.search);
  params.set('action','get_stats');
  fetch('dashboard.php?'+params.toString()).then(r=>r.json()).then(d=>{
    new Chart(ctx,{type:'line',data:{labels:d.labels,datasets:[{label:'Jumlah Pengunjung',data:d.data,fill:true,tension:0.3,borderColor:'#0d6efd',backgroundColor:'rgba(13,110,253,0.1)'}]},options:{scales:{y:{beginAtZero:true}}}});
  });
})();
</script>
</body>
</html>