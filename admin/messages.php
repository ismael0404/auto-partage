<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

$adminId = $_SESSION['user_id'];
$selectedClientId = isset($_GET['client_id']) ? (int)$_GET['client_id'] : null;

// Envoyer un message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $selectedClientId && isset($_POST['message']) && !empty(trim($_POST['message']))) {
    $message = clean($_POST['message']);
    $stmt = $pdo->prepare("INSERT INTO chat_messages (expediteur_id, destinataire_id, message) VALUES (:exp, :dest, :msg)");
    $stmt->execute([
        ':exp' => $adminId,
        ':dest' => $selectedClientId,
        ':msg' => $message
    ]);
    redirect("/admin/messages.php?client_id=$selectedClientId");
}

// Liste des clients avec qui il y a une discussion
$stmt = $pdo->query("SELECT DISTINCT u.id, u.prenom, u.nom, u.email,
                    (SELECT COUNT(*) FROM chat_messages WHERE expediteur_id = u.id AND destinataire_id = $adminId AND lu = 0) as unread_count,
                    (SELECT MAX(date_envoi) FROM chat_messages WHERE expediteur_id = u.id OR destinataire_id = u.id) as last_msg_date
                    FROM utilisateurs u
                    JOIN chat_messages cm ON u.id = cm.expediteur_id OR u.id = cm.destinataire_id
                    WHERE u.role = 'client'
                    ORDER BY last_msg_date DESC");
$chatList = $stmt->fetchAll();

// Charger la conversation si un client est sélectionné
$chatMessages = [];
$selectedClient = null;
if ($selectedClientId) {
    // Marquer comme lu
    $stmt = $pdo->prepare("UPDATE chat_messages SET lu = 1 WHERE destinataire_id = :admin_id AND expediteur_id = :uid");
    $stmt->execute([':admin_id' => $adminId, ':uid' => $selectedClientId]);

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = :id");
    $stmt->execute([':id' => $selectedClientId]);
    $selectedClient = $stmt->fetch();

    $stmt = $pdo->prepare("SELECT * FROM chat_messages 
                           WHERE (expediteur_id = :admin_id1 AND destinataire_id = :uid1) 
                           OR (expediteur_id = :uid2 AND destinataire_id = :admin_id2) 
                           ORDER BY date_envoi ASC");
    $stmt->execute([
        ':admin_id1' => $adminId, 
        ':uid1' => $selectedClientId,
        ':uid2' => $selectedClientId,
        ':admin_id2' => $adminId
    ]);
    $chatMessages = $stmt->fetchAll();
}

$pageTitle = "Messagerie Clients";
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
            <h1>Messagerie Clients</h1>
        </header>

        <div class="chat-wrapper" style="flex-direction: row;">
            <!-- Liste des discussions -->
            <div class="chat-list">
                <div style="padding: 16px; border-bottom: 1px solid var(--border); font-weight: 700; background: var(--bg-alt);">
                    Discussions
                </div>
                <?php if (empty($chatList)): ?>
                    <p style="padding: 20px; text-align: center; font-size: 0.8rem; color: var(--secondary);">Aucune discussion en cours.</p>
                <?php else: ?>
                    <?php foreach ($chatList as $item): ?>
                        <a href="messages.php?client_id=<?= $item['id'] ?>" class="chat-list-item <?= $selectedClientId == $item['id'] ? 'active' : '' ?>">
                            <div style="width: 35px; height: 35px; background: #ddd; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">
                                <?= strtoupper($item['prenom'][0] . $item['nom'][0]) ?>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 600; font-size: 0.9rem;"><?= clean($item['prenom'] . ' ' . $item['nom']) ?></div>
                                <div style="font-size: 0.7rem; color: var(--secondary);">Client</div>
                            </div>
                            <?php if ($item['unread_count'] > 0): ?>
                                <span class="unread-count"><?= $item['unread_count'] ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Fenêtre de chat -->
            <div style="flex: 1; display: flex; flex-direction: column;">
                <?php if ($selectedClient): ?>
                    <div class="chat-header">
                        <strong><?= clean($selectedClient['prenom'] . ' ' . $selectedClient['nom']) ?></strong>
                    </div>

                    <div class="chat-messages" id="chatBox">
                        <?php foreach ($chatMessages as $msg): ?>
                            <div class="message-bubble <?= $msg['expediteur_id'] == $adminId ? 'message-sent' : 'message-received' ?>">
                                <?= nl2br(clean($msg['message'])) ?>
                                <div class="message-time">
                                    <?= date('H:i', strtotime($msg['date_envoi'])) ?>
                                    <?php if ($msg['expediteur_id'] == $adminId): ?>
                                        <i class="fas fa-check-double" style="color: <?= $msg['lu'] ? '#53bdeb' : '#8696a0' ?>; margin-left: 4px;"></i>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <form action="messages.php?client_id=<?= $selectedClientId ?>" method="POST" class="chat-input-area">
                        <input type="text" name="message" placeholder="Répondre au client..." required autocomplete="off">
                        <button type="submit"><i class="fas fa-paper-plane"></i></button>
                    </form>
                <?php else: ?>
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--secondary); background: #f0f2f5;">
                        <i class="fas fa-comments" style="font-size: 4rem; opacity: 0.2; margin-bottom: 20px;"></i>
                        <p>Sélectionnez un client pour démarrer la discussion.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>
        const chatBox = document.getElementById('chatBox');
        if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
    </script>
</body>
</html>
