<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireClient();

$userId = $_SESSION['user_id'];

// Trouver l'ID de l'administrateur
$stmt = $pdo->query("SELECT id FROM utilisateurs WHERE role = 'admin' LIMIT 1");
$admin = $stmt->fetch();
$adminId = $admin['id'];

// Envoyer un message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && !empty(trim($_POST['message']))) {
    $message = clean($_POST['message']);
    $stmt = $pdo->prepare("INSERT INTO chat_messages (expediteur_id, destinataire_id, message) VALUES (:exp, :dest, :msg)");
    $stmt->execute([
        ':exp' => $userId,
        ':dest' => $adminId,
        ':msg' => $message
    ]);
    redirect('/client/messages.php');
}

// Marquer comme lu
$stmt = $pdo->prepare("UPDATE chat_messages SET lu = 1 WHERE destinataire_id = :uid AND expediteur_id = :admin_id");
$stmt->execute([':uid' => $userId, ':admin_id' => $adminId]);

// Charger la conversation
$stmt = $pdo->prepare("SELECT * FROM chat_messages 
                       WHERE (expediteur_id = :uid1 AND destinataire_id = :admin_id1) 
                       OR (expediteur_id = :admin_id2 AND destinataire_id = :uid2) 
                       ORDER BY date_envoi ASC");
$stmt->execute([
    ':uid1' => $userId, 
    ':admin_id1' => $adminId, 
    ':admin_id2' => $adminId, 
    ':uid2' => $userId
]);
$chatMessages = $stmt->fetchAll();

$pageTitle = "Messagerie";
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
            <h1>Messagerie Admin</h1>
        </header>

        <div class="chat-wrapper">
            <div class="chat-header">
                <div class="flex gap-2">
                    <div style="width: 40px; height: 40px; background: var(--primary); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div>
                        <strong>Support AutoPartage</strong>
                        <p style="font-size: 0.7rem; color: var(--success);"><i class="fas fa-circle" style="font-size: 0.5rem;"></i> En ligne</p>
                    </div>
                </div>
            </div>

            <div class="chat-messages" id="chatBox">
                <?php if (empty($chatMessages)): ?>
                    <div style="text-align: center; margin-top: 50px; color: var(--secondary);">
                        <i class="fas fa-comments" style="font-size: 3rem; opacity: 0.2; margin-bottom: 10px;"></i>
                        <p>Commencez la discussion avec notre équipe de support.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($chatMessages as $msg): ?>
                        <div class="message-bubble <?= $msg['expediteur_id'] == $userId ? 'message-sent' : 'message-received' ?>">
                            <?= nl2br(clean($msg['message'])) ?>
                            <div class="message-time">
                                <?= date('H:i', strtotime($msg['date_envoi'])) ?>
                                <?php if ($msg['expediteur_id'] == $userId): ?>
                                    <i class="fas fa-check-double" style="color: <?= $msg['lu'] ? '#53bdeb' : '#8696a0' ?>; margin-left: 4px;"></i>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <form action="messages.php" method="POST" class="chat-input-area">
                <input type="text" name="message" placeholder="Tapez votre message ici..." required autocomplete="off">
                <button type="submit"><i class="fas fa-paper-plane"></i></button>
            </form>
        </div>
    </main>

    <script>
        // Scroll to bottom
        const chatBox = document.getElementById('chatBox');
        chatBox.scrollTop = chatBox.scrollHeight;
    </script>
</body>
</html>
