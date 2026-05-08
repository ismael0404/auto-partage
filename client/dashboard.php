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
            <div>
                <h1>Ravi de vous revoir, <?= $_SESSION['user_prenom'] ?> 👋</h1>
                <p style="color: var(--secondary); font-size: 0.9rem;">Voici un aperçu de votre activité sur AutoPartage.</p>
            </div>
            <div class="user-info">
                <a href="profile.php" class="btn btn-outline btn-sm">Mon profil</a>
            </div>
        </header>

        <?php $flash = getFlash(); if ($flash): ?>
            <div class="flash flash-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
        <?php endif; ?>

        <div class="stat-cards">
            <div class="stat-card">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="label">Réservations</div>
                        <div class="value"><?= $totalReservations ?></div>
                    </div>
                    <i class="fas fa-calendar-check" style="font-size: 1.5rem; color: var(--primary); opacity: 0.5;"></i>
                </div>
            </div>
            <div class="stat-card">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="label">En attente</div>
                        <div class="value"><?= $pendingReservations ?></div>
                    </div>
                    <i class="fas fa-clock" style="font-size: 1.5rem; color: var(--warning); opacity: 0.5;"></i>
                </div>
            </div>
            <div class="stat-card">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="label">Messages</div>
                        <div class="value"><?= countUnreadChatMessages($pdo, $userId) ?></div>
                    </div>
                    <i class="fas fa-comment-dots" style="font-size: 1.5rem; color: var(--info); opacity: 0.5;"></i>
                </div>
            </div>
            <div class="stat-card dark">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="label" style="color: rgba(255,255,255,0.7);">Notifications</div>
                        <div class="value" style="color: #fff;"><?= countUnreadMessages($pdo, $userId) ?></div>
                    </div>
                    <i class="fas fa-bell" style="font-size: 1.5rem; color: #fff; opacity: 0.5;"></i>
                </div>
            </div>
        </div>

        <div class="section-title mt-4">
            <h3>Accès Rapide</h3>
        </div>
        <div class="grid-2 mb-4">
            <a href="messages.php" class="stat-card" style="text-decoration: none; display: flex; align-items: center; gap: 20px; transition: transform 0.3s;">
                <div style="width: 50px; height: 50px; background: #e0f2fe; color: #0369a1; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="fas fa-comments"></i>
                </div>
                <div>
                    <h4 style="color: #111;">Discuter avec l'admin</h4>
                    <p style="font-size: 0.8rem; color: var(--secondary);">Besoin d'aide ? Envoyez-nous un message.</p>
                </div>
            </a>
            <a href="notifications.php" class="stat-card" style="text-decoration: none; display: flex; align-items: center; gap: 20px; transition: transform 0.3s;">
                <div style="width: 50px; height: 50px; background: #fef3c7; color: #92400e; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="fas fa-bell"></i>
                </div>
                <div>
                    <h4 style="color: #111;">Mes alertes</h4>
                    <p style="font-size: 0.8rem; color: var(--secondary);">Consultez vos dernières notifications système.</p>
                </div>
            </a>
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
