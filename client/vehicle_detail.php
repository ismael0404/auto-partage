<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('/client/vehicles.php');
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM vehicules WHERE id = :id");
$stmt->execute([':id' => $id]);
$vehicle = $stmt->fetch();

if (!$vehicle) {
    setFlash('error', "Véhicule non trouvé.");
    redirect('/client/vehicles.php');
}

$pageTitle = $vehicle['marque'] . ' ' . $vehicle['modele'];
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
            <a href="vehicles.php" class="back-link">&larr; Retour aux véhicules</a>
            <div class="user-info">
                 <button class="btn btn-outline btn-sm"><i class="far fa-heart"></i></button>
            </div>
        </header>

        <div class="detail-page">
            <div class="detail-grid">
                <div class="detail-image">
                    <img src="<?= getVehiculeImage($vehicle['image']) ?>" alt="<?= clean($vehicle['marque'] . ' ' . $vehicle['modele']) ?>">
                    <div class="grid-4 mt-2">
                        <img src="<?= getVehiculeImage($vehicle['image']) ?>" style="height: 80px; object-fit: cover; border-radius: 4px;">
                        <img src="<?= getVehiculeImage($vehicle['image']) ?>" style="height: 80px; object-fit: cover; border-radius: 4px; opacity: 0.6;">
                        <img src="<?= getVehiculeImage($vehicle['image']) ?>" style="height: 80px; object-fit: cover; border-radius: 4px; opacity: 0.6;">
                        <div style="height: 80px; background: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">+</div>
                    </div>
                </div>
                <div class="detail-info">
                    <div class="flex-between mb-2">
                        <h1><?= clean($vehicle['marque'] . ' ' . $vehicle['modele']) ?></h1>
                        <?= getVehiculeStatutBadge($vehicle['statut']) ?>
                    </div>
                    
                    <div class="detail-specs">
                        <div class="spec-tag"><i class="fas fa-bolt"></i> <?= clean($vehicle['type_carburant']) ?></div>
                        <div class="spec-tag"><i class="fas fa-calendar-alt"></i> <?= $vehicle['annee'] ?></div>
                        <div class="spec-tag"><i class="fas fa-cogs"></i> <?= clean($vehicle['transmission']) ?></div>
                        <div class="spec-tag"><i class="fas fa-users"></i> <?= $vehicle['nombre_places'] ?> places</div>
                        <div class="spec-tag"><i class="fas fa-snowflake"></i> Climatisation</div>
                    </div>

                    <div class="detail-price">
                        <?= formatPrix($vehicle['prix_jour']) ?> <span>/ jour</span>
                    </div>

                    <p class="mb-4" style="color: var(--secondary);"><?= nl2br(clean($vehicle['description'])) ?></p>

                    <?php if ($vehicle['statut'] === 'disponible'): ?>
                        <a href="reserve.php?id=<?= $vehicle['id'] ?>" class="btn btn-primary btn-lg btn-block">Réserver maintenant</a>
                    <?php else: ?>
                        <button class="btn btn-primary btn-lg btn-block" disabled>Indisponible actuellement</button>
                    <?php endif; ?>

                    <div class="detail-tabs">
                        <div class="tabs-header">
                            <button class="tab-btn active">Description</button>
                            <button class="tab-btn">Caractéristiques</button>
                            <button class="tab-btn">Conditions</button>
                        </div>
                        <div class="tab-content">
                            <?= nl2br(clean($vehicle['caracteristiques'])) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
