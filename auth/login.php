<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (isLoggedIn()) {
    redirect(isAdmin() ? '/admin/dashboard.php' : '/client/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['mot_de_passe'])) {
            if ($user['statut'] !== 'actif') {
                $error = "Votre compte est " . $user['statut'] . ". Veuillez contacter l'administrateur.";
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_prenom'] = $user['prenom'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                setFlash('success', "Bienvenue, " . $user['prenom'] . " !");
                redirect($user['role'] === 'admin' ? '/admin/dashboard.php' : '/client/dashboard.php');
            }
        } else {
            $error = "Identifiants invalides.";
        }
    }
}

$pageTitle = "Connexion";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - AutoPartage</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-page">
        <div class="auth-left">
            <a href="../index.php" class="logo">
                <span class="icon" style="background:#fff; color:var(--primary)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                </span> AutoPartage
            </a>
            <h2>Bienvenue !</h2>
            <p>Connectez-vous à votre compte pour continuer votre expérience d'autopartage.</p>
            <div class="mt-4">
                <img src="../assets/images/vehicules/voiture de page de connexion et inscprition.jfif" alt="Login Image" style="width: 80%; border-radius: 12px; opacity: 0.8;">
            </div>
        </div>
        <div class="auth-right">
            <div class="auth-form">
                <h2>Connexion</h2>
                <p class="subtitle">Entrez vos identifiants pour accéder à votre espace.</p>
                
                <?php if ($error): ?>
                    <div class="flash flash-error"><?= $error ?></div>
                <?php endif; ?>
                
                <?php $flash = getFlash(); if ($flash): ?>
                    <div class="flash flash-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="votre@email.com" required value="<?= isset($_POST['email']) ? clean($_POST['email']) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <div class="form-footer">
                        <div class="form-check">
                            <input type="checkbox" id="remember">
                            <label for="remember">Se souvenir de moi</label>
                        </div>
                        <a href="#">Mot de passe oublié ?</a>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block mt-4">Se connecter</button>
                    <p class="text-center mt-4" style="font-size: 0.9rem; color: var(--secondary);">
                        Vous n'avez pas de compte ? <a href="register.php" style="color: var(--primary); font-weight: 600;">S'inscrire</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
