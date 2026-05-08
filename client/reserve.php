<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireClient();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('/client/vehicles.php');
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM vehicules WHERE id = :id");
$stmt->execute([':id' => $id]);
$vehicle = $stmt->fetch();

if (!$vehicle || $vehicle['statut'] !== 'disponible') {
    setFlash('error', "Ce véhicule n'est pas disponible à la réservation.");
    redirect('/client/vehicles.php');
}

$error = '';
$date_debut = isset($_POST['date_debut']) ? $_POST['date_debut'] : date('Y-m-d\TH:i', strtotime('+1 day'));
$date_fin = isset($_POST['date_fin']) ? $_POST['date_fin'] : date('Y-m-d\TH:i', strtotime('+2 days'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    if (empty($date_debut) || empty($date_fin)) {
        $error = "Veuillez sélectionner les dates de début et de fin.";
    } elseif (strtotime($date_debut) >= strtotime($date_fin)) {
        $error = "La date de fin doit être après la date de début.";
    } elseif (strtotime($date_debut) < time()) {
        $error = "La date de début ne peut pas être dans le passé.";
    } elseif (!vehiculeDisponible($pdo, $id, $date_debut, $date_fin)) {
        $error = "Le véhicule n'est pas disponible pour ces dates.";
    } else {
        $duree = calculerDuree($date_debut, $date_fin);
        $prix_total = $duree * $vehicle['prix_jour'];

        $stmt = $pdo->prepare("INSERT INTO reservations (utilisateur_id, vehicule_id, date_debut, date_fin, duree_jours, prix_unitaire, prix_total, statut) VALUES (:uid, :vid, :debut, :fin, :duree, :prix_u, :prix_t, 'en_attente')");
        
        try {
            $stmt->execute([
                ':uid' => $_SESSION['user_id'],
                ':vid' => $id,
                ':debut' => $date_debut,
                ':fin' => $date_fin,
                ':duree' => $duree,
                ':prix_u' => $vehicle['prix_jour'],
                ':prix_t' => $prix_total
            ]);

            // Notification pour l'admin
            $admin_notif_stmt = $pdo->prepare("INSERT INTO messages (utilisateur_id, titre, contenu, type) VALUES (1, :titre, :contenu, 'info')");
            $admin_notif_stmt->execute([
                ':titre' => "Nouvelle réservation",
                ':contenu' => $_SESSION['user_prenom'] . " a réservé le véhicule " . $vehicle['marque'] . " " . $vehicle['modele'] . "."
            ]);

            setFlash('success', "Votre réservation a été enregistrée et est en attente de confirmation.");
            redirect('/client/reservations.php');
        } catch (PDOException $e) {
            $error = "Une erreur est survenue lors de l'enregistrement de la réservation.";
        }
    }
}

$pageTitle = "Réserver " . $vehicle['marque'] . ' ' . $vehicle['modele'];
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
            <a href="vehicle_detail.php?id=<?= $id ?>" class="back-link">&larr; Retour</a>
            <h1>Réserver <?= clean($vehicle['marque'] . ' ' . $vehicle['modele']) ?></h1>
        </header>

        <?php if ($error): ?>
            <div class="flash flash-error"><?= $error ?></div>
        <?php endif; ?>

        <div class="reserve-page">
            <div class="reserve-grid">
                <div class="reserve-form">
                    <div class="table-container p-4" style="padding: 24px;">
                        <form action="reserve.php?id=<?= $id ?>" method="POST" id="reservationForm">
                            <div class="form-group">
                                <label for="date_debut">Date de début</label>
                                <input type="datetime-local" name="date_debut" id="date_debut" class="form-control" required value="<?= clean($date_debut) ?>">
                            </div>
                            <div class="form-group">
                                <label for="date_fin">Date de fin</label>
                                <input type="datetime-local" name="date_fin" id="date_fin" class="form-control" required value="<?= clean($date_fin) ?>">
                            </div>
                            <button type="submit" class="btn btn-primary btn-block mt-4">Confirmer la réservation</button>
                        </form>
                    </div>
                </div>
                
                <div class="reserve-summary">
                    <h3>Récapitulatif</h3>
                    <div class="flex gap-2 mb-3">
                        <img src="<?= getVehiculeImage($vehicle['image']) ?>" alt="" style="width: 100px; border-radius: 4px;">
                        <div>
                            <p style="font-weight: 700;"><?= clean($vehicle['marque'] . ' ' . $vehicle['modele']) ?></p>
                            <p style="font-size: 0.8rem; color: var(--secondary);"><?= clean($vehicle['type_carburant']) ?> • <?= $vehicle['annee'] ?></p>
                        </div>
                    </div>
                    
                    <div class="summary-row">
                        <span class="label">Prix par jour</span>
                        <span><?= formatPrix($vehicle['prix_jour']) ?></span>
                    </div>
                    <div class="summary-row">
                        <span class="label">Durée</span>
                        <span id="display-duree">1 jour</span>
                    </div>
                    <div class="summary-total">
                        <span>Total</span>
                        <span id="display-total"><?= formatPrix($vehicle['prix_jour']) ?></span>
                    </div>
                    
                    <p class="mt-3" style="font-size: 0.8rem; color: var(--secondary);">* Le prix total définitif sera calculé lors de la validation.</p>
                </div>
            </div>
        </div>
    </main>

    <script>
        const dateDebutInput = document.getElementById('date_debut');
        const dateFinInput = document.getElementById('date_fin');
        const displayDuree = document.getElementById('display-duree');
        const displayTotal = document.getElementById('display-total');
        const prixJour = <?= $vehicle['prix_jour'] ?>;

        function updateSummary() {
            const debut = new Date(dateDebutInput.value);
            const fin = new Date(dateFinInput.value);

            if (debut && fin && fin > debut) {
                const diffTime = Math.abs(fin - debut);
                const diffDays = Math.max(1, Math.ceil(diffTime / (1000 * 60 * 60 * 24)));
                const total = diffDays * prixJour;

                displayDuree.textContent = diffDays + (diffDays > 1 ? ' jours' : ' jour');
                displayTotal.textContent = total.toLocaleString('fr-FR') + ' FCFA';
            }
        }

        dateDebutInput.addEventListener('change', updateSummary);
        dateFinInput.addEventListener('change', updateSummary);
        updateSummary();
    </script>
</body>
</html>
