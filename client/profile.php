<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireClient();

$userId = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = clean($_POST['prenom']);
    $nom = clean($_POST['nom']);
    $email = clean($_POST['email']);
    $telephone = clean($_POST['telephone']);
    $adresse = clean($_POST['adresse']);

    if (empty($prenom) || empty($nom) || empty($email)) {
        $error = "Veuillez remplir tous les champs obligatoires.";
    } else {
        // Vérifier si l'email existe déjà pour un autre utilisateur
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email AND id != :id");
        $stmt->execute([':email' => $email, ':id' => $userId]);
        if ($stmt->fetch()) {
            $error = "Cet email est déjà utilisé par un autre compte.";
        } else {
            $stmt = $pdo->prepare("UPDATE utilisateurs SET prenom = :prenom, nom = :nom, email = :email, telephone = :telephone, adresse = :adresse WHERE id = :id");
            $stmt->execute([
                ':prenom' => $prenom,
                ':nom' => $nom,
                ':email' => $email,
                ':telephone' => $telephone,
                ':adresse' => $adresse,
                ':id' => $userId
            ]);
            
            $_SESSION['user_prenom'] = $prenom;
            $_SESSION['user_nom'] = $nom;
            $_SESSION['user_email'] = $email;
            
            $success = "Profil mis à jour avec succès.";
        }
    }
}

// Récupérer les infos à jour
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = :id");
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch();

$pageTitle = "Mon profil";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - AutoPartage</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="dashboard">
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <header class="dashboard-header">
            <h1>Mon profil</h1>
        </header>

        <?php if ($error): ?>
            <div class="flash flash-error"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="flash flash-success"><?= $success ?></div>
        <?php endif; ?>

        <div class="profile-card">
            <div class="flex gap-3 mb-4" style="align-items: center;">
                <div style="width: 100px; height: 100px; border-radius: 50%; background: var(--bg-alt); display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700; border: 4px solid #fff; box-shadow: var(--shadow);">
                    <?= strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)) ?>
                </div>
                <div>
                    <h2><?= clean($user['prenom'] . ' ' . $user['nom']) ?></h2>
                    <p style="color: var(--secondary);"><?= clean($user['email']) ?></p>
                </div>
            </div>

            <form action="profile.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="prenom">Prénom</label>
                        <input type="text" name="prenom" id="prenom" class="form-control" value="<?= clean($user['prenom']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="nom">Nom</label>
                        <input type="text" name="nom" id="nom" class="form-control" value="<?= clean($user['nom']) ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?= clean($user['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="text" name="telephone" id="telephone" class="form-control" value="<?= clean($user['telephone']) ?>">
                </div>
                <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <textarea name="adresse" id="adresse" class="form-control" rows="3"><?= clean($user['adresse']) ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
            </form>
            
            <div class="mt-4 pt-4" style="border-top: 1px solid var(--border);">
                <h3>Sécurité</h3>
                <p class="mb-3" style="color: var(--secondary); font-size: 0.9rem;">Vous pouvez changer votre mot de passe ici.</p>
                <button class="btn btn-outline">Changer le mot de passe</button>
            </div>
        </div>
    </main>
</body>
</html>
