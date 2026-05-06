<?php
/**
 * Header public (pages non-dashboard)
 */
$base = BASE_URL;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AutoPartage - Louez une voiture en toute simplicité. Plateforme d'autopartage rapide, sécurisée et économique.">
    <title><?= $pageTitle ?? 'AutoPartage' ?> - AutoPartage</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/style.css">
</head>
<body>
    <header class="header">
        <div class="container flex-between">
            <a href="<?= $base ?>/index.php" class="logo">
                <span class="icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                </span> AutoPartage
            </a>
            <nav class="nav">
                <a href="<?= $base ?>/index.php">Accueil</a>
                <a href="<?= $base ?>/client/vehicles.php">Véhicules</a>
                <a href="#how-it-works">Comment ça marche</a>
                <a href="#about">À propos</a>
            </nav>
            <div class="nav-actions">
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <a href="<?= $base ?>/admin/dashboard.php" class="btn btn-primary btn-sm">Dashboard</a>
                    <?php else: ?>
                        <a href="<?= $base ?>/client/dashboard.php" class="btn btn-primary btn-sm">Tableau de bord</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?= $base ?>/auth/login.php" class="btn btn-outline btn-sm">Se connecter</a>
                    <a href="<?= $base ?>/auth/register.php" class="btn btn-primary btn-sm">S'inscrire</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <?php
    $flash = getFlash();
    if ($flash): ?>
    <div class="container mt-2">
        <div class="flash flash-<?= $flash['type'] ?>"><?= clean($flash['message']) ?></div>
    </div>
    <?php endif; ?>
