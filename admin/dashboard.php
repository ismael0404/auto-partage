<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

$adminId = $_SESSION['user_id'];

// Statistiques Globales
$stmt = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'client' AND is_deleted = 0");
$totalClients = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM vehicules WHERE is_deleted = 0");
$totalVehicules = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM reservations");
$totalReservations = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT SUM(prix_total) FROM reservations WHERE statut = 'confirmee' OR statut = 'terminee'");
$totalRevenu = $stmt->fetchColumn() ?: 0;

$stmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE utilisateur_id = :uid AND lu = 0");
$stmt->execute([':uid' => $adminId]);
$unreadNotifs = $stmt->fetchColumn();

// Réservations récentes
$stmt = $pdo->query("SELECT r.*, v.marque, v.modele, u.prenom, u.nom 
                     FROM reservations r 
                     JOIN vehicules v ON r.vehicule_id = v.id 
                     JOIN utilisateurs u ON r.utilisateur_id = u.id 
                     ORDER BY r.date_creation DESC LIMIT 5");
$recentReservations = $stmt->fetchAll();

// Statistiques pour le graphique (15 derniers jours)
$chartLabels = [];
$chartData = [];

// Générer les 15 derniers jours
for ($i = 14; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $displayDate = date('d/m', strtotime($date));
    $chartLabels[$date] = $displayDate;
    $chartData[$date] = 0;
}

// Récupérer les revenus réels par jour
$stmt = $pdo->query("SELECT DATE(date_creation) as jour, SUM(prix_total) as total 
                     FROM reservations 
                     WHERE (statut = 'confirmee' OR statut = 'terminee') 
                     AND date_creation >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
                     GROUP BY DATE(date_creation)");
$realStats = $stmt->fetchAll();

foreach ($realStats as $s) {
    if (isset($chartData[$s['jour']])) {
        $chartData[$s['jour']] = (float)$s['total'];
    }
}

$finalLabels = array_values($chartLabels);
$finalData = array_values($chartData);

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
                <?php if ($unreadNotifs > 0): ?>
                    <a href="notifications.php" class="badge badge-warning" style="text-decoration: none;">
                        <i class="fas fa-bell"></i> <?= $unreadNotifs ?> nouvelle(s) notification(s)
                    </a>
                <?php endif; ?>
                <span class="badge badge-success">Admin en ligne</span>
            </div>
        </header>

        <?php $flash = getFlash(); if ($flash): ?>
            <div class="flash flash-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
        <?php endif; ?>

        <div class="stat-cards">
            <div class="stat-card">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="label">Clients</div>
                        <div class="value"><?= $totalClients ?></div>
                    </div>
                    <div class="icon-box" style="background: rgba(17,17,17,0.05); padding: 10px; border-radius: 8px;">
                        <i class="fas fa-users" style="color: var(--primary);"></i>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="label">Réservations</div>
                        <div class="value"><?= $totalReservations ?></div>
                    </div>
                    <div class="icon-box" style="background: rgba(17,17,17,0.05); padding: 10px; border-radius: 8px;">
                        <i class="fas fa-calendar-alt" style="color: var(--primary);"></i>
                    </div>
                </div>
            </div>
            <div class="stat-card <?= $unreadNotifs > 0 ? 'dark' : '' ?>" style="<?= $unreadNotifs > 0 ? 'background: var(--warning); color: #fff;' : '' ?>">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="label" style="<?= $unreadNotifs > 0 ? 'color: #fff;' : '' ?>">Alertes</div>
                        <div class="value" style="<?= $unreadNotifs > 0 ? 'color: #fff;' : '' ?>"><?= $unreadNotifs ?></div>
                    </div>
                    <div class="icon-box" style="background: rgba(0,0,0,0.1); padding: 10px; border-radius: 8px;">
                        <i class="fas fa-exclamation-triangle" style="<?= $unreadNotifs > 0 ? 'color: #fff;' : 'color: var(--warning);' ?>"></i>
                    </div>
                </div>
            </div>
            <div class="stat-card dark">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="label" style="color: rgba(255,255,255,0.7);">Revenus</div>
                        <div class="value" style="color: #fff;"><?= formatPrix($totalRevenu) ?></div>
                    </div>
                    <div class="icon-box" style="background: rgba(255,255,255,0.1); padding: 10px; border-radius: 8px;">
                        <i class="fas fa-wallet" style="color: #fff;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-title mt-4">
            <h3>Actions Rapides</h3>
        </div>
        <div class="flex gap-2 mb-4">
            <a href="vehicles.php" class="btn btn-outline" style="flex: 1; text-align: center;">
                <i class="fas fa-plus-circle"></i> Ajouter Véhicule
            </a>
            <a href="notifications.php" class="btn btn-outline" style="flex: 1; text-align: center;">
                <i class="fas fa-paper-plane"></i> Notifications
            </a>
            <a href="messages.php" class="btn btn-outline" style="flex: 1; text-align: center;">
                <i class="fas fa-headset"></i> Support Client
            </a>
            <a href="settings.php" class="btn btn-outline" style="flex: 1; text-align: center;">
                <i class="fas fa-tools"></i> Configuration
            </a>
        </div>

        <div class="grid-2">
            <div class="table-container" style="background: #fff; padding: 20px; border-radius: var(--radius); border: 1px solid var(--border);">
                <div class="table-header">
                    <h3>Analytics (Revenus des 15 derniers jours)</h3>
                </div>
                <div style="height: 300px; position: relative;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

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
                            <th>Mode</th>
                            <th>Paiement</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentReservations as $res): ?>
                        <tr>
                            <td><?= clean($res['prenom'] . ' ' . $res['nom']) ?></td>
                            <td><?= clean($res['marque'] . ' ' . $res['modele']) ?></td>
                            <td>
                                <?php if ($res['mode_paiement'] === 'sur_place'): ?>
                                    <span class="badge" style="background: #e5e7eb; color: #374151;">Sur place</span>
                                <?php elseif ($res['mode_paiement'] === 'ligne'): ?>
                                    <span class="badge badge-info">En ligne</span>
                                <?php else: ?>
                                    <span class="text-secondary">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($res['statut_paiement'] === 'paye'): ?>
                                    <span class="badge badge-success">Payé</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Attente</span>
                                <?php endif; ?>
                            </td>
                            <td><?= getStatutBadge($res['statut']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('revenueChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($finalLabels) ?>,
                    datasets: [{
                        label: 'Revenus Quotidiens (FCFA)',
                        data: <?= json_encode($finalData) ?>,
                        borderColor: '#111',
                        backgroundColor: 'rgba(17,17,17,0.05)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y.toLocaleString('fr-FR') + ' FCFA';
                                }
                            }
                        }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('fr-FR') + ' FCFA';
                                }
                            }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        </script>
    </main>
</body>
</html>
    </main>
</body>
</html>
