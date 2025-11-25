<?php
session_start();
require_once '../config.php';

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$login_role = $_SESSION['admin_role'] ?? 'admin';
$login_id = $_SESSION['admin_id'] ?? 0;

// Hapus admin
if (isset($_GET['delete'])) {
    if ($login_role !== 'superadmin') {
        $msg_error = "Akses ditolak: hanya Superadmin yang dapat menghapus admin.";
    } else {
        $id = $_GET['delete'];
        try {
            $stmt = $pdo->prepare("DELETE FROM admin WHERE id=:id");
            $stmt->execute([':id'=>$id]);
            $msg_success = "Admin berhasil dihapus!";
        } catch (PDOException $e) {
            $msg_error = "Error: ".$e->getMessage();
        }
    }
}

// Tambah admin
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    try {
        $stmt = $pdo->prepare("INSERT INTO admin (name, username, password, role) VALUES (:name, :username, :password, :role)");
        $stmt->execute([':name'=>$name, ':username'=>$username, ':password'=>$password, ':role'=>$role]);
        header('Location: manage_admin.php');
        exit;
    } catch (PDOException $e) {
        $msg_error = "Error: ".$e->getMessage();
    }
}

// Ambil semua admin
try {
    $stmt = $pdo->query("SELECT * FROM admin ORDER BY id ASC");
    $admins = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error ambil data admin: ".$e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Admin</title>
<link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{font-family:'Inter',sans-serif;background:#f9f9f9;color:#333;margin:0;padding:0;}
header{display:flex;justify-content:space-between;align-items:center;padding:15px 25px;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.1);}
header h1{margin:0;font-size:1.7rem;font-weight:600;}
header a.logout{padding:8px 16px;background:#dc3545;color:#fff;border-radius:6px;font-weight:500;text-decoration:none;}
header a.logout:hover{background:#c82333;text-decoration:none;}
.container{max-width:1000px;margin:30px auto;padding:0 15px;}
.btn-dashboard{display:inline-flex;align-items:center;gap:6px;padding:10px 16px;background:#0d6efd;color:#fff;border-radius:6px;font-weight:500;margin-bottom:25px;text-decoration:none;}
.btn-dashboard:hover{background:#0b5ed7;text-decoration:none;}
.card{background:#fff;border-radius:12px;padding:25px;box-shadow:0 4px 20px rgba(0,0,0,0.05);margin-bottom:30px;}
h2{color:#0d6efd;margin-bottom:20px;font-weight:600;}
form{display:flex;flex-wrap:wrap;gap:15px;}
form input, form select{padding:12px;flex:1 1 220px;border:1px solid #ccc;border-radius:8px;font-size:1rem;background:#fff;color:#333;}
form input:focus, form select:focus{outline:none;border-color:#0d6efd;box-shadow:0 0 5px rgba(13,110,253,0.2);}
form button{padding:12px 25px;border:none;background:#0d6efd;color:#fff;border-radius:8px;cursor:pointer;font-weight:500;transition:0.3s;}
form button:hover{background:#0b5ed7; transform: translateY(-2px);}
.table-responsive{overflow-x:auto;}
table{width:100%;border-collapse:collapse;min-width:700px;}
table th, table td{padding:12px 10px;text-align:left;border-bottom:1px solid #e0e0e0;}
table th{background:#f5f5f5;font-weight:500;color:#333;}
table tr:hover{background:#f1f5f9;}
.btn{padding:6px 12px;border:none;border-radius:6px;cursor:pointer;display:inline-flex;align-items:center;gap:4px;font-size:0.9rem;transition:0.3s;}
.btn-edit{background:#ffc107;color:#000;}
.btn-edit:hover{background:#e0a800; transform:translateY(-1px);}
.btn-delete{background:#dc3545;color:#fff;}
.btn-delete:hover{background:#c82333; transform:translateY(-1px);}
.role-admin{color:#fff;background:#0d6efd;padding:3px 10px;border-radius:5px;font-weight:500;}
.role-superadmin{color:#fff;background:#dc3545;padding:3px 10px;border-radius:5px;font-weight:500;}
.toast-container{position:fixed;top:20px;right:20px;z-index:1055;}
@media(max-width:768px){form{flex-direction:column;}}
</style>
</head>
<body>

<header style="display:flex; justify-content:space-between; align-items:center; padding:18px 30px; background:#fff; box-shadow:0 2px 5px rgba(0,0,0,0.05); border-bottom:1px solid #e0e0e0;">
    <h1>Kelola Admin</h1>
    <div style="display:flex; align-items:center; gap:15px;">
        <!-- Avatar + Nama Admin -->
        <div style="display:flex; align-items:center; gap:8px;">
            <img src="<?= htmlspecialchars($photoURL) ?>" 
                 alt="Avatar" 
                 style="width:32px; height:32px; border-radius:50%; object-fit:cover; border:1px solid #1976d2;">
            <span style="font-weight:500; color:#1976d2;"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></span>
        </div>
        <a href="logout.php" class="logout" style="padding:6px 12px; font-size:0.9rem;">Logout</a>
    </div>
</header>

<div class="container">
    <?php if(!empty($msg)): ?>
        <div class="alert-msg"><?= $msg ?></div>
    <?php endif; ?>

<div class="container">
    <a href="dashboard.php" class="btn-dashboard"><i class='bx bx-left-arrow-alt'></i> Kembali ke Dashboard</a>
    <div class="card">
        <h2>Tambah Admin</h2>
        <form action="manage_admin.php" method="POST">
            <input type="text" name="name" placeholder="Nama" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role">
                <option value="admin">Admin</option>
                <option value="superadmin">Superadmin</option>
            </select>
            <button type="submit" name="add"><i class='bx bx-user-plus'></i> Tambah</button>
        </form>
    </div>

    <div class="card">
        <h2>Daftar Admin</h2>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($admins as $admin): ?>
                    <tr>
                        <td><?= htmlspecialchars($admin['id'] ?? '') ?></td>
                        <td><?= htmlspecialchars($admin['name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($admin['username'] ?? '') ?></td>
                        <td><?= ($admin['role'] ?? '')=='superadmin' ? '<span class="role-superadmin">Superadmin</span>' : '<span class="role-admin">Admin</span>' ?></td>
                        <td><?= htmlspecialchars($admin['created_at'] ?? '') ?></td>
                        <td>
                            <?php if($login_role === 'superadmin'): ?>
                                <a href="edit_admin.php?id=<?= $admin['id'] ?? '' ?>" class="btn btn-edit"><i class='bx bx-edit'></i> Edit</a>
                                <a href="manage_admin.php?delete=<?= $admin['id'] ?? '' ?>" class="btn btn-delete" onclick="return confirm('Yakin hapus admin ini?')"><i class='bx bx-trash'></i> Hapus</a>
                            <?php else: ?>
                                <?php if($login_id == $admin['id']): ?>
                                    <a href="edit_admin.php?id=<?= $admin['id'] ?? '' ?>" class="btn btn-edit"><i class='bx bx-edit'></i> Edit</a>
                                <?php else: ?>
                                    <button class="btn btn-edit" type="button" onclick="showToast('Akses ditolak: hanya Superadmin yang dapat mengedit admin lain.')"><i class='bx bx-edit'></i> Edit</button>
                                    <button class="btn btn-delete" type="button" onclick="showToast('Akses ditolak: hanya Superadmin yang dapat menghapus admin.')"><i class='bx bx-trash'></i> Hapus</button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="toast-container">
    <div class="toast align-items-center text-bg-danger border-0" role="alert" id="toast">
        <div class="d-flex">
            <div class="toast-body"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function showToast(msg){
    const toastEl = document.getElementById('toast');
    toastEl.querySelector('.toast-body').textContent = msg;
    const toast = new bootstrap.Toast(toastEl, {delay:5000});
    toast.show();
}

<?php if(!empty($msg_error)): ?>
    showToast("<?= addslashes($msg_error) ?>");
<?php endif; ?>
<?php if(!empty($msg_success)): ?>
    showToast("<?= addslashes($msg_success) ?>");
<?php endif; ?>
</script>

</body>
</html>
