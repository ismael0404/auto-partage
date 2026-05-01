<?php
// auth/register_process.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /Projet_Auto/auth/register.php');
    exit();
}

$prenom = sanitize($_POST['prenom'] ?? '');
$nom = sanitize($_POST['nom'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$telephone = sanitize($_POST['telephone'] ?? '');
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

// Validation de base
if (empty($prenom) || empty($nom) || empty($email) || empty($password) || empty($password_confirm)) {
    setFlashMessage("Veuillez remplir tous les champs obligatoires.", "error");
    header('Location: /Projet_Auto/auth/register.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setFlashMessage("Format d'email invalide.", "error");
    header('Location: /Projet_Auto/auth/register.php');
    exit();
}

if ($password !== $password_confirm) {
    setFlashMessage("Les mots de passe ne correspondent pas.", "error");
    header('Location: /Projet_Auto/auth/register.php');
    exit();
}

if (strlen($password) < 8) {
    setFlashMessage("Le mot de passe doit contenir au moins 8 caractÃ¨res.", "warning");
    header('Location: /Projet_Auto/auth/register.php');
    exit();
}

// VÃ©rifier si l'email existe dÃ©jÃ 
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    setFlashMessage("Cet email est dÃ©jÃ  utilisÃ©.", "error");
    header('Location: /Projet_Auto/auth/register.php');
    exit();
}

// Hachage du mot de passe
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insertion dans la base de donnÃ©es
try {
    $stmt = $pdo->prepare("INSERT INTO users (prenom, nom, email, telephone, password, role) VALUES (?, ?, ?, ?, ?, 'client')");
    $stmt->execute([$prenom, $nom, $email, $telephone, $hashed_password]);
    
    // Connexion automatique aprÃ¨s inscription
    $user_id = $pdo->lastInsertId();
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $prenom . ' ' . $nom;
    $_SESSION['user_role'] = 'client';
    
    setFlashMessage("Compte crÃ©Ã© avec succÃ¨s ! Bienvenue.", "success");
    header('Location: /Projet_Auto/client/dashboard.php');
    exit();
} catch (PDOException $e) {
    setFlashMessage("Erreur lors de la crÃ©ation du compte.", "error");
    header('Location: /Projet_Auto/auth/register.php');
    exit();
}

