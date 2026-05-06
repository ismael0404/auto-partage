<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Filtres
$where = " WHERE 1=1";
$params = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $where .= " AND (marque LIKE :search OR modele LIKE :search)";
    $params[':search'] = '%' . $_GET['search'] . '%';
}

if (isset($_GET['carburant']) && !empty($_GET['carburant'])) {
    $where .= " AND type_carburant = :carburant";
    $params[':carburant'] = $_GET['carburant'];
}

if (isset($_GET['prix_max']) && !empty($_GET['prix_max'])) {
    $where .= " AND prix_jour <= :prix_max";
    $params[':prix_max'] = $_GET['prix_max'];
}

// Seulement les véhicules disponibles ou réservés (mais pas en maintenance pour les clients)
$where .= " AND statut != 'maintenance' AND is_deleted = 0";

$sql = "SELECT * FROM vehicules" . $where . " ORDER BY date_creation DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$vehicles = $stmt->fetchAll();

$pageTitle = "Véhicules disponibles";
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
            <h1>Nos véhicules disponibles</h1>
            <div class="user-info flex gap-2">
                <?php if (isLoggedIn()): ?>
                    <span>Bonjour, <strong><?= $_SESSION['user_prenom'] ?></strong></span>
                <?php else: ?>
                    <a href="../auth/login.php" class="btn btn-outline btn-sm">Se connecter</a>
                <?php endif; ?>
            </div>
        </header>

        <?php $flash = getFlash(); if ($flash): ?>
            <div class="flash flash-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
        <?php endif; ?>

        <form action="vehicles.php" method="GET" class="filters">
            <div class="search-box">
                <input type="text" name="search" placeholder="Rechercher un véhicule..." value="<?= isset($_GET['search']) ? clean($_GET['search']) : '' ?>">
            </div>
            <select name="carburant" class="form-control" style="width: auto;">
                <option value="">Type de carburant</option>
                <option value="Essence" <?= isset($_GET['carburant']) && $_GET['carburant'] === 'Essence' ? 'selected' : '' ?>>Essence</option>
                <option value="Diesel" <?= isset($_GET['carburant']) && $_GET['carburant'] === 'Diesel' ? 'selected' : '' ?>>Diesel</option>
                <option value="Électrique" <?= isset($_GET['carburant']) && $_GET['carburant'] === 'Électrique' ? 'selected' : '' ?>>Électrique</option>
                <option value="Hybride" <?= isset($_GET['carburant']) && $_GET['carburant'] === 'Hybride' ? 'selected' : '' ?>>Hybride</option>
            </select>
            <input type="number" name="prix_max" placeholder="Prix max / jour" class="form-control" style="width: auto;" value="<?= isset($_GET['prix_max']) ? clean($_GET['prix_max']) : '' ?>">
            <button type="submit" class="btn btn-primary">Filtrer</button>
            <a href="vehicles.php" class="btn btn-outline">Réinitialiser</a>
        </form>

        <div class="grid-3">
            <?php if (empty($vehicles)): ?>
                <p>Aucun véhicule ne correspond à votre recherche.</p>
            <?php else: ?>
                <?php foreach ($vehicles as $vehicle): ?>
                <div class="vehicle-card">
                    <img src="<?= getVehiculeImage($vehicle['image']) ?>" alt="<?= clean($vehicle['marque'] . ' ' . $vehicle['modele']) ?>" class="vehicle-card-img">
                    <div class="vehicle-card-body">
                        <div class="flex-between mb-1">
                            <p class="type"><?= clean($vehicle['type_carburant']) ?></p>
                            <?= getVehiculeStatutBadge($vehicle['statut']) ?>
                        </div>
                        <h3><?= clean($vehicle['marque'] . ' ' . $vehicle['modele']) ?></h3>
                        <p class="price"><?= formatPrix($vehicle['prix_jour']) ?> <span>/ jour</span></p>
                        <a href="vehicle_detail.php?id=<?= $vehicle['id'] ?>" class="btn btn-primary btn-sm btn-block mt-2">Voir détails</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
