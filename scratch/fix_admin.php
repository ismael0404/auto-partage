<?php
require_once 'config/database.php';
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = :hash WHERE email = 'admin@autopartage.com'");
$stmt->execute([':hash' => $hash]);
echo "Password updated successfully with hash: " . $hash;
