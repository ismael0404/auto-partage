<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (isLoggedIn()) {
    redirect(isAdmin() ? '/admin/dashboard.php' : '/client/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = clean($_POST['prenom']);
    $nom = clean($_POST['nom']);
    $email = clean($_POST['email']);
    $telephone = clean($_POST['telephone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($prenom) || empty($nom) || empty($email) || empty($password)) {
        $error = "Veuillez remplir tous les champs obligatoires.";
    } elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit contenir au moins 6 caractères.";
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $error = "Cet email est déjà utilisé.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (prenom, nom, email, telephone, mot_de_passe) VALUES (:prenom, :nom, :email, :telephone, :password)");
            
            try {
                $stmt->execute([
                    ':prenom' => $prenom,
                    ':nom' => $nom,
                    ':email' => $email,
                    ':telephone' => $telephone,
                    ':password' => $hashed_password
                ]);
                
                // Ajouter un message de bienvenue
                $userId = $pdo->lastInsertId();
                $stmtMsg = $pdo->prepare("INSERT INTO messages (utilisateur_id, titre, contenu, type) VALUES (:uid, 'Bienvenue !', 'Bienvenue sur AutoPartage. Nous sommes ravis de vous compter parmi nos clients.', 'success')");
                $stmtMsg->execute([':uid' => $userId]);

                setFlash('success', "Compte créé avec succès. Vous pouvez maintenant vous connecter.");
                redirect('/auth/login.php');
            } catch (PDOException $e) {
                $error = "Une erreur est survenue lors de la création du compte.";
            }
        }
    }
}

$pageTitle = "Inscription";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - AutoPartage</title>
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
            <h2>Rejoignez-nous !</h2>
            <p>Créez votre compte et commencez votre expérience d'autopartage en toute liberté.</p>
            <div class="mt-4">
                <img src="../assets/images/vehicules/voiture de page de connexion et inscprition.jfif" alt="Register Image" style="width: 80%; border-radius: 12px; opacity: 0.8;">
            </div>
        </div>
        <div class="auth-right">
            <div class="auth-form" style="max-width: 500px;">
                <h2>Créer un compte</h2>
                <p class="subtitle">Remplissez le formulaire pour rejoindre notre communauté.</p>
                
                <?php if ($error): ?>
                    <div class="flash flash-error"><?= $error ?></div>
                <?php endif; ?>

                <form action="register.php" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="prenom">Prénom</label>
                            <input type="text" name="prenom" id="prenom" class="form-control" placeholder="Jean" required value="<?= isset($_POST['prenom']) ? clean($_POST['prenom']) : '' ?>">
                        </div>
                        <div class="form-group">
                            <label for="nom">Nom</label>
                            <input type="text" name="nom" id="nom" class="form-control" placeholder="Dupont" required value="<?= isset($_POST['nom']) ? clean($_POST['nom']) : '' ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="jean.dupont@email.com" required value="<?= isset($_POST['email']) ? clean($_POST['email']) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="text" name="telephone" id="telephone" class="form-control" placeholder="+225 00 00 00 00" value="<?= isset($_POST['telephone']) ? clean($_POST['telephone']) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirmer le mot de passe</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="••••••••" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block mt-4">S'inscrire</button>
                    <p class="text-center mt-4" style="font-size: 0.9rem; color: var(--secondary);">
                        Vous avez déjà un compte ? <a href="login.php" style="color: var(--primary); font-weight: 600;">Se connecter</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
