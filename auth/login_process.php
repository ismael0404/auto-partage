<?php
// auth/login_process.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /Projet_Auto/auth/login.php');
    exit();
}

$email = sanitize($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    setFlashMessage("Tous les champs sont obligatoires.", "error");
    header('Location: /Projet_Auto/auth/login.php');
    exit();
}

$stmt = $pdo->prepare("SELECT id, prenom, nom, email, password, role FROM users WHERE email = ? AND deleted_at IS NULL");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    // Connexion rÃ©ussie
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
    $_SESSION['user_role'] = $user['role'];

    setFlashMessage("Bienvenue, " . htmlspecialchars($user['prenom']) . " !", "success");
    
    if ($user['role'] === 'admin') {
        header('Location: /Projet_Auto/admin/dashboard.php');
    } else {
        header('Location: /Projet_Auto/client/dashboard.php');
    }
    exit();
} else {
    setFlashMessage("Email ou mot de passe incorrect.", "error");
    header('Location: /Projet_Auto/auth/login.php');
    exit();
}

