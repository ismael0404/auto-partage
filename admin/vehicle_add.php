<?php
// admin/vehicle_add.php
require_once __DIR__ . '/../includes/header.php';
requireRole('admin');

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
    
    // Upload d'image
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = uploadImage($_FILES['image'], __DIR__ . '/../assets/images');
        if ($uploadResult) {
            $image = $uploadResult;
        } else {
            // L'erreur est gÃ©rÃ©e dans setFlashMessage de uploadImage
            header("Location: /Projet_Auto/admin/vehicle_add.php");
            exit();
        }
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO vehicles (marque, modele, annee, image, prix_heure, prix_jour, carburant, transmission, places, climatisation, description, statut)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$marque, $modele, $annee, $image, $prix_heure, $prix_jour, $carburant, $transmission, $places, $climatisation, $description, $statut]);
        
        setFlashMessage("VÃ©hicule ajoutÃ© avec succÃ¨s.", "success");
        header("Location: /Projet_Auto/admin/vehicles.php");
        exit();
    } catch (PDOException $e) {
        setFlashMessage("Erreur lors de l'ajout du vÃ©hicule.", "error");
    }
}
?>

<div class="d-flex" style="max-width: 1200px; margin: 0 auto; gap: 2rem;">
    <?php include __DIR__ . '/../includes/sidebar_admin.php'; ?>
    
    <div style="flex: 1; padding: 2rem 0;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Ajouter un vÃ©hicule</h2>
            <a href="/Projet_Auto/admin/vehicles.php" class="btn btn-outline">Annuler</a>
        </div>
        
        <form action="" method="POST" enctype="multipart/form-data" class="card">
            <div class="card-body">
                <div class="d-flex" style="gap: 1.5rem;">
                    <div style="flex: 1;">
                        <div class="form-group">
                            <label class="form-label">Marque</label>
                            <input type="text" name="marque" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">ModÃ¨le</label>
                            <input type="text" name="modele" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">AnnÃ©e</label>
                            <input type="number" name="annee" class="form-control" min="1900" max="<?php echo date('Y') + 1; ?>" required>
                        </div>
                        <div class="d-flex" style="gap: 1rem;">
                            <div class="form-group" style="flex: 1;">
                                <label class="form-label">Prix par heure (FCFA)</label>
                                <input type="number" name="prix_heure" class="form-control" required>
                            </div>
                            <div class="form-group" style="flex: 1;">
                                <label class="form-label">Prix par jour (FCFA)</label>
                                <input type="number" name="prix_jour" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div style="flex: 1;">
                        <div class="form-group">
                            <label class="form-label">Carburant</label>
                            <select name="carburant" class="form-control" required>
                                <option value="Essence">Essence</option>
                                <option value="Diesel">Diesel</option>
                                <option value="Electrique">Ã‰lectrique</option>
                                <option value="Hybride">Hybride</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Transmission</label>
                            <select name="transmission" class="form-control" required>
                                <option value="Manuelle">Manuelle</option>
                                <option value="Automatique">Automatique</option>
                            </select>
                        </div>
                        <div class="d-flex" style="gap: 1rem;">
                            <div class="form-group" style="flex: 1;">
                                <label class="form-label">Nombre de places</label>
                                <input type="number" name="places" class="form-control" required min="1">
                            </div>
                            <div class="form-group" style="flex: 1; display: flex; align-items: flex-end; padding-bottom: 0.75rem;">
                                <label class="d-flex align-items-center" style="gap: 0.5rem; cursor: pointer;">
                                    <input type="checkbox" name="climatisation" value="1" checked> Climatisation
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Image (JPG, PNG, WEBP)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
                
                <div class="form-group mt-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Statut initial</label>
                    <select name="statut" class="form-control" style="width: 200px;">
                        <option value="disponible">Disponible</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Enregistrer le vÃ©hicule</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

