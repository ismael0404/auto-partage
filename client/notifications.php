<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireLogin();

$userId = $_SESSION['user_id'];

// Marquer tout comme lu
if (isset($_POST['mark_read'])) {
    $stmt = $pdo->prepare("UPDATE messages SET lu = 1 WHERE utilisateur_id = :uid");
    $stmt->execute([':uid' => $userId]);
    redirect('/client/messages.php');
}

$stmt = $pdo->prepare("SELECT * FROM messages WHERE utilisateur_id = :uid ORDER BY date_creation DESC");
$stmt->execute([':uid' => $userId]);
$messages = $stmt->fetchAll();

$pageTitle = "Mes notifications";
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
            <h1>Mes notifications</h1>
            <form action="notifications.php" method="POST">
                <button type="submit" name="mark_read" class="btn btn-outline btn-sm">Tout marquer comme lu</button>
            </form>
        </header>

        <div class="messages-list" style="display: flex; flex-direction: column; gap: 16px;">
            <?php if (empty($messages)): ?>
                <div class="table-container p-4 text-center" style="padding: 40px;">
                    <p>Vous n'avez aucun message.</p>
                </div>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                <div class="table-container" style="padding: 20px; border-left: 4px solid <?= $msg['lu'] ? 'var(--border)' : 'var(--primary)' ?>; background: <?= $msg['lu'] ? '#fff' : '#f9f9f9' ?>;">
                    <div class="flex-between mb-1">
                        <h3 style="font-size: 1rem; color: <?= $msg['lu'] ? 'var(--secondary)' : 'var(--primary)' ?>;">
                            <?= clean($msg['titre']) ?>
                            <?php if (!$msg['lu']): ?><span class="badge badge-primary" style="background: var(--primary); color: #fff; margin-left: 8px;">Nouveau</span><?php endif; ?>
                        </h3>
                        <span style="font-size: 0.8rem; color: var(--secondary);"><?= formatDateTime($msg['date_creation']) ?></span>
                    </div>
                    <p style="color: var(--secondary); font-size: 0.9rem;"><?= nl2br(clean($msg['contenu'])) ?></p>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
