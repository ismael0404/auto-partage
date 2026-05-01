<?php
// admin/clients.php
require_once __DIR__ . '/../includes/header.php';
requireRole('admin');

// Soft delete
if (isset($_POST['delete_id'])) {
    $delete_id = filter_input(INPUT_POST, 'delete_id', FILTER_VALIDATE_INT);
    if ($delete_id) {
        $stmt = $pdo->prepare("UPDATE users SET deleted_at = NOW() WHERE id = ? AND role = 'client'");
        $stmt->execute([$delete_id]);
        setFlashMessage("Client supprimÃ© avec succÃ¨s.", "success");
        header("Location: /Projet_Auto/admin/clients.php");
        exit();
    }
}

$stmt = $pdo->query("SELECT * FROM users WHERE role = 'client' AND deleted_at IS NULL ORDER BY created_at DESC");
$clients = $stmt->fetchAll();
?>

<div class="d-flex" style="max-width: 1200px; margin: 0 auto; gap: 2rem;">
    <?php include __DIR__ . '/../includes/sidebar_admin.php'; ?>
    
    <div style="flex: 1; padding: 2rem 0;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestion des clients</h2>
        </div>
        
        <div class="card">
            <div class="card-body">
                <?php if(empty($clients)): ?>
                    <p class="text-center" style="color: var(--gray-500);">Aucun client enregistrÃ©.</p>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left;">
                            <thead>
                                <tr style="border-bottom: 2px solid var(--gray-200);">
                                    <th style="padding: 1rem;">Client</th>
                                    <th style="padding: 1rem;">Contact</th>
                                    <th style="padding: 1rem;">Date d'inscription</th>
                                    <th style="padding: 1rem; text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($clients as $c): ?>
                                <tr style="border-bottom: 1px solid var(--gray-200);">
                                    <td style="padding: 1rem; display: flex; align-items: center; gap: 1rem;">
                                        <div style="width: 40px; height: 40px; background-color: var(--gray-200); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--gray-600);"><i class="fa fa-user"></i></div>
                                        <div>
                                            <span style="font-weight: 500; display: block;"><?php echo htmlspecialchars($c['prenom'] . ' ' . $c['nom']); ?></span>
                                        </div>
                                    </td>
                                    <td style="padding: 1rem; font-size: 0.9rem;">
                                        <div style="color: var(--gray-700);"><i class="fa fa-envelope" style="width: 15px; color: var(--gray-500);"></i> <?php echo htmlspecialchars($c['email']); ?></div>
                                        <div style="color: var(--gray-700); margin-top: 0.25rem;"><i class="fa fa-phone" style="width: 15px; color: var(--gray-500);"></i> <?php echo htmlspecialchars($c['telephone']); ?></div>
                                    </td>
                                    <td style="padding: 1rem; color: var(--gray-500);">
                                        <?php echo date('d/m/Y', strtotime($c['created_at'])); ?>
                                    </td>
                                    <td style="padding: 1rem; text-align: right;">
                                        <div class="d-flex" style="justify-content: flex-end; gap: 0.5rem;">
                                            <form method="POST" onsubmit="return confirm('Supprimer ce compte client ?');" style="display: inline;">
                                                <input type="hidden" name="delete_id" value="<?php echo $c['id']; ?>">
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

