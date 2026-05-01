<?php
// admin/vehicle_edit.php
require_once __DIR__ . '/../includes/header.php';
requireRole('admin');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: /Projet_Auto/admin/vehicles.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marque = sanitize($_POST['marque'] ?? '');
    $modele = sanitize($_POST['modele'] ?? '');
    $annee = filter_input(INPUT_POST, 'annee', FILTER_VALIDATE_INT);
    $prix_heure = filter_input(INPUT_POST, 'prix_heure', FILTER_VALIDATE_FLOAT);
    $prix_jour = filter_input(INPUT_POST, 'prix_jour', FILTER_VALIDATE_FLOAT);
    $carburant = sanitize($_POST['carburant'] ?? '');
    $transmission = sanitize($_POST['transmission'] ?? '');
    $places = filter_input(INPUT_POST, 'places', FILTER_VALIDATE_INT);
    $climatisation = isset($_POST['climatisation']) ? 1 : 0;
    $description = sanitize($_POST['description'] ?? '');
    $statut = sanitize($_POST['statut'] ?? 'disponible');
    
    // RÃ©cupÃ©rer l'ancienne image
    $stmt = $pdo->prepare("SELECT image FROM vehicles WHERE id = ?");
    $stmt->execute([$id]);
    $oldImage = $stmt->fetchColumn();
    $image = $oldImage;
    
    // Upload d'une nouvelle image (optionnel)
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = uploadImage($_FILES['image'], __DIR__ . '/../assets/images');
        if ($uploadResult) {
            $image = $uploadResult;
            // Supprimer l'ancienne image si elle existe
            if ($oldImage && file_exists(__DIR__ . '/../assets/images/' . $oldImage)) {
                unlink(__DIR__ . '/../assets/images/' . $oldImage);
            }
        } else {
            header("Location: /Projet_Auto/admin/vehicle_edit.php?id=" . $id);
            exit();
        }
    }
    
    try {
        $stmt = $pdo->prepare("
            UPDATE vehicles 
            SET marque=?, modele=?, annee=?, image=?, prix_heure=?, prix_jour=?, carburant=?, transmission=?, places=?, climatisation=?, description=?, statut=?
            WHERE id = ?
        ");
        $stmt->execute([$marque, $modele, $annee, $image, $prix_heure, $prix_jour, $carburant, $transmission, $places, $climatisation, $description, $statut, $id]);
        
        setFlashMessage("VÃ©hicule mis Ã  jour avec succÃ¨s.", "success");
        header("Location: /Projet_Auto/admin/vehicles.php");
        exit();
    } catch (PDOException $e) {
        setFlashMessage("Erreur lors de la mise Ã  jour.", "error");
    }
}

// RÃ©cupÃ©rer les donnÃ©es pour le formulaire
$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ? AND deleted_at IS NULL");
$stmt->execute([$id]);
$vehicle = $stmt->fetch();

if (!$vehicle) {
    setFlashMessage("VÃ©hicule introuvable.", "error");
    header("Location: /Projet_Auto/admin/vehicles.php");
    exit();
}
?>

<div class="d-flex" style="max-width: 1200px; margin: 0 auto; gap: 2rem;">
    <?php include __DIR__ . '/../includes/sidebar_admin.php'; ?>
    
    <div style="flex: 1; padding: 2rem 0;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Modifier <?php echo htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']); ?></h2>
            <a href="/Projet_Auto/admin/vehicles.php" class="btn btn-outline">Annuler</a>
        </div>
        
        <form action="" method="POST" enctype="multipart/form-data" class="card">
            <div class="card-body">
                <div class="d-flex" style="gap: 1.5rem;">
                    <div style="flex: 1;">
                        <div class="form-group">
                            <label class="form-label">Marque</label>
                            <input type="text" name="marque" class="form-control" value="<?php echo htmlspecialchars($vehicle['marque']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">ModÃ¨le</label>
                            <input type="text" name="modele" class="form-control" value="<?php echo htmlspecialchars($vehicle['modele']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">AnnÃ©e</label>
                            <input type="number" name="annee" class="form-control" min="1900" max="<?php echo date('Y') + 1; ?>" value="<?php echo htmlspecialchars($vehicle['annee']); ?>" required>
                        </div>
                        <div class="d-flex" style="gap: 1rem;">
                            <div class="form-group" style="flex: 1;">
                                <label class="form-label">Prix par heure (FCFA)</label>
                                <input type="number" name="prix_heure" class="form-control" value="<?php echo htmlspecialchars($vehicle['prix_heure']); ?>" required>
                            </div>
                            <div class="form-group" style="flex: 1;">
                                <label class="form-label">Prix par jour (FCFA)</label>
                                <input type="number" name="prix_jour" class="form-control" value="<?php echo htmlspecialchars($vehicle['prix_jour']); ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div style="flex: 1;">
                        <div class="form-group">
                            <label class="form-label">Carburant</label>
                            <select name="carburant" class="form-control" required>
                                <option value="Essence" <?php echo $vehicle['carburant'] === 'Essence' ? 'selected' : ''; ?>>Essence</option>
                                <option value="Diesel" <?php echo $vehicle['carburant'] === 'Diesel' ? 'selected' : ''; ?>>Diesel</option>
                                <option value="Electrique" <?php echo $vehicle['carburant'] === 'Electrique' ? 'selected' : ''; ?>>Ã‰lectrique</option>
                                <option value="Hybride" <?php echo $vehicle['carburant'] === 'Hybride' ? 'selected' : ''; ?>>Hybride</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Transmission</label>
                            <select name="transmission" class="form-control" required>
                                <option value="Manuelle" <?php echo $vehicle['transmission'] === 'Manuelle' ? 'selected' : ''; ?>>Manuelle</option>
                                <option value="Automatique" <?php echo $vehicle['transmission'] === 'Automatique' ? 'selected' : ''; ?>>Automatique</option>
                            </select>
                        </div>
                        <div class="d-flex" style="gap: 1rem;">
                            <div class="form-group" style="flex: 1;">
                                <label class="form-label">Nombre de places</label>
                                <input type="number" name="places" class="form-control" value="<?php echo htmlspecialchars($vehicle['places']); ?>" required min="1">
                            </div>
                            <div class="form-group" style="flex: 1; display: flex; align-items: flex-end; padding-bottom: 0.75rem;">
                                <label class="d-flex align-items-center" style="gap: 0.5rem; cursor: pointer;">
                                    <input type="checkbox" name="climatisation" value="1" <?php echo $vehicle['climatisation'] ? 'checked' : ''; ?>> Climatisation
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nouvelle image (laisser vide pour conserver l'actuelle)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <?php if($vehicle['image']): ?>
                                <div class="mt-2 text-sm text-gray-500">Image actuelle : <?php echo htmlspecialchars($vehicle['image']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="form-group mt-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($vehicle['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-control" style="width: 200px;">
                        <option value="disponible" <?php echo $vehicle['statut'] === 'disponible' ? 'selected' : ''; ?>>Disponible</option>
                        <option value="reserve" <?php echo $vehicle['statut'] === 'reserve' ? 'selected' : ''; ?>>RÃ©servÃ©</option>
                        <option value="maintenance" <?php echo $vehicle['statut'] === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                    </select>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Mettre Ã  jour</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

