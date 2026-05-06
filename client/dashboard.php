<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireClient();

$userId = $_SESSION['user_id'];

// Statistiques client
$stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE utilisateur_id = :uid");
$stmt->execute([':uid' => $userId]);
$totalReservations = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE utilisateur_id = :uid AND statut = 'en_attente'");
$stmt->execute([':uid' => $userId]);
$pendingReservations = $stmt->fetchColumn();

// Dernières réservations
$stmt = $pdo->prepare("SELECT r.*, v.marque, v.modele, v.image 
                       FROM reservations r 
                       JOIN vehicules v ON r.vehicule_id = v.id 
                       WHERE r.utilisateur_id = :uid 
                       ORDER BY r.date_creation DESC LIMIT 5");
$stmt->execute([':uid' => $userId]);
$recentReservations = $stmt->fetchAll();

$pageTitle = "Tableau de bord";
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
            <h1>Bienvenue, <?= $_SESSION['user_prenom'] ?></h1>
            <div class="user-info">
                <a href="profile.php" class="btn btn-outline btn-sm">Mon profil</a>
            </div>
        </header>

        <?php $flash = getFlash(); if ($flash): ?>
            <div class="flash flash-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
        <?php endif; ?>

        <div class="stat-cards">
            <div class="stat-card">
                <div class="label">Total réservations</div>
                <div class="value"><?= $totalReservations ?></div>
            </div>
            <div class="stat-card">
                <div class="label">En attente</div>
                <div class="value"><?= $pendingReservations ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Messages non lus</div>
                <div class="value"><?= countUnreadMessages($pdo, $userId) ?></div>
            </div>
            <div class="stat-card dark">
                <div class="label">Statut compte</div>
                <div class="value">Actif</div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h3>Mes réservations récentes</h3>
                <a href="reservations.php" class="btn btn-outline btn-sm">Voir tout</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Véhicule</th>
                        <th>Période</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentReservations)): ?>
                        <tr>
                            <td colspan="5" class="text-center">Vous n'avez pas encore de réservations.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentReservations as $res): ?>
                        <tr>
                            <td>
                                <div class="flex gap-1">
                                    <img src="<?= getVehiculeImage($res['image']) ?>" alt="" style="width: 40px; height: 30px; object-fit: cover; border-radius: 4px;">
                                    <strong><?= clean($res['marque'] . ' ' . $res['modele']) ?></strong>
                                </div>
                            </td>
                            <td>
                                <?= formatDate($res['date_debut']) ?> - <?= formatDate($res['date_fin']) ?>
                            </td>
                            <td><strong><?= formatPrix($res['prix_total']) ?></strong></td>
                            <td><?= getStatutBadge($res['statut']) ?></td>
                            <td>
                                <a href="reservations.php" class="btn btn-outline btn-sm">Détails</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="section-title mt-4">
            <h2>Recommandé pour vous</h2>
            <a href="vehicles.php">Voir tout &rarr;</a>
        </div>
        <div class="grid-4">
            <?php
            $stmt = $pdo->query("SELECT * FROM vehicules WHERE statut = 'disponible' AND is_deleted = 0 LIMIT 4");
            $reco = $stmt->fetchAll();
            foreach ($reco as $v):
            ?>
            <div class="vehicle-card">
                <img src="<?= getVehiculeImage($v['image']) ?>" alt="" class="vehicle-card-img" style="height: 150px;">
                <div class="vehicle-card-body">
                    <h3><?= clean($v['marque'] . ' ' . $v['modele']) ?></h3>
                    <p class="price" style="font-size: 0.9rem;"><?= formatPrix($v['prix_jour']) ?></p>
                    <a href="vehicle_detail.php?id=<?= $v['id'] ?>" class="btn btn-primary btn-sm btn-block mt-1">Réserver</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>
