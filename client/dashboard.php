<?php
// client/dashboard.php
require_once __DIR__ . '/../includes/header.php';
requireRole('client');

// RÃ©cupÃ©rer les rÃ©servations rÃ©centes
$stmt = $pdo->prepare("
    SELECT r.*, v.marque, v.modele, v.image 
    FROM reservations r 
    JOIN vehicles v ON r.vehicle_id = v.id 
    WHERE r.user_id = ? AND r.deleted_at IS NULL
    ORDER BY r.created_at DESC LIMIT 5
");
$stmt->execute([$_SESSION['user_id']]);
$reservations = $stmt->fetchAll();
?>

<div class="d-flex" style="max-width: 1200px; margin: 0 auto; gap: 2rem;">
    <?php include __DIR__ . '/../includes/sidebar_client.php'; ?>
    
    <div style="flex: 1; padding: 2rem 0;">
        <h2 class="mb-4">Bonjour, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>
        
        <div class="card mb-5">
            <div class="card-body">
                <h3>Vos RÃ©servations RÃ©centes</h3>
                <?php if (empty($reservations)): ?>
                    <p class="mt-3 text-center" style="color: var(--gray-500);">Vous n'avez aucune rÃ©servation pour le moment.</p>
                    <div class="text-center mt-3">
                        <a href="/Projet_Auto/client/vehicles.php" class="btn btn-primary">Voir les vÃ©hicules disponibles</a>
                    </div>
                <?php else: ?>
                    <div style="overflow-x: auto; margin-top: 1rem;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left;">
                            <thead>
                                <tr style="border-bottom: 2px solid var(--gray-200);">
                                    <th style="padding: 1rem;">VÃ©hicule</th>
                                    <th style="padding: 1rem;">PÃ©riode</th>
                                    <th style="padding: 1rem;">Total</th>
                                    <th style="padding: 1rem;">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservations as $resa): ?>
                                <tr style="border-bottom: 1px solid var(--gray-200);">
                                    <td style="padding: 1rem; display: flex; align-items: center; gap: 1rem;">
                                        <?php if($resa['image']): ?>
                                            <img src="/Projet_Auto/assets/images/<?php echo htmlspecialchars($resa['image']); ?>" alt="Auto" style="width: 50px; height: 35px; object-fit: cover; border-radius: 4px;">
                                        <?php else: ?>
                                            <div style="width: 50px; height: 35px; background: var(--gray-200); border-radius: 4px; display: flex; align-items: center; justify-content: center;"><i class="fa fa-car" style="color: var(--gray-400)"></i></div>
                                        <?php endif; ?>
                                        <?php echo htmlspecialchars($resa['marque'] . ' ' . $resa['modele']); ?>
                                    </td>
                                    <td style="padding: 1rem; font-size: 0.9rem;">
                                        Du <?php echo date('d/m/Y H:i', strtotime($resa['date_debut'])); ?><br>
                                        Au <?php echo date('d/m/Y H:i', strtotime($resa['date_fin'])); ?>
                                    </td>
                                    <td style="padding: 1rem; font-weight: 600;">
                                        <?php echo formatPrice($resa['prix_total']); ?>
                                    </td>
                                    <td style="padding: 1rem;">
                                        <span style="padding: 0.25rem 0.5rem; border-radius: 999px; font-size: 0.8rem; font-weight: 600;
                                            <?php 
                                            echo match($resa['statut']) {
                                                'en_attente' => 'background-color: #fef3c7; color: #92400e;',
                                                'confirmee' => 'background-color: #d1fae5; color: #065f46;',
                                                'en_cours' => 'background-color: #dbeafe; color: #1e40af;',
                                                'terminee' => 'background-color: #f3f4f6; color: #374151;',
                                                'annulee' => 'background-color: #fee2e2; color: #991b1b;',
                                                default => 'background-color: var(--gray-200); color: var(--gray-700);'
                                            };
                                            ?>
                                        ">
                                            <?php echo ucfirst(str_replace('_', ' ', $resa['statut'])); ?>
                                        </span>
                                    </td>
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

