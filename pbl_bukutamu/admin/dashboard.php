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

// Foto profil
$uploadDir = '../uploads/';
$photoPath = $uploadDir . $admin_username . '.jpg';
$photoURL = file_exists($photoPath) ? $photoPath : '../assets/images/default-profile.png';

date_default_timezone_set('Asia/Jakarta');

// ===================== HAPUS TAMU AJAX =====================
if (isset($_POST['action']) && $_POST['action'] === 'delete_tamu') {
    $id = intval($_POST['id']);
    $stmt = $pdo->prepare("DELETE FROM tamu WHERE id = :id");
    $stmt->execute([':id'=>$id]);
    echo json_encode(['success'=>true]);
    exit;
}

// ===================== SET KELUAR AJAX =====================
if (isset($_POST['action']) && $_POST['action'] === 'set_keluar') {
    $id = intval($_POST['id']);
    $stmt = $pdo->prepare("UPDATE tamu SET jam_keluar = NOW() WHERE id=:id");
    $stmt->execute([':id'=>$id]);
    echo json_encode(['success'=>true]);
    exit;
}

// ===================== GET CHART DATA =====================
if (isset($_GET['action']) && $_GET['action']==='get_stats') {
    $start = $_GET['start'] ?? null;
    $end   = $_GET['end'] ?? null;

    $sql = "SELECT DATE(tanggal) as tgl, COUNT(*) as cnt FROM tamu WHERE 1=1";
    $params = [];

    if ($start && $end) {
        $sql .= " AND tanggal BETWEEN :s AND :e";
        $params[':s'] = $start." 00:00:00";
        $params[':e'] = $end." 23:59:59";
    }

    $sql .= " GROUP BY DATE(tanggal) ORDER BY DATE(tanggal)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($rows);
    exit;
}

// ===================== FILTER TABEL =====================
$start  = $_GET['start'] ?? '';
$end    = $_GET['end'] ?? '';
$search = $_GET['search'] ?? '';
$prodi  = $_GET['prodi'] ?? '';

$page  = max(1, intval($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page-1)*$limit;

$sql = "SELECT * FROM tamu WHERE 1=1";
$params = [];

if ($start && $end) {
    $sql .= " AND tanggal BETWEEN :s AND :e";
    $params[':s'] = $start." 00:00:00";
    $params[':e'] = $end." 23:59:59";
}

if ($search) {
    $sql .= " AND (nama LIKE :x OR nim_nik LIKE :x OR keperluan LIKE :x)";
    $params[':x'] = "%$search%";
}

if ($prodi) {
    $sql .= " AND instansi = :p";
    $params[':p'] = $prodi;
}

$totalStmt = $pdo->prepare(str_replace("*","COUNT(*)",$sql));
$totalStmt->execute($params);
$totalRows = $totalStmt->fetchColumn();

$sql .= " ORDER BY tanggal DESC LIMIT :lim OFFSET :off";
$stmt = $pdo->prepare($sql);
foreach($params as $k=>$v) $stmt->bindValue($k,$v);
$stmt->bindValue(':lim',(int)$limit,PDO::PARAM_INT);
$stmt->bindValue(':off',(int)$offset,PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalPages = ceil($totalRows/$limit);

// Statistik
$totalAll = $pdo->query("SELECT COUNT(*) FROM tamu")->fetchColumn();
$totalToday = $pdo->query("SELECT COUNT(*) FROM tamu WHERE DATE(tanggal)=CURDATE()")->fetchColumn();
$keluarToday = $pdo->query("SELECT COUNT(*) FROM tamu WHERE DATE(tanggal)=CURDATE() AND jam_keluar IS NOT NULL")->fetchColumn();

// List prodi (contoh hardcode)
$prodiList = $pdo->query("SELECT DISTINCT instansi FROM tamu ORDER BY instansi ASC")->fetchAll(PDO::FETCH_COLUMN);

// ===================== EXPORT CSV =====================
if(isset($_GET['action']) && $_GET['action']==='export_csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=tamu.csv');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['No','Nama','NIM/NIK','Instansi','Keperluan','Tanggal','Jam Masuk','Jam Keluar']);

    $sqlExport = "SELECT * FROM tamu WHERE 1=1";
    $paramsExport = [];

    if ($start && $end) {
        $sqlExport .= " AND tanggal BETWEEN :s AND :e";
        $paramsExport[':s'] = $start." 00:00:00";
        $paramsExport[':e'] = $end." 23:59:59";
    }

    if ($search) {
        $sqlExport .= " AND (nama LIKE :x OR nim_nik LIKE :x OR keperluan LIKE :x)";
        $paramsExport[':x'] = "%$search%";
    }

    if ($prodi) {
        $sqlExport .= " AND instansi = :p";
        $paramsExport[':p'] = $prodi;
    }

    $stmtExport = $pdo->prepare($sqlExport);
    foreach($paramsExport as $k=>$v) $stmtExport->bindValue($k,$v);
    $stmtExport->execute();
    $dataExport = $stmtExport->fetchAll(PDO::FETCH_ASSOC);

    foreach($dataExport as $i=>$r){
        fputcsv($output, [
            $i+1,
            $r['nama'],
            $r['nim_nik'],
            $r['instansi'],
            $r['keperluan'],
            $r['tanggal'],
            $r['tanggal'], // jam masuk bisa disimpan di kolom terpisah jika ada
            $r['jam_keluar']
        ]);
    }
    fclose($output);
    exit;
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard Admin — Buku Tamu</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
body {background:#f8f9fa; font-family:'Inter',sans-serif; transition:0.3s;}
.sidebar{position:fixed;top:0;left:0;width:240px;height:100vh;background:  #0168bdff;color:#fff;padding-top:20px;overflow-y:auto;box-shadow:2px 0 12px rgba(0,0,0,0.25);}
.sidebar .brand{font-size:20px;font-weight:700;padding:20px;text-align:center;border-bottom:1px solid rgba(0, 0, 0, 0.86);}
.sidebar a{display:block;padding:15px 15px;font-size:15px;color:#d1d5db;text-decoration:none;border-radius:6px;margin:5px 5px;}
.sidebar a:hover,.sidebar a.active{background:#3b82f6;color:#fff;}
.topbar{margin-left:240px;height:65px;background:white;display:flex;align-items:center;padding:0 25px;border-bottom:1px solid #ffffffff;position:sticky;top:0;z-index:100;}
.topbar .profile{margin-left:auto;display:flex;align-items:center;gap:12px;cursor:pointer;}
.avatar{width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid #1e6ae5ff;}
.content{margin-left:240px;padding:25px;min-height:100vh;}
.stat-value{font-size:1.8rem;font-weight:600;margin-top:5px;}
.card{border-radius:12px;box-shadow:0 6px 18px rgba(0,0,0,0.06);}
.card-header{font-weight:600;font-size:1rem;}
.table th,.table td{vertical-align:middle;}
.btn{transition:all 0.2s;}
.btn:hover{opacity:0.9;}
#chartPengunjung{background:#fff;border-radius:12px;padding:12px;}
body.dark #chartPengunjung{background:#0b1220;}
body.dark{background:#0f1724;color:#e6eef8;}
body.dark .card{background:#0b1220;color:#e6eef8;}
body.dark .table thead{background:#071028;color:#ddd;}
body.dark .table td, body.dark .table th{border-color:#1f2937;}
body.dark footer{color:#888;}
@media(max-width:768px){
  .card-header .d-flex{flex-direction:column;gap:5px;}
  #searchInput{width:100% !important;}
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
<div class="brand"><i class="bi bi-grid-1x2-fill"></i> Buku Tamu</div>
<a href="dashboard.php" class="active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
<a href="manage_admin.php"><i class="bi bi-people me-2"></i> Kelola Admin</a>
<div class="divider"></div>
<a href="profile.php"><i class="bi bi-person-circle me-2"></i> Profil</a>
<a href="logout.php" class="text-danger"><i class="bi bi-door-closed me-2"></i> Logout</a>
</div>

<!-- TOPBAR -->
<div class="topbar">
<button id="themeToggle" class="btn btn-sm btn-outline-secondary">🌙</button>
<div class="profile dropdown">
<img src="<?= htmlspecialchars($photoURL) ?>" class="avatar" data-bs-toggle="dropdown">
<span class="fw-semibold" data-bs-toggle="dropdown"><?= htmlspecialchars($admin_name) ?></span>
<ul class="dropdown-menu dropdown-menu-end shadow">
<li><a class="dropdown-item" href="profile.php">Profil</a></li>
<li><a class="dropdown-item" href="manage_admin.php">Kelola Admin</a></li>
<li><hr class="dropdown-divider"></li>
<li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
</ul>
</div>
</div>

<!-- CONTENT -->
<div class="content">
<!-- Statistik -->
<div class="row g-3 mb-4">
<div class="col-md-3"><div class="card p-3 shadow-sm border-0" style="background:#f8f9fa;color:#rgb;"><div class="text-muted">Total Pengunjung</div><div class="stat-value"><?= $totalAll ?></div><i class="bi bi-people-fill" style="font-size:24px;opacity:0.4;"></i></div></div>
<div class="col-md-3"><div class="card p-3 shadow-sm border-0" style="background:#f8f9fa;color:#rgb;"><div class="text-muted">Hari Ini</div><div class="stat-value"><?= $totalToday ?></div><i class="bi bi-calendar-day" style="font-size:24px;opacity:0.4;"></i></div></div>
<div class="col-md-3"><div class="card p-3 shadow-sm border-0" style="background:#f8f9fa;color:#rgb;"><div class="text-muted">Sudah Keluar Hari Ini</div><div class="stat-value"><?= $keluarToday ?></div><i class="bi bi-door-closed" style="font-size:24px;opacity:0.4;"></i></div></div>
<div class="col-md-3"><div class="card p-3 shadow-sm border-0" style="background:#32cd32;color:#rgb;"><div class="text-muted">Admin Aktif</div><div class="stat-value"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Administrator') ?></div><i class="bi bi-person-circle" style="font-size:24px;opacity:0.4;"></i></div></div>
</div>

<!-- Filter + Chart -->
<div class="card shadow-sm mb-4"><div class="card-body">
<form id="filterForm" class="row g-2 align-items-end">
<div class="col-md-3"><label class="form-label">Dari</label><input type="date" name="start" class="form-control" value="<?= htmlspecialchars($start) ?>"></div>
<div class="col-md-3"><label class="form-label">Sampai</label><input type="date" name="end" class="form-control" value="<?= htmlspecialchars($end) ?>"></div>
<div class="col-md-3"><label class="form-label">Prodi / Instansi</label>
<select name="prodi" class="form-select">
<option value="">Semua</option>
<?php foreach($prodiList as $p): ?>
<option value="<?= htmlspecialchars($p) ?>" <?= $p===$prodi?'selected':'' ?>><?= htmlspecialchars($p) ?></option>
<?php endforeach; ?>
</select></div>
<div class="col-md-3 d-flex gap-2">
<button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Terapkan</button>
<a href="dashboard.php" class="btn btn-outline-secondary">Reset</a>
<a href="export.php" class="btn btn-success">Excel</a>
</div>
</form>
<hr>
<canvas id="chartPengunjung" height="80"></canvas>
</div></div>

<!-- Tabel Pengunjung -->
<div class="card-header d-flex justify-content-between align-items-center" style="background: #5481a5ff;color:#fff;border: radius 13px;px 12px 0 0;">
  <div><i class="bi bi-people-fill me-2"></i> Daftar Pengunjung</div>
  <div class="d-flex gap-2 align-items-center">
    <form id="searchForm" class="d-flex" method="GET" action="dashboard.php">
      <input id="searchInput" name="search" class="form-control form-control-sm" placeholder="Cari..." value="<?= htmlspecialchars($search) ?>" style="width:200px">
      <input type="hidden" name="start" value="<?= htmlspecialchars($start) ?>">
      <input type="hidden" name="end" value="<?= htmlspecialchars($end) ?>">
      <input type="hidden" name="prodi" value="<?= htmlspecialchars($prodi) ?>">
    </form>
  </div>
</div>
<div class="card-body p-0">
<div class="table-responsive">
<table class="table table-hover mb-0 align-middle">
<thead class="table-light">
<tr>
<th>No</th><th>Nama</th><th>NIM/NIK</th><th>Instansi</th><th>Keperluan</th><th>Tanggal - Jam Masuk</th><th>Jam Keluar</th><th>Aksi</th>
</tr>
</thead>
<tbody>
<?php if(count($rows)===0): ?>
<tr><td colspan="8" class="text-center p-4">Tidak ada data</td></tr>
<?php else: foreach($rows as $i=>$r): ?>
<tr data-id="<?= $r['id'] ?>">
<td><?= $offset+$i+1 ?></td>
<td><strong><?= htmlspecialchars($r['nama']) ?></strong></td>
<td><?= htmlspecialchars($r['nim_nik']) ?></td>
<td><?= htmlspecialchars($r['instansi']) ?></td>
<td><?= htmlspecialchars($r['keperluan']) ?></td>
<td><?= htmlspecialchars($r['tanggal']) ?></td>
<td class="jam-keluar"><?= $r['jam_keluar'] ?? '-' ?></td>
<td class="d-flex gap-1">
<?php if(empty($r['jam_keluar'])): ?>
<button class="btn btn-sm btn-success btn-set-keluar" title="Tandai Keluar"><i class="bi bi-door-closed"></i></button>
<?php else: ?>
<button class="btn btn-sm btn-secondary" disabled><i class="bi bi-check2"></i></button>
<?php endif; ?>
<button class="btn btn-sm btn-danger btn-delete" title="Hapus Pengunjung"><i class="bi bi-trash"></i></button>
</td>
</tr>
<?php endforeach; endif; ?>
</tbody>
</table>
</div>

<!-- Pagination Blok -->
<div class="d-flex justify-content-between align-items-center mb-2 px-3">
<div>
Halaman <?= $page ?> dari <?= $totalPages ?> — total <?= $totalRows ?> entri
</div>
<div>
<nav>
<ul class="pagination mb-0">
<?php
$adjacents = 2;
$startPage = max(1, $page-$adjacents);
$endPage = min($totalPages, $page+$adjacents);

if($page>1): ?>
<li class="page-item"><a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$page-1])) ?>">&laquo;</a></li>
<?php endif;

for($i=$startPage;$i<=$endPage;$i++): ?>
<li class="page-item <?= $i==$page?'active':'' ?>"><a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$i])) ?>"><?= $i ?></a></li>
<?php endfor;

if($page<$totalPages): ?>
<li class="page-item"><a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$page+1])) ?>">&raquo;</a></li>
<?php endif; ?>
</ul>
</nav>
</div>
</div>
</div>
<footer class="text-center text-muted mt-5 mb-3" style="font-size:0.9rem;">
&copy; <?= date('Y') ?> — Buku Tamu Digital. All Rights Reserved.
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Dark mode
(function(){
const btnTheme=document.getElementById('themeToggle');
const setTheme=(mode)=>{
if(mode==='dark'){document.body.classList.add('dark'); btnTheme.textContent='☀️';}else{document.body.classList.remove('dark'); btnTheme.textContent='🌙';}
localStorage.setItem('theme',mode);
};
setTheme(localStorage.getItem('theme')||'light');
btnTheme.onclick=()=>setTheme(document.body.classList.contains('dark')?'light':'dark');

// Chart
const ctx=document.getElementById('chartPengunjung');
const params=new URLSearchParams(window.location.search);
params.set('action','get_stats');
fetch('dashboard.php?'+params.toString()).then(r=>r.json()).then(d=>{
new Chart(ctx,{type:'line',data:{labels:d.map(r=>r.tgl),datasets:[{label:'Jumlah Pengunjung',data:d.map(r=>parseInt(r.cnt)),fill:true,tension:0.4,borderColor:'#3b82f6',backgroundColor:'rgba(59,130,246,0.1)',pointBackgroundColor:'#3b82f6'}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true},x:{grid:{display:false}}}}});
});

// Tombol keluar
document.querySelectorAll('.btn-set-keluar').forEach(btn=>{
btn.onclick=()=>{
const tr=btn.closest('tr');
const id=tr.dataset.id;
if(confirm('Tandai pengunjung ini sudah keluar?')){
fetch('dashboard.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'action=set_keluar&id='+id}).then(r=>r.json()).then(res=>{if(res.success){tr.querySelector('.jam-keluar').textContent=new Date().toLocaleTimeString();btn.disabled=true;btn.classList.replace('btn-success','btn-secondary');}});}}});

// Tombol hapus
document.querySelectorAll('.btn-delete').forEach(btn=>{
btn.onclick=()=>{
const tr=btn.closest('tr');
const id=tr.dataset.id;
if(confirm('Hapus pengunjung ini?')){fetch('dashboard.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'action=delete_tamu&id='+id}).then(r=>r.json()).then(res=>{if(res.success){tr.remove();}});}}});

// Search langsung
const searchInput=document.getElementById('searchInput');
searchInput.addEventListener('keyup',e=>{if(e.key==='Enter'){document.getElementById('filterForm').submit();}});
})();
</script>
</body>
</html> 