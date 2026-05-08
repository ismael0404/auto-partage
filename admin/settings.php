<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Protéger la page
requireAdmin();

$adminId = $_SESSION['user_id'];
$success = "";
$error = "";

// Récupérer les infos actuelles de l'admin
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = :id");
$stmt->execute([':id' => $adminId]);
$admin = $stmt->fetch();

if (!$admin) {
    die("Erreur : Administrateur non trouvé.");
}

// Traitement de la mise à jour du profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $prenom = clean($_POST['prenom']);
    $nom = clean($_POST['nom']);
    $email = clean($_POST['email']);
    $telephone = clean($_POST['telephone']);

    if (empty($prenom) || empty($nom) || empty($email)) {
        $error = "Veuillez remplir tous les champs obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format d'email invalide.";
    } else {
        // Vérifier si l'email est déjà utilisé par un autre utilisateur
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email AND id != :id");
        $stmt->execute([':email' => $email, ':id' => $adminId]);
        if ($stmt->fetch()) {
            $error = "Cet email est déjà utilisé par un autre compte.";
        } else {
            // Mise à jour
            $stmt = $pdo->prepare("UPDATE utilisateurs SET prenom = :prenom, nom = :nom, email = :email, telephone = :telephone WHERE id = :id");
            if ($stmt->execute([
                ':prenom' => $prenom,
                ':nom' => $nom,
                ':email' => $email,
                ':telephone' => $telephone,
                ':id' => $adminId
            ])) {
                $_SESSION['user_prenom'] = $prenom;
                $_SESSION['user_nom'] = $nom;
                setFlash('success', 'Profil mis à jour avec succès.');
                redirect('/admin/settings.php');
            } else {
                $error = "Une erreur est survenue lors de la mise à jour.";
            }
        }
    }
}

// Traitement du changement de mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $old_pass = $_POST['old_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if (empty($old_pass) || empty($new_pass) || empty($confirm_pass)) {
        $error = "Veuillez remplir tous les champs de mot de passe.";
    } elseif ($new_pass !== $confirm_pass) {
        $error = "Le nouveau mot de passe et sa confirmation ne correspondent pas.";
    } elseif (strlen($new_pass) < 6) {
        $error = "Le nouveau mot de passe doit faire au moins 6 caractères.";
    } else {
        // Vérifier l'ancien mot de passe
        if (password_verify($old_pass, $admin['mot_de_passe'])) {
            $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = :pass WHERE id = :id");
            if ($stmt->execute([':pass' => $hashed_pass, ':id' => $adminId])) {
                setFlash('success', 'Mot de passe modifié avec succès.');
                redirect('/admin/settings.php');
            } else {
                $error = "Erreur lors de la modification du mot de passe.";
            }
        } else {
            $error = "L'ancien mot de passe est incorrect.";
        }
    }
}

$pageTitle = "Paramètres Admin";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - AutoPartage</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="dashboard">
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <header class="dashboard-header">
            <h1>Paramètres de configuration</h1>
            <div class="user-info">
                <span class="badge badge-info">Compte Administrateur</span>
            </div>
        </header>

        <?php if ($error): ?>
            <div class="flash flash-error"><?= $error ?></div>
        <?php endif; ?>

        <?php $flash = getFlash(); if ($flash): ?>
            <div class="flash flash-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
        <?php endif; ?>

        <div class="grid-2">
            <!-- Section Profil -->
            <div class="profile-card" style="max-width: 100%;">
                <h2>Informations personnelles</h2>
                <form action="settings.php" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="prenom">Prénom</label>
                            <input type="text" name="prenom" id="prenom" class="form-control" value="<?= clean($admin['prenom']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="nom">Nom</label>
                            <input type="text" name="nom" id="nom" class="form-control" value="<?= clean($admin['nom']) ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email professionnel</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= clean($admin['email']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="text" name="telephone" id="telephone" class="form-control" value="<?= clean($admin['telephone']) ?>">
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary">Enregistrer les modifications</button>
                </form>
            </div>

            <!-- Section Sécurité -->
            <div class="profile-card" style="max-width: 100%;">
                <h2>Sécurité du compte</h2>
                <form action="settings.php" method="POST">
                    <div class="form-group">
                        <label for="old_password">Ancien mot de passe</label>
                        <input type="password" name="old_password" id="old_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">Nouveau mot de passe</label>
                        <input type="password" name="new_password" id="new_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirmer le nouveau mot de passe</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" name="update_password" class="btn btn-outline">Mettre à jour le mot de passe</button>
                </form>
            </div>
        </div>

        <!-- Section Information Système -->
        <div class="table-container mt-4" style="background: #fff; padding: 32px; border-radius: var(--radius);">
            <div class="flex gap-2 items-center mb-3">
                <i class="fas fa-info-circle" style="color: var(--info); font-size: 1.5rem;"></i>
                <h3 style="font-size: 1.2rem; font-weight: 700;">Informations Système</h3>
            </div>
            <p style="color: var(--secondary); font-size: 0.9rem; line-height: 1.6;">
                Cette console d'administration vous permet de gérer l'intégralité de la plateforme AutoPartage. 
                Assurez-vous de maintenir vos identifiants en sécurité. En cas de suspicion d'accès non autorisé, 
                veuillez changer votre mot de passe immédiatement.
            </p>
            <div class="grid-3 mt-3" style="border-top: 1px solid var(--border); padding-top: 20px;">
                <div>
                    <div style="font-size: 0.75rem; color: var(--secondary); text-transform: uppercase;">Version</div>
                    <div style="font-weight: 700;">v1.0.4 - Premium</div>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--secondary); text-transform: uppercase;">PHP Version</div>
                    <div style="font-weight: 700;"><?= phpversion() ?></div>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--secondary); text-transform: uppercase;">Dernière connexion</div>
                    <div style="font-weight: 700;"><?= date('d/m/Y H:i') ?></div>
                </div>
            </div>
        </div>
    </main>

    <script src="../assets/js/app.js"></script>
</body>
</html>
