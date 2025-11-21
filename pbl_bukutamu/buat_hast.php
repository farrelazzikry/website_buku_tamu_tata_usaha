<?php
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Password hash baru untuk 'admin123' adalah:<br><b>$hash</b>";
?>
