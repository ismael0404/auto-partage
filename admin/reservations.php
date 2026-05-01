<?php
// admin/reservations.php
require_once __DIR__ . '/../includes/header.php';
requireRole('admin');

// Actions sur la rÃ©servation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['reservation_id'])) {
    $action = $_POST['action'];
    $resa_id = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT);
    
    if ($resa_id) {
        $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ?");
        $stmt->execute([$resa_id]);
        $resa = $stmt->fetch();
        
        if ($resa) {
            try {
                $pdo->beginTransaction();
                
                if ($action === 'confirm' && $resa['statut'] === 'en_attente') {
                    // Confirmer
                    $update = $pdo->prepare("UPDATE reservations SET statut = 'confirmee' WHERE id = ?");
                    $update->execute([$resa_id]);
                    // Mettre le vÃ©hicule en statut 'reserve' (comme demandÃ©)
                    $updateVeh = $pdo->prepare("UPDATE vehicles SET statut = 'reserve' WHERE id = ?");
                    $updateVeh->execute([$resa['vehicle_id']]);
                    
                    setFlashMessage("RÃ©servation confirmÃ©e avec succÃ¨s.", "success");
                } 
                elseif ($action === 'cancel' && in_array($resa['statut'], ['en_attente', 'confirmee', 'en_cours'])) {
                    // Annuler
                    $update = $pdo->prepare("UPDATE reservations SET statut = 'annulee' WHERE id = ?");
                    $update->execute([$resa_id]);
                    // Remettre le vÃ©hicule disponible
                    $updateVeh = $pdo->prepare("UPDATE vehicles SET statut = 'disponible' WHERE id = ?");
                    $updateVeh->execute([$resa['vehicle_id']]);
                    
                    setFlashMessage("RÃ©servation annulÃ©e.", "info");
                }
                elseif ($action === 'finish' && in_array($resa['statut'], ['confirmee', 'en_cours'])) {
                    // Terminer
                    $update = $pdo->prepare("UPDATE reservations SET statut = 'terminee' WHERE id = ?");
                    $update->execute([$resa_id]);
                    // Remettre le vÃ©hicule disponible
                    $updateVeh = $pdo->prepare("UPDATE vehicles SET statut = 'disponible' WHERE id = ?");
                    $updateVeh->execute([$resa['vehicle_id']]);
                    
                    setFlashMessage("RÃ©servation marquÃ©e comme terminÃ©e.", "success");
                }
                
                $pdo->commit();
            } catch (Exception $e) {
                $pdo->rollBack();
                setFlashMessage("Erreur lors de la mise Ã  jour.", "error");
            }
        }
    }
    header("Location: /Projet_Auto/admin/reservations.php");
    exit();
}

$stmt = $pdo->query("
    SELECT r.*, u.prenom, u.nom, u.email, v.marque, v.modele 
    FROM reservations r 
    JOIN users u ON r.user_id = u.id 
    JOIN vehicles v ON r.vehicle_id = v.id 
    WHERE r.deleted_at IS NULL
    ORDER BY r.created_at DESC
");
$reservations = $stmt->fetchAll();
?>

<div class="d-flex" style="max-width: 1200px; margin: 0 auto; gap: 2rem;">
    <?php include __DIR__ . '/../includes/sidebar_admin.php'; ?>
    
    <div style="flex: 1; padding: 2rem 0;">
        <h2 class="mb-4">Gestion des rÃ©servations</h2>
        
        <div class="card">
            <div class="card-body">
                <?php if(empty($reservations)): ?>
                    <p class="text-center" style="color: var(--gray-500);">Aucune rÃ©servation.</p>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                            <thead>
                                <tr style="border-bottom: 2px solid var(--gray-200);">
                                    <th style="padding: 1rem;">Client</th>
                                    <th style="padding: 1rem;">VÃ©hicule</th>
                                    <th style="padding: 1rem;">PÃ©riode</th>
                                    <th style="padding: 1rem;">Statut</th>
                                    <th style="padding: 1rem; text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($reservations as $r): ?>
                                <tr style="border-bottom: 1px solid var(--gray-200);">
                                    <td style="padding: 1rem;">
                                        <div style="font-weight: 500;"><?php echo htmlspecialchars($r['prenom'] . ' ' . $r['nom']); ?></div>
                                        <div style="color: var(--gray-500); font-size: 0.8rem;"><?php echo htmlspecialchars($r['email']); ?></div>
                                    </td>
                                    <td style="padding: 1rem; font-weight: 500;">
                                        <?php echo htmlspecialchars($r['marque'] . ' ' . $r['modele']); ?>
                                    </td>
                                    <td style="padding: 1rem;">
                                        <?php echo date('d/m/Y H:i', strtotime($r['date_debut'])); ?> au<br>
                                        <?php echo date('d/m/Y H:i', strtotime($r['date_fin'])); ?>
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
                                            <?php if($r['statut'] === 'en_attente'): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="reservation_id" value="<?php echo $r['id']; ?>">
                                                    <input type="hidden" name="action" value="confirm">
                                                    <button type="submit" class="btn btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; border-color: var(--success); color: var(--success);"><i class="fa fa-check"></i></button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <?php if(in_array($r['statut'], ['en_attente', 'confirmee', 'en_cours'])): ?>
                                                <form method="POST" onsubmit="return confirm('Annuler cette rÃ©servation ?');" style="display: inline;">
                                                    <input type="hidden" name="reservation_id" value="<?php echo $r['id']; ?>">
                                                    <input type="hidden" name="action" value="cancel">
                                                    <button type="submit" class="btn btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; border-color: var(--error); color: var(--error);"><i class="fa fa-times"></i></button>
                                                </form>
                                            <?php endif; ?>

                                            <?php if(in_array($r['statut'], ['confirmee', 'en_cours'])): ?>
                                                <form method="POST" onsubmit="return confirm('Marquer comme terminÃ©e ?');" style="display: inline;">
                                                    <input type="hidden" name="reservation_id" value="<?php echo $r['id']; ?>">
                                                    <input type="hidden" name="action" value="finish">
                                                    <button type="submit" class="btn btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; background-color: var(--gray-200);"><i class="fa fa-flag-checkered"></i></button>
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

