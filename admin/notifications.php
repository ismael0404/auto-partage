<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

$adminId = $_SESSION['user_id'];

// Marquer tout comme lu
if (isset($_POST['mark_all_read'])) {
    $stmt = $pdo->prepare("UPDATE messages SET lu = 1 WHERE utilisateur_id = :uid");
    $stmt->execute([':uid' => $adminId]);
    setFlash('success', "Toutes les notifications ont été marquées comme lues.");
    redirect('notifications.php');
}

// Marquer une notification spécifique comme lue
if (isset($_POST['mark_read'])) {
    $notifId = (int)$_POST['mark_read'];
    $stmt = $pdo->prepare("UPDATE messages SET lu = 1 WHERE id = :id AND utilisateur_id = :uid");
    $stmt->execute([':id' => $notifId, ':uid' => $adminId]);
    setFlash('success', "Notification marquée comme lue.");
    redirect('notifications.php');
}

// Récupérer les notifications de l'admin
$stmt = $pdo->prepare("SELECT * FROM messages WHERE utilisateur_id = :uid ORDER BY date_creation DESC");
$stmt->execute([':uid' => $adminId]);
$notifications = $stmt->fetchAll();

$pageTitle = "Mes Notifications";
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
            <h1>Mes Notifications</h1>
            <form action="notifications.php" method="POST">
                <button type="submit" name="mark_all_read" class="btn btn-outline">
                    <i class="fas fa-check-double"></i> Tout marquer comme lu
                </button>
            </form>
        </header>

        <?php $flash = getFlash(); if ($flash): ?>
            <div class="flash flash-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
        <?php endif; ?>

        <div class="table-container">
            <div class="table-header">
                <h3>Centre de notifications</h3>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Message</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($notifications)): ?>
                        <tr>
                            <td colspan="5" class="text-center">Aucune notification pour le moment.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($notifications as $n): ?>
                        <tr class="<?= !$n['lu'] ? 'unread-row' : '' ?>" style="<?= !$n['lu'] ? 'background: rgba(17,17,17,0.02); font-weight: 500;' : '' ?>">
                            <td>
                                <?php if (!$n['lu']): ?>
                                    <span style="display: inline-block; width: 8px; height: 8px; background: var(--primary); border-radius: 50%; margin-right: 8px;"></span>
                                <?php endif; ?>
                                <?= clean($n['titre']) ?>
                            </td>
                            <td><small><?= clean($n['contenu']) ?></small></td>
                            <td><span class="badge badge-<?= $n['type'] ?>"><?= ucfirst($n['type']) ?></span></td>
                            <td><?= formatDateTime($n['date_creation']) ?></td>
                            <td>
                                <?php if (!$n['lu']): ?>
                                    <form action="notifications.php" method="POST" style="display: inline;">
                                        <input type="hidden" name="mark_read" value="<?= $n['id'] ?>">
                                        <button type="submit" class="btn btn-outline btn-sm">Marquer lu</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-secondary text-sm">Déjà lu</span>
                                <?php endif; ?>
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
