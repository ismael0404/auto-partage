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

$stmt = $pdo->prepare("SELECT r.*, v.marque, v.modele, v.image 
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
                                <?php endif; ?>
                                <button class="btn btn-outline btn-sm">Voir</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
