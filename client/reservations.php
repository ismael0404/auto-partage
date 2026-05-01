<?php
// client/reservations.php
require_once __DIR__ . '/../includes/header.php';
requireRole('client');

// Traitement de l'annulation si demandÃ©e
if (isset($_POST['cancel_id'])) {
    $cancel_id = filter_input(INPUT_POST, 'cancel_id', FILTER_VALIDATE_INT);
    if ($cancel_id) {
        $stmt = $pdo->prepare("SELECT id, statut FROM reservations WHERE id = ? AND user_id = ?");
        $stmt->execute([$cancel_id, $_SESSION['user_id']]);
        $resa = $stmt->fetch();
        
        if ($resa && $resa['statut'] === 'en_attente') {
            $update = $pdo->prepare("UPDATE reservations SET statut = 'annulee' WHERE id = ?");
            $update->execute([$cancel_id]);
            setFlashMessage("RÃ©servation annulÃ©e avec succÃ¨s.", "success");
        } else {
            setFlashMessage("Impossible d'annuler cette rÃ©servation.", "error");
        }
        header("Location: /Projet_Auto/client/reservations.php");
        exit();
    }
}

// RÃ©cupÃ©ration de l'historique
$stmt = $pdo->prepare("
    SELECT r.*, v.marque, v.modele, v.image 
    FROM reservations r 
    JOIN vehicles v ON r.vehicle_id = v.id 
    WHERE r.user_id = ? AND r.deleted_at IS NULL
    ORDER BY r.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$reservations = $stmt->fetchAll();
?>

<div class="d-flex" style="max-width: 1200px; margin: 0 auto; gap: 2rem;">
    <?php include __DIR__ . '/../includes/sidebar_client.php'; ?>
    
    <div style="flex: 1; padding: 2rem 0;">
        <h2 class="mb-4">Mes rÃ©servations</h2>
        
        <div class="card">
            <div class="card-body">
                <?php if(empty($reservations)): ?>
                    <p class="text-center" style="color: var(--gray-500);">Vous n'avez aucune rÃ©servation.</p>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left;">
                            <thead>
                                <tr style="border-bottom: 2px solid var(--gray-200);">
                                    <th style="padding: 1rem;">VÃ©hicule</th>
                                    <th style="padding: 1rem;">PÃ©riode</th>
                                    <th style="padding: 1rem;">Total</th>
                                    <th style="padding: 1rem;">Statut</th>
                                    <th style="padding: 1rem; text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($reservations as $r): ?>
                                <tr style="border-bottom: 1px solid var(--gray-200);">
                                    <td style="padding: 1rem; display: flex; align-items: center; gap: 1rem;">
                                        <?php if($r['image']): ?>
                                            <img src="/Projet_Auto/assets/images/<?php echo htmlspecialchars($r['image']); ?>" alt="Auto" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                                        <?php else: ?>
                                            <div style="width: 60px; height: 40px; background: var(--gray-200); border-radius: 4px; display: flex; align-items: center; justify-content: center;"><i class="fa fa-car" style="color: var(--gray-400)"></i></div>
                                        <?php endif; ?>
                                        <span style="font-weight: 500;"><?php echo htmlspecialchars($r['marque'] . ' ' . $r['modele']); ?></span>
                                    </td>
                                    <td style="padding: 1rem; font-size: 0.9rem;">
                                        <?php echo date('d/m/Y H:i', strtotime($r['date_debut'])); ?> - <br>
                                        <?php echo date('d/m/Y H:i', strtotime($r['date_fin'])); ?>
                                    </td>
                                    <td style="padding: 1rem; font-weight: 600;">
                                        <?php echo formatPrice($r['prix_total']); ?>
                                    </td>
                                    <td style="padding: 1rem;">
                                        <span style="padding: 0.25rem 0.5rem; border-radius: 999px; font-size: 0.8rem; font-weight: 600;
                                            <?php 
                                            echo match($r['statut']) {
                                                'en_attente' => 'background-color: #fef3c7; color: #92400e;',
                                                'confirmee' => 'background-color: #d1fae5; color: #065f46;',
                                                'en_cours' => 'background-color: #dbeafe; color: #1e40af;',
                                                'terminee' => 'background-color: #f3f4f6; color: #374151;',
                                                'annulee' => 'background-color: #fee2e2; color: #991b1b;',
                                                default => 'background-color: var(--gray-200); color: var(--gray-700);'
                                            };
                                            ?>
                                        ">
                                            <?php echo ucfirst(str_replace('_', ' ', $r['statut'])); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 1rem; text-align: right;">
                                        <div class="d-flex" style="justify-content: flex-end; gap: 0.5rem;">
                                            <a href="/Projet_Auto/client/vehicle_detail.php?id=<?php echo $r['vehicle_id']; ?>" class="btn btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">Voir</a>
                                            
                                            <?php if($r['statut'] === 'en_attente'): ?>
                                            <form method="POST" onsubmit="return confirm('ÃŠtes-vous sÃ»r de vouloir annuler cette rÃ©servation ?');" style="display: inline;">
                                                <input type="hidden" name="cancel_id" value="<?php echo $r['id']; ?>">
                                                <button type="submit" class="btn" style="background-color: #fee2e2; color: #991b1b; padding: 0.25rem 0.5rem; font-size: 0.875rem;">Annuler</button>
                                            </form>
                                            <?php endif; ?>
                                        </div>
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

