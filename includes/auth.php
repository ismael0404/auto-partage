<?php
// includes/auth.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * VÃ©rifie si l'utilisateur est connectÃ©
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * VÃ©rifie si l'utilisateur a le rÃ´le requis (ex: 'admin')
 */
function hasRole($role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

/**
 * Redirige l'utilisateur s'il n'est pas connectÃ©
 */
function requireLogin() {
    if (!isLoggedIn()) {
        setFlashMessage("Veuillez vous connecter pour accÃ©der Ã  cette page.", "warning");
        header("Location: /Projet_Auto/auth/login.php");
        exit();
    }
}

/**
 * Redirige l'utilisateur s'il n'a pas le rÃ´le requis
 */
function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        setFlashMessage("AccÃ¨s refusÃ©. Vous n'avez pas les droits nÃ©cessaires.", "error");
        
        // Redirection selon le rÃ´le
        if (hasRole('client')) {
            header("Location: /Projet_Auto/client/dashboard.php");
        } else {
            header("Location: /Projet_Auto/index.php");
        }
        exit();
    }
}

/**
 * Redirige les utilisateurs dÃ©jÃ  connectÃ©s (pour login/register)
 */
function redirectIfConnected() {
    if (isLoggedIn()) {
        if (hasRole('admin')) {
            header("Location: /Projet_Auto/admin/dashboard.php");
        } else {
            header("Location: /Projet_Auto/client/dashboard.php");
        }
        exit();
    }
}

