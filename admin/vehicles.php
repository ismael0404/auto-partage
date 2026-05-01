<?php
// admin/vehicles.php
require_once __DIR__ . '/../includes/header.php';
requireRole('admin');

// Soft delete
if (isset($_POST['delete_id'])) {
    $delete_id = filter_input(INPUT_POST, 'delete_id', FILTER_VALIDATE_INT);
    if ($delete_id) {
        $stmt = $pdo->prepare("UPDATE vehicles SET deleted_at = NOW() WHERE id = ?");
        $stmt->execute([$delete_id]);
        setFlashMessage("VÃ©hicule supprimÃ© avec succÃ¨s.", "success");
        header("Location: /Projet_Auto/admin/vehicles.php");
        exit();
    }
}

$stmt = $pdo->query("SELECT * FROM vehicles WHERE deleted_at IS NULL ORDER BY created_at DESC");
$vehicles = $stmt->fetchAll();
?>

<div class="d-flex" style="max-width: 1200px; margin: 0 auto; gap: 2rem;">
    <?php include __DIR__ . '/../includes/sidebar_admin.php'; ?>
    
    <div style="flex: 1; padding: 2rem 0;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestion des vÃ©hicules</h2>
            <a href="/Projet_Auto/admin/vehicle_add.php" class="btn btn-primary"><i class="fa fa-plus"></i> Ajouter un vÃ©hicule</a>
        </div>
        
        <div class="card">
            <div class="card-body">
                <?php if(empty($vehicles)): ?>
                    <p class="text-center" style="color: var(--gray-500);">Aucun vÃ©hicule enregistrÃ©.</p>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left;">
                            <thead>
                                <tr style="border-bottom: 2px solid var(--gray-200);">
                                    <th style="padding: 1rem;">VÃ©hicule</th>
                                    <th style="padding: 1rem;">Tarifs</th>
                                    <th style="padding: 1rem;">Statut</th>
                                    <th style="padding: 1rem; text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($vehicles as $v): ?>
                                <tr style="border-bottom: 1px solid var(--gray-200);">
                                    <td style="padding: 1rem; display: flex; align-items: center; gap: 1rem;">
                                        <?php if($v['image']): ?>
                                            <img src="/Projet_Auto/assets/images/<?php echo htmlspecialchars($v['image']); ?>" alt="Auto" style="width: 50px; height: 35px; object-fit: cover; border-radius: 4px;">
                                        <?php else: ?>
                                            <div style="width: 50px; height: 35px; background: var(--gray-200); border-radius: 4px; display: flex; align-items: center; justify-content: center;"><i class="fa fa-car" style="color: var(--gray-400)"></i></div>
                                        <?php endif; ?>
                                        <div>
                                            <span style="font-weight: 500; display: block;"><?php echo htmlspecialchars($v['marque'] . ' ' . $v['modele']); ?></span>
                                            <span style="font-size: 0.8rem; color: var(--gray-500);"><?php echo $v['annee']; ?> &bull; <?php echo $v['carburant']; ?></span>
                                        </div>
                                    </td>
                                    <td style="padding: 1rem; font-size: 0.9rem;">
                                        <div style="font-weight: 600;"><?php echo formatPrice($v['prix_jour']); ?> / j</div>
                                        <div style="color: var(--gray-500);"><?php echo formatPrice($v['prix_heure']); ?> / h</div>
                                    </td>
                                    <td style="padding: 1rem;">
                                        <span style="padding: 0.25rem 0.5rem; border-radius: 999px; font-size: 0.8rem; font-weight: 600;
                                            <?php 
                                            echo match($v['statut']) {
                                                'disponible' => 'background-color: #d1fae5; color: #065f46;',
                                                'reserve' => 'background-color: #dbeafe; color: #1e40af;',
                                                'maintenance' => 'background-color: #fee2e2; color: #991b1b;',
                                                default => 'background-color: var(--gray-200); color: var(--gray-700);'
                                            };
                                            ?>
                                        ">
                                            <?php echo ucfirst($v['statut']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 1rem; text-align: right;">
                                        <div class="d-flex" style="justify-content: flex-end; gap: 0.5rem;">
                                            <a href="/Projet_Auto/admin/vehicle_edit.php?id=<?php echo $v['id']; ?>" class="btn btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;"><i class="fa fa-edit"></i></a>
                                            <form method="POST" onsubmit="return confirm('Supprimer ce vÃ©hicule ?');" style="display: inline;">
                                                <input type="hidden" name="delete_id" value="<?php echo $v['id']; ?>">
                                                <button type="submit" class="btn" style="background-color: #fee2e2; color: #991b1b; padding: 0.25rem 0.5rem; font-size: 0.875rem;"><i class="fa fa-trash"></i></button>
                                            </form>
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

