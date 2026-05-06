<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

// Statistiques Globales
$stmt = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'client' AND is_deleted = 0");
$totalClients = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM vehicules WHERE is_deleted = 0");
$totalVehicules = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM reservations");
$totalReservations = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT SUM(prix_total) FROM reservations WHERE statut = 'confirmee' OR statut = 'terminee'");
$totalRevenu = $stmt->fetchColumn() ?: 0;

// Réservations récentes
$stmt = $pdo->query("SELECT r.*, v.marque, v.modele, u.prenom, u.nom 
                     FROM reservations r 
                     JOIN vehicules v ON r.vehicule_id = v.id 
                     JOIN utilisateurs u ON r.utilisateur_id = u.id 
                     ORDER BY r.date_creation DESC LIMIT 5");
$recentReservations = $stmt->fetchAll();

// Clients récents
$stmt = $pdo->query("SELECT * FROM utilisateurs WHERE role = 'client' AND is_deleted = 0 ORDER BY date_creation DESC LIMIT 5");
$recentClients = $stmt->fetchAll();

$pageTitle = "Dashboard Admin";
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
            <h1>Tableau de bord</h1>
            <div class="user-info flex gap-2">
                <span class="badge badge-success">Admin en ligne</span>
                <strong><?= $_SESSION['user_prenom'] ?></strong>
            </div>
        </header>

        <?php $flash = getFlash(); if ($flash): ?>
            <div class="flash flash-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
        <?php endif; ?>

        <div class="stat-cards">
            <div class="stat-card">
                <div class="label">Clients</div>
                <div class="value"><?= $totalClients ?></div>
                <div class="change">+12 ce mois</div>
            </div>
            <div class="stat-card">
                <div class="label">Véhicules</div>
                <div class="value"><?= $totalVehicules ?></div>
                <div class="change">+2 ce mois</div>
            </div>
            <div class="stat-card">
                <div class="label">Réservations</div>
                <div class="value"><?= $totalReservations ?></div>
                <div class="change">+8 ce mois</div>
            </div>
            <div class="stat-card dark">
                <div class="label">Revenus</div>
                <div class="value"><?= formatPrix($totalRevenu) ?></div>
                <div class="change" style="color: #6ee7b7;">+15% vs mois dernier</div>
            </div>
        </div>

        <div class="grid-2">
            <div class="table-container">
                <div class="table-header">
                    <h3>Réservations récentes</h3>
                    <a href="reservations.php" class="btn btn-outline btn-sm">Voir tout</a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Véhicule</th>
                            <th>Date</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentReservations as $res): ?>
                        <tr>
                            <td><?= clean($res['prenom'] . ' ' . $res['nom']) ?></td>
                            <td><?= clean($res['marque'] . ' ' . $res['modele']) ?></td>
                            <td><?= formatDate($res['date_debut']) ?></td>
                            <td><?= getStatutBadge($res['statut']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="table-container">
                <div class="table-header">
                    <h3>Nouveaux clients</h3>
                    <a href="clients.php" class="btn btn-outline btn-sm">Gérer</a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Email</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentClients as $client): ?>
                        <tr>
                            <td><?= clean($client['prenom'] . ' ' . $client['nom']) ?></td>
                            <td><?= clean($client['email']) ?></td>
                            <td><?= formatDate($client['date_creation']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="chart-container mt-4">
            <h3>Évolution des réservations</h3>
            <div style="height: 200px; display: flex; align-items: flex-end; gap: 20px; padding: 20px;">
                <div style="flex: 1; background: #eee; height: 40%; border-radius: 4px;"></div>
                <div style="flex: 1; background: #ddd; height: 60%; border-radius: 4px;"></div>
                <div style="flex: 1; background: #ccc; height: 30%; border-radius: 4px;"></div>
                <div style="flex: 1; background: #bbb; height: 80%; border-radius: 4px;"></div>
                <div style="flex: 1; background: #aaa; height: 50%; border-radius: 4px;"></div>
                <div style="flex: 1; background: var(--primary); height: 90%; border-radius: 4px;"></div>
                <div style="flex: 1; background: #999; height: 70%; border-radius: 4px;"></div>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 0 20px; font-size: 0.8rem; color: var(--secondary);">
                <span>Lun</span><span>Mar</span><span>Mer</span><span>Jeu</span><span>Ven</span><span>Sam</span><span>Dim</span>
            </div>
        </div>
    </main>
</body>
</html>
