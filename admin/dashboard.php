<?php
// admin/dashboard.php
require_once __DIR__ . '/../includes/header.php';
requireRole('admin');

// Statistiques
$stats = [
    'clients' => $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'client' AND deleted_at IS NULL")->fetchColumn(),
    'vehicles' => $pdo->query("SELECT COUNT(*) FROM vehicles WHERE deleted_at IS NULL")->fetchColumn(),
    'reservations' => $pdo->query("SELECT COUNT(*) FROM reservations WHERE deleted_at IS NULL")->fetchColumn(),
    'revenus' => $pdo->query("SELECT SUM(prix_total) FROM reservations WHERE statut IN ('confirmee', 'en_cours', 'terminee') AND deleted_at IS NULL")->fetchColumn() ?? 0
];

// RÃ©servations rÃ©centes
$stmt = $pdo->query("
    SELECT r.*, u.prenom, u.nom, v.marque, v.modele 
    FROM reservations r 
    JOIN users u ON r.user_id = u.id 
    JOIN vehicles v ON r.vehicle_id = v.id 
    WHERE r.deleted_at IS NULL
    ORDER BY r.created_at DESC LIMIT 5
");
$recentes = $stmt->fetchAll();
?>

<div class="d-flex" style="max-width: 1200px; margin: 0 auto; gap: 2rem;">
    <?php include __DIR__ . '/../includes/sidebar_admin.php'; ?>
    
    <div style="flex: 1; padding: 2rem 0;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Tableau de bord</h2>
            <div class="d-flex align-items-center" style="gap: 1rem;">
                <span style="font-weight: 600;"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <div style="width: 40px; height: 40px; background-color: var(--gray-200); border-radius: 50%; display: flex; align-items: center; justify-content: center;"><i class="fa fa-user"></i></div>
            </div>
        </div>
        
        <!-- KPIs -->
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
            <div class="card">
                <div class="card-body">
                    <p style="color: var(--gray-500); font-size: 0.875rem; margin-bottom: 0.5rem;">Clients</p>
                    <h3 style="font-size: 2rem;"><?php echo $stats['clients']; ?></h3>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <p style="color: var(--gray-500); font-size: 0.875rem; margin-bottom: 0.5rem;">VÃ©hicules</p>
                    <h3 style="font-size: 2rem;"><?php echo $stats['vehicles']; ?></h3>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <p style="color: var(--gray-500); font-size: 0.875rem; margin-bottom: 0.5rem;">RÃ©servations</p>
                    <h3 style="font-size: 2rem;"><?php echo $stats['reservations']; ?></h3>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <p style="color: var(--gray-500); font-size: 0.875rem; margin-bottom: 0.5rem;">Revenus</p>
                    <h3 style="font-size: 1.5rem;"><?php echo formatPrice($stats['revenus']); ?></h3>
                </div>
            </div>
        </div>
        
        <!-- DerniÃ¨res rÃ©servations -->
        <div class="card">
            <div class="card-body">
                <h3 class="mb-4">RÃ©servations rÃ©centes</h3>
                <?php if(empty($recentes)): ?>
                    <p class="text-center" style="color: var(--gray-500);">Aucune rÃ©servation pour le moment.</p>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left;">
                            <tbody>
                                <?php foreach($recentes as $r): ?>
                                <tr style="border-bottom: 1px solid var(--gray-200);">
                                    <td style="padding: 1rem;"><?php echo htmlspecialchars($r['prenom'] . ' ' . $r['nom']); ?></td>
                                    <td style="padding: 1rem; color: var(--gray-500);"><?php echo htmlspecialchars($r['marque'] . ' ' . $r['modele']); ?></td>
                                    <td style="padding: 1rem; text-align: right; color: var(--gray-500);"><?php echo date('d/m/Y', strtotime($r['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

