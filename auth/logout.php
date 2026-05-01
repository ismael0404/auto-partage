<?php
// auth/logout.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../includes/functions.php';

// DÃ©truire toutes les variables de session
$_SESSION = array();

// DÃ©truire le cookie de session si prÃ©sent
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// DÃ©truire la session
session_destroy();

// DÃ©marrer une nouvelle session pour le flash message
if (session_status() === PHP_SESSION_NONE) { session_start(); }
setFlashMessage("Vous avez Ã©tÃ© dÃ©connectÃ© avec succÃ¨s.", "info");

header('Location: /Projet_Auto/auth/login.php');
exit();

