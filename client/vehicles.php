<?php
// client/vehicles.php
require_once __DIR__ . '/../includes/header.php';
requireRole('client');

// Filtres
$carburant = $_GET['carburant'] ?? '';
$prix_max = $_GET['prix_max'] ?? '';

// RequÃªte de base (seulement les vÃ©hicules non supprimÃ©s et non en maintenance)
$query = "SELECT * FROM vehicles WHERE deleted_at IS NULL AND statut != 'maintenance'";
$params = [];

if (!empty($carburant)) {
    $query .= " AND carburant = ?";
    $params[] = $carburant;
}

if (!empty($prix_max)) {
    $query .= " AND prix_jour <= ?";
    $params[] = $prix_max;
}

$query .= " ORDER BY prix_jour ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$vehicles = $stmt->fetchAll();
?>

<div class="d-flex" style="max-width: 1200px; margin: 0 auto; gap: 2rem;">
    <?php include __DIR__ . '/../includes/sidebar_client.php'; ?>
    
    <div style="flex: 1; padding: 2rem 0;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Nos vÃ©hicules disponibles</h2>
        </div>
        
        <!-- Filtres -->
        <form method="GET" class="card mb-4">
            <div class="card-body d-flex" style="gap: 1rem; align-items: flex-end;">
                <div class="form-group mb-0" style="flex: 1;">
                    <label class="form-label">Type de carburant</label>
                    <select name="carburant" class="form-control">
                        <option value="">Tous</option>
                        <option value="Essence" <?php echo $carburant === 'Essence' ? 'selected' : ''; ?>>Essence</option>
                        <option value="Diesel" <?php echo $carburant === 'Diesel' ? 'selected' : ''; ?>>Diesel</option>
                        <option value="Electrique" <?php echo $carburant === 'Electrique' ? 'selected' : ''; ?>>Ã‰lectrique</option>
                        <option value="Hybride" <?php echo $carburant === 'Hybride' ? 'selected' : ''; ?>>Hybride</option>
                    </select>
                </div>
                <div class="form-group mb-0" style="flex: 1;">
                    <label class="form-label">Prix max. (FCFA/jour)</label>
                    <input type="number" name="prix_max" class="form-control" placeholder="Ex: 50000" value="<?php echo htmlspecialchars($prix_max); ?>">
                </div>
                <button type="submit" class="btn btn-primary" style="height: 45px;">Filtrer</button>
                <a href="/Projet_Auto/client/vehicles.php" class="btn btn-outline" style="height: 45px; display: flex; align-items: center;">RÃ©initialiser</a>
            </div>
        </form>
        
        <!-- Liste des vÃ©hicules -->
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
            <?php if(empty($vehicles)): ?>
                <p>Aucun vÃ©hicule ne correspond Ã  vos critÃ¨res.</p>
            <?php endif; ?>
            
            <?php foreach($vehicles as $v): ?>
            <div class="card">
                <div style="height: 200px; background-color: var(--gray-100); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                    <?php if($v['image']): ?>
                        <img src="/Projet_Auto/assets/images/<?php echo htmlspecialchars($v['image']); ?>" alt="Voiture" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <i class="fa fa-car" style="font-size: 4rem; color: var(--gray-300);"></i>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($v['marque'] . ' ' . $v['modele']); ?></h3>
                    <p style="color: var(--gray-500); font-size: 0.875rem; margin-bottom: 1rem;">
                        <?php echo htmlspecialchars($v['carburant']); ?> &bull; <?php echo htmlspecialchars($v['transmission']); ?>
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span style="font-weight: 700; font-size: 1.125rem;"><?php echo formatPrice($v['prix_jour']); ?></span> <span style="color: var(--gray-500); font-size: 0.875rem;">/ jour</span>
                        </div>
                        <a href="/Projet_Auto/client/vehicle_detail.php?id=<?php echo $v['id']; ?>" class="btn btn-outline" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">Voir dÃ©tails</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

