<?php
// client/vehicle_detail.php
require_once __DIR__ . '/../includes/header.php';
requireRole('client');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    setFlashMessage("VÃ©hicule introuvable.", "error");
    header("Location: /Projet_Auto/client/vehicles.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ? AND deleted_at IS NULL AND statut != 'maintenance'");
$stmt->execute([$id]);
$vehicle = $stmt->fetch();

if (!$vehicle) {
    setFlashMessage("VÃ©hicule introuvable ou indisponible.", "error");
    header("Location: /Projet_Auto/client/vehicles.php");
    exit();
}
?>

<div class="d-flex" style="max-width: 1200px; margin: 0 auto; gap: 2rem;">
    <?php include __DIR__ . '/../includes/sidebar_client.php'; ?>
    
    <div style="flex: 1; padding: 2rem 0;">
        <a href="/Projet_Auto/client/vehicles.php" style="display: inline-block; margin-bottom: 1rem; color: var(--gray-500);"><i class="fa fa-arrow-left"></i> Retour</a>
        
        <div class="d-flex" style="gap: 2rem;">
            <div style="flex: 2;">
                <div class="card" style="margin-bottom: 2rem;">
                    <div style="height: 400px; background-color: var(--gray-100); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <?php if($vehicle['image']): ?>
                            <img src="/Projet_Auto/assets/images/<?php echo htmlspecialchars($vehicle['image']); ?>" alt="Voiture" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <i class="fa fa-car" style="font-size: 8rem; color: var(--gray-300);"></i>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <h3 class="mb-3">Description</h3>
                        <p style="color: var(--gray-700); line-height: 1.6; white-space: pre-line;">
                            <?php echo htmlspecialchars($vehicle['description'] ?? 'Aucune description disponible pour ce vÃ©hicule.'); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div style="flex: 1;">
                <div class="card" style="position: sticky; top: 100px;">
                    <div class="card-body">
                        <h2 style="font-size: 1.5rem; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']); ?></h2>
                        <p style="color: var(--success); font-weight: 600; margin-bottom: 1.5rem;"><i class="fa fa-check-circle"></i> Disponible</p>
                        
                        <ul style="list-style: none; display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2rem;">
                            <li class="d-flex align-items-center" style="gap: 1rem; color: var(--gray-700);">
                                <i class="fa-solid fa-bolt" style="width: 20px; text-align: center; color: var(--gray-500);"></i> 
                                <?php echo htmlspecialchars($vehicle['carburant']); ?>
                            </li>
                            <li class="d-flex align-items-center" style="gap: 1rem; color: var(--gray-700);">
                                <i class="fa-regular fa-calendar-alt" style="width: 20px; text-align: center; color: var(--gray-500);"></i> 
                                <?php echo htmlspecialchars($vehicle['annee']); ?>
                            </li>
                            <li class="d-flex align-items-center" style="gap: 1rem; color: var(--gray-700);">
                                <i class="fa-solid fa-cogs" style="width: 20px; text-align: center; color: var(--gray-500);"></i> 
                                <?php echo htmlspecialchars($vehicle['transmission']); ?>
                            </li>
                            <li class="d-flex align-items-center" style="gap: 1rem; color: var(--gray-700);">
                                <i class="fa-solid fa-users" style="width: 20px; text-align: center; color: var(--gray-500);"></i> 
                                <?php echo htmlspecialchars($vehicle['places']); ?> places
                            </li>
                            <li class="d-flex align-items-center" style="gap: 1rem; color: var(--gray-700);">
                                <i class="fa-solid fa-snowflake" style="width: 20px; text-align: center; color: var(--gray-500);"></i> 
                                <?php echo $vehicle['climatisation'] ? 'Climatisation' : 'Sans climatisation'; ?>
                            </li>
                        </ul>
                        
                        <div style="border-top: 1px solid var(--gray-200); padding-top: 1.5rem; margin-bottom: 1.5rem;">
                            <div style="font-size: 1.5rem; font-weight: 700;">
                                <?php echo formatPrice($vehicle['prix_jour']); ?> <span style="font-size: 1rem; font-weight: 400; color: var(--gray-500);">/ jour</span>
                            </div>
                        </div>
                        
                        <a href="/Projet_Auto/client/reserve.php?id=<?php echo $vehicle['id']; ?>" class="btn btn-primary btn-block text-center" style="padding: 1rem;">RÃ©server maintenant</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

