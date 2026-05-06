<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Démarrer une nouvelle session pour le message flash
session_start();
setFlash('info', "Vous avez été déconnecté.");

header("Location: " . BASE_URL . "/index.php");
exit;
