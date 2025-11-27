<?php
require_once '../config.php'; // pastikan path config.php benar

// Ganti sesuai username admin yang mau di-reset
$username = 'admin';

// Ganti password baru di bawah
$new_password_plain = '12345678';

// Buat hash dari password baru
$new_password_hash = password_hash($new_password_plain, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("UPDATE admins SET password = :password WHERE username = :username");
    $stmt->execute([
        ':password' => $new_password_hash,
        ':username' => $username
    ]);

    if ($stmt->rowCount() > 0) {
        echo "Password berhasil di-reset!<br>";
        echo "Username: <b>$username</b><br>";
        echo "Password baru: <b>$new_password_plain</b><br>";
        echo "Silakan login sekarang.";
    } else {
        echo "Username '$username' tidak ditemukan atau password sudah sama.";
    }

} catch (PDOException $e) {
    echo "Terjadi error: " . $e->getMessage();
}
?>