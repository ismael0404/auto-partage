<?php
// client/reserve.php
require_once __DIR__ . '/../includes/header.php';
requireRole('client');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: /Projet_Auto/client/vehicles.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ? AND deleted_at IS NULL AND statut != 'maintenance'");
$stmt->execute([$id]);
$vehicle = $stmt->fetch();

if (!$vehicle) {
    setFlashMessage("Ce vÃ©hicule n'est pas disponible pour la rÃ©servation.", "error");
    header("Location: /Projet_Auto/client/vehicles.php");
    exit();
}
?>

<div class="d-flex" style="max-width: 1200px; margin: 0 auto; gap: 2rem;">
    <?php include __DIR__ . '/../includes/sidebar_client.php'; ?>
    
    <div style="flex: 1; padding: 2rem 0;">
        <a href="/Projet_Auto/client/vehicle_detail.php?id=<?php echo $vehicle['id']; ?>" style="display: inline-block; margin-bottom: 1rem; color: var(--gray-500);"><i class="fa fa-arrow-left"></i> Retour</a>
        
        <div class="d-flex" style="gap: 2rem;">
            <div style="flex: 2;">
                <h2 class="mb-4">RÃ©server <?php echo htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']); ?></h2>
                
                <form action="/Projet_Auto/client/reserve_process.php" method="POST" class="card">
                    <input type="hidden" name="vehicle_id" value="<?php echo $vehicle['id']; ?>">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Date et heure de dÃ©but</label>
                            <input type="datetime-local" name="date_debut" id="date_debut" class="form-control" required min="<?php echo date('Y-m-d\TH:i'); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Date et heure de fin</label>
                            <input type="datetime-local" name="date_fin" id="date_fin" class="form-control" required min="<?php echo date('Y-m-d\TH:i'); ?>">
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Confirmer la rÃ©servation</button>
                    </div>
                </form>
            </div>
            
            <div style="flex: 1;">
                <div class="card" style="position: sticky; top: 100px;">
                    <div class="card-body">
                        <h3 class="mb-3">RÃ©capitulatif</h3>
                        <p style="font-weight: 600; margin-bottom: 1rem;"><?php echo htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']); ?></p>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span style="color: var(--gray-600);">Prix par jour</span>
                            <span style="font-weight: 600;"><?php echo formatPrice($vehicle['prix_jour']); ?></span>
                        </div>
                        
                        <div id="recap-dates" style="display: none; border-top: 1px solid var(--gray-200); padding-top: 1rem; margin-top: 1rem;">
                            <div class="d-flex justify-content-between mb-2">
                                <span style="color: var(--gray-600);">DurÃ©e</span>
                                <span style="font-weight: 600;" id="recap-duree">-</span>
                            </div>
                            <div class="d-flex justify-content-between" style="font-size: 1.25rem; margin-top: 1rem;">
                                <span style="font-weight: 700;">Total</span>
                                <span style="font-weight: 700;" id="recap-total">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const prixJour = <?php echo $vehicle['prix_jour']; ?>;
const dateDebutInput = document.getElementById('date_debut');
const dateFinInput = document.getElementById('date_fin');
const recapDates = document.getElementById('recap-dates');
const recapDuree = document.getElementById('recap-duree');
const recapTotal = document.getElementById('recap-total');

function calculateTotal() {
    const debut = new Date(dateDebutInput.value);
    const fin = new Date(dateFinInput.value);
    
    if (debut && fin && fin > debut) {
        const diffTime = Math.abs(fin - debut);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        recapDates.style.display = 'block';
        recapDuree.innerText = diffDays + ' jour(s)';
        
        const total = diffDays * prixJour;
        recapTotal.innerText = new Intl.NumberFormat('fr-FR').format(total) + ' FCFA';
    } else {
        recapDates.style.display = 'none';
    }
}

dateDebutInput.addEventListener('change', calculateTotal);
dateFinInput.addEventListener('change', calculateTotal);
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

