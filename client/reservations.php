<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireClient();

$userId = $_SESSION['user_id'];

// Annuler une réservation
if (isset($_POST['cancel_id'])) {
    $cancelId = (int)$_POST['cancel_id'];
    
    // Vérifier que la réservation appartient à l'utilisateur et est en attente
    $stmt = $pdo->prepare("SELECT statut FROM reservations WHERE id = :id AND utilisateur_id = :uid");
    $stmt->execute([':id' => $cancelId, ':uid' => $userId]);
    $res = $stmt->fetch();
    
    if ($res && $res['statut'] === 'en_attente') {
        $stmt = $pdo->prepare("UPDATE reservations SET statut = 'annulee' WHERE id = :id");
        $stmt->execute([':id' => $cancelId]);
        setFlash('success', "Réservation annulée avec succès.");
    } else {
        setFlash('error', "Vous ne pouvez plus annuler cette réservation.");
    }
    redirect('/client/reservations.php');
}

$stmt = $pdo->prepare("SELECT r.*, v.marque, v.modele, v.image, v.immatriculation, v.type_carburant, v.transmission, v.nombre_places 
                       FROM reservations r 
                       JOIN vehicules v ON r.vehicule_id = v.id 
                       WHERE r.utilisateur_id = :uid 
                       ORDER BY r.date_creation DESC");
$stmt->execute([':uid' => $userId]);
$reservations = $stmt->fetchAll();

$pageTitle = "Mes réservations";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - AutoPartage</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="dashboard">
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <header class="dashboard-header">
            <h1>Mes réservations</h1>
        </header>

        <?php $flash = getFlash(); if ($flash): ?>
            <div class="flash flash-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Véhicule</th>
                        <th>Période</th>
                        <th>Durée</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reservations)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Vous n'avez aucune réservation.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reservations as $res): ?>
                        <tr>
                            <td>
                                <div class="flex gap-1">
                                    <img src="<?= getVehiculeImage($res['image']) ?>" alt="" style="width: 50px; height: 40px; object-fit: cover; border-radius: 4px;">
                                    <div>
                                        <strong><?= clean($res['marque'] . ' ' . $res['modele']) ?></strong>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?= formatDateTime($res['date_debut']) ?><br>
                                <?= formatDateTime($res['date_fin']) ?>
                            </td>
                            <td><?= $res['duree_jours'] ?> jour(s)</td>
                            <td><strong><?= formatPrix($res['prix_total']) ?></strong></td>
                            <td><?= getStatutBadge($res['statut']) ?></td>
                            <td class="flex gap-1">
                                <?php if ($res['statut'] === 'en_attente'): ?>
                                    <form action="reservations.php" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?');">
                                        <input type="hidden" name="cancel_id" value="<?= $res['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Annuler</button>
                                    </form>
                                <?php elseif ($res['statut'] === 'confirmee'): ?>
                                    <?php if ($res['statut_paiement'] === 'non_paye' && !$res['mode_paiement']): ?>
                                        <a href="paiement.php?id=<?= $res['id'] ?>" class="btn btn-primary btn-sm">Régler le paiement</a>
                                    <?php else: ?>
                                        <a href="recu.php?id=<?= $res['id'] ?>" class="btn btn-success btn-sm" target="_blank"><i class="fas fa-download"></i> Reçu</a>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <button class="btn btn-outline btn-sm" onclick='showDetails(<?= json_encode($res) ?>)'>Voir</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal Détails -->
    <div id="detailsModal" class="modal-overlay">
        <div class="modal" style="max-width: 600px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 id="modalTitle">Détails de la réservation</h3>
                <button onclick="closeModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            
            <div id="modalContent">
                <div class="flex gap-2 mb-3">
                    <img id="modalImage" src="" alt="" style="width: 200px; height: 130px; object-fit: cover; border-radius: 8px;">
                    <div>
                        <h2 id="modalVehicleName" style="margin-bottom: 5px;"></h2>
                        <span id="modalBadge" class="badge"></span>
                        <div style="margin-top: 10px; font-size: 0.9rem; color: var(--secondary);">
                            <p><i class="fas fa-id-card"></i> Immatriculation : <strong id="modalPlate"></strong></p>
                            <p><i class="fas fa-gas-pump"></i> Énergie : <strong id="modalFuel"></strong></p>
                            <p><i class="fas fa-cog"></i> Boîte : <strong id="modalGearbox"></strong></p>
                            <p><i class="fas fa-users"></i> Places : <strong id="modalSeats"></strong></p>
                        </div>
                    </div>
                </div>

                <div style="background: var(--bg-alt); padding: 15px; border-radius: 8px; margin-top: 20px;">
                    <h4 style="margin-bottom: 10px; border-bottom: 1px solid var(--border); padding-bottom: 5px;">Informations de location</h4>
                    <div class="grid-2">
                        <div>
                            <p class="text-sm text-secondary">Date de début</p>
                            <p id="modalDateStart" class="font-bold"></p>
                        </div>
                        <div>
                            <p class="text-sm text-secondary">Date de fin</p>
                            <p id="modalDateEnd" class="font-bold"></p>
                        </div>
                        <div>
                            <p class="text-sm text-secondary">Durée totale</p>
                            <p id="modalDuration" class="font-bold"></p>
                        </div>
                        <div>
                            <p class="text-sm text-secondary">Prix Total</p>
                            <p id="modalPrice" class="font-bold text-primary" style="font-size: 1.2rem;"></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-actions" style="margin-top: 20px;">
                <button class="btn btn-primary btn-block" onclick="closeModal()">Fermer</button>
            </div>
        </div>
    </div>

    <script>
        function showDetails(res) {
            document.getElementById('modalVehicleName').innerText = res.marque + ' ' + res.modele;
            document.getElementById('modalImage').src = res.image ? '../assets/images/vehicules/' + res.image : 'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=400&h=300&fit=crop';
            document.getElementById('modalPlate').innerText = res.immatriculation || 'N/A';
            document.getElementById('modalFuel').innerText = res.type_carburant;
            document.getElementById('modalGearbox').innerText = res.transmission;
            document.getElementById('modalSeats').innerText = res.nombre_places;
            document.getElementById('modalDateStart').innerText = new Date(res.date_debut).toLocaleString('fr-FR');
            document.getElementById('modalDateEnd').innerText = new Date(res.date_fin).toLocaleString('fr-FR');
            document.getElementById('modalDuration').innerText = res.duree_jours + ' jour(s)';
            document.getElementById('modalPrice').innerText = new Intl.NumberFormat('fr-FR').format(res.prix_total) + ' FCFA';
            
            // Badge statut
            const badge = document.getElementById('modalBadge');
            badge.innerText = res.statut.replace('_', ' ').toUpperCase();
            badge.className = 'badge badge-' + (res.statut === 'confirmee' ? 'success' : (res.statut === 'en_attente' ? 'warning' : 'danger'));

            document.getElementById('detailsModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('detailsModal').style.display = 'none';
        }

        // Fermer au clic extérieur
        window.onclick = function(event) {
            const modal = document.getElementById('detailsModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
