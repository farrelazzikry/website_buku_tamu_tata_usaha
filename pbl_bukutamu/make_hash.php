<?php
// make_hash.php — buat hash password baru
if (php_sapi_name() === 'cli') {
    $pw = $argv[1] ?? '';
} else {
    $pw = $_GET['pw'] ?? '';
}
if (!$pw) {
    echo "Gunakan: /make_hash.php?pw=PASSWORD\n";
    exit;
}
echo password_hash($pw, PASSWORD_DEFAULT) . "\n";
