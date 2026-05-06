<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

// Envoyer une notification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_notif'])) {
    $uid = (int)$_POST['utilisateur_id'];
    $titre = clean($_POST['titre']);
    $contenu = clean($_POST['contenu']);
    $type = $_POST['type'];

    $stmt = $pdo->prepare("INSERT INTO messages (utilisateur_id, titre, contenu, type) VALUES (:uid, :titre, :contenu, :type)");
    $stmt->execute([
        ':uid' => $uid,
        ':titre' => $titre,
        ':contenu' => $contenu,
        ':type' => $type
    ]);
    setFlash('success', "Notification envoyée avec succès.");
    redirect('notifications.php');
}

// Récupérer tous les clients
$stmt = $pdo->query("SELECT id, prenom, nom, email FROM utilisateurs WHERE role = 'client' AND is_deleted = 0 ORDER BY prenom ASC");
$clients = $stmt->fetchAll();

// Récupérer les dernières notifications envoyées
$stmt = $pdo->query("SELECT m.*, u.prenom, u.nom 
                     FROM messages m 
                     JOIN utilisateurs u ON m.utilisateur_id = u.id 
                     ORDER BY m.date_creation DESC LIMIT 20");
$notifications = $stmt->fetchAll();

$pageTitle = "Gestion des Notifications";
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
            <h1>Gestion des Notifications</h1>
            <button class="btn btn-primary" onclick="document.getElementById('notifModal').style.display='flex'">
                <i class="fas fa-plus"></i> Nouvelle Notification
            </button>
        </header>

        <?php $flash = getFlash(); if ($flash): ?>
            <div class="flash flash-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
        <?php endif; ?>

        <div class="table-container">
            <div class="table-header">
                <h3>Dernières notifications envoyées</h3>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Destinataire</th>
                        <th>Titre</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($notifications as $n): ?>
                    <tr>
                        <td><strong><?= clean($n['prenom'] . ' ' . $n['nom']) ?></strong></td>
                        <td><?= clean($n['titre']) ?></td>
                        <td><span class="badge badge-<?= $n['type'] ?>"><?= ucfirst($n['type']) ?></span></td>
                        <td><?= formatDateTime($n['date_creation']) ?></td>
                        <td><?= $n['lu'] ? '<span class="badge badge-success">Lu</span>' : '<span class="badge badge-warning">Non lu</span>' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal Nouvelle Notification -->
        <div id="notifModal" class="modal-overlay">
            <div class="modal">
                <h3>Envoyer une notification</h3>
                <form action="notifications.php" method="POST">
                    <div class="form-group">
                        <label for="utilisateur_id">Destinataire</label>
                        <select name="utilisateur_id" id="utilisateur_id" class="form-control" required>
                            <?php foreach ($clients as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= clean($c['prenom'] . ' ' . $c['nom']) ?> (<?= $c['email'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="titre">Titre</label>
                        <input type="text" name="titre" id="titre" class="form-control" required placeholder="Ex: Rappel de réservation">
                    </div>
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select name="type" id="type" class="form-control">
                            <option value="info">Information</option>
                            <option value="success">Succès</option>
                            <option value="warning">Avertissement</option>
                            <option value="error">Erreur</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="contenu">Message</label>
                        <textarea name="contenu" id="contenu" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn btn-outline" onclick="document.getElementById('notifModal').style.display='none'">Annuler</button>
                        <button type="submit" name="send_notif" class="btn btn-primary">Envoyer</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
