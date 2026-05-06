<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

// Actions sur les réservations
if (isset($_POST['action']) && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $action = $_POST['action'];
    $newStatut = '';

    if ($action === 'confirm') $newStatut = 'confirmee';
    elseif ($action === 'cancel') $newStatut = 'annulee';
    elseif ($action === 'finish') $newStatut = 'terminee';

    if ($newStatut) {
        $stmt = $pdo->prepare("UPDATE reservations SET statut = :st WHERE id = :id");
        $stmt->execute([':st' => $newStatut, ':id' => $id]);
        
        // Récupérer les infos pour notification et mise à jour véhicule
        $stmtRes = $pdo->prepare("SELECT r.utilisateur_id, r.vehicule_id, v.marque, v.modele FROM reservations r JOIN vehicules v ON r.vehicule_id = v.id WHERE r.id = :id");
        $stmtRes->execute([':id' => $id]);
        $resData = $stmtRes->fetch();

        // Mettre à jour le statut du véhicule
        if ($newStatut === 'confirmee') {
            $stmtVeh = $pdo->prepare("UPDATE vehicules SET statut = 'reserve' WHERE id = :vid");
            $stmtVeh->execute([':vid' => $resData['vehicule_id']]);
        } elseif ($newStatut === 'terminee' || $newStatut === 'annulee') {
            // Vérifier s'il n'y a pas d'autres réservations confirmées en cours pour ce véhicule
            $stmtVeh = $pdo->prepare("UPDATE vehicules SET statut = 'disponible' WHERE id = :vid");
            $stmtVeh->execute([':vid' => $resData['vehicule_id']]);
        }
        
        $msgTitre = "Mise à jour de votre réservation";
        $msgContenu = "Votre réservation pour le véhicule " . $resData['marque'] . " " . $resData['modele'] . " est passée au statut : " . $newStatut . ".";
        
        $stmtMsg = $pdo->prepare("INSERT INTO messages (utilisateur_id, titre, contenu, type) VALUES (:uid, :titre, :cont, :tp)");
        $stmtMsg->execute([
            ':uid' => $resData['utilisateur_id'],
            ':titre' => $msgTitre,
            ':cont' => $msgContenu,
            ':tp' => ($newStatut === 'confirmee' ? 'success' : ($newStatut === 'annulee' ? 'error' : 'info'))
        ]);

        setFlash('success', "Réservation mise à jour avec succès.");
    }
    redirect('/admin/reservations.php');
}

$stmt = $pdo->query("SELECT r.*, v.marque, v.modele, u.prenom, u.nom, u.email 
                     FROM reservations r 
                     JOIN vehicules v ON r.vehicule_id = v.id 
                     JOIN utilisateurs u ON r.utilisateur_id = u.id 
                     ORDER BY r.date_creation DESC");
$reservations = $stmt->fetchAll();

$pageTitle = "Gestion des réservations";
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
            <h1>Gestion des réservations</h1>
        </header>

        <?php $flash = getFlash(); if ($flash): ?>
            <div class="flash flash-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Véhicule</th>
                        <th>Période</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reservations)): ?>
                        <tr><td colspan="6" class="text-center">Aucune réservation.</td></tr>
                    <?php else: ?>
                        <?php foreach ($reservations as $res): ?>
                        <tr>
                            <td>
                                <strong><?= clean($res['prenom'] . ' ' . $res['nom']) ?></strong><br>
                                <span style="font-size: 0.8rem; color: var(--secondary);"><?= clean($res['email']) ?></span>
                            </td>
                            <td><?= clean($res['marque'] . ' ' . $res['modele']) ?></td>
                            <td>
                                <?= formatDate($res['date_debut']) ?> - <?= formatDate($res['date_fin']) ?><br>
                                <span style="font-size: 0.8rem; color: var(--secondary);"><?= $res['duree_jours'] ?> jour(s)</span>
                            </td>
                            <td><strong><?= formatPrix($res['prix_total']) ?></strong></td>
                            <td><?= getStatutBadge($res['statut']) ?></td>
                            <td>
                                <form action="reservations.php" method="POST" class="flex gap-1">
                                    <input type="hidden" name="id" value="<?= $res['id'] ?>">
                                    <?php if ($res['statut'] === 'en_attente'): ?>
                                        <button type="submit" name="action" value="confirm" class="btn btn-success btn-sm">Confirmer</button>
                                        <button type="submit" name="action" value="cancel" class="btn btn-danger btn-sm">Annuler</button>
                                    <?php elseif ($res['statut'] === 'confirmee'): ?>
                                        <button type="submit" name="action" value="finish" class="btn btn-primary btn-sm">Terminer</button>
                                        <button type="submit" name="action" value="cancel" class="btn btn-danger btn-sm">Annuler</button>
                                    <?php else: ?>
                                        <button class="btn btn-outline btn-sm" disabled>Aucune action</button>
                                    <?php endif; ?>
                                </form>
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
