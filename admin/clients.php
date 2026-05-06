<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

$error = '';
$success = '';

// Actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action === 'suspend') {
        $pdo->prepare("UPDATE utilisateurs SET statut = 'suspendu' WHERE id = :id")->execute([':id' => $id]);
        setFlash('warning', "Compte client suspendu.");
    } elseif ($action === 'activate') {
        $pdo->prepare("UPDATE utilisateurs SET statut = 'actif' WHERE id = :id")->execute([':id' => $id]);
        setFlash('success', "Compte client activé.");
    } elseif ($action === 'delete') {
        $pdo->prepare("UPDATE utilisateurs SET is_deleted = 1 WHERE id = :id AND role = 'client'")->execute([':id' => $id]);
        setFlash('success', "Compte client supprimé (archivé).");
    }
    redirect('/admin/clients.php');
}

$stmt = $pdo->query("SELECT * FROM utilisateurs WHERE role = 'client' AND is_deleted = 0 ORDER BY date_creation DESC");
$clients = $stmt->fetchAll();

$pageTitle = "Gestion des clients";
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
            <h1>Gestion des clients</h1>
        </header>

        <?php $flash = getFlash(); if ($flash): ?>
            <div class="flash flash-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Email / Tél</th>
                        <th>Date d'inscription</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $c): ?>
                    <tr>
                        <td>
                            <div class="flex gap-1">
                                <div style="width: 35px; height: 35px; border-radius: 50%; background: #eee; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700;">
                                    <?= strtoupper(substr($c['prenom'], 0, 1) . substr($c['nom'], 0, 1)) ?>
                                </div>
                                <strong><?= clean($c['prenom'] . ' ' . $c['nom']) ?></strong>
                            </div>
                        </td>
                        <td>
                            <?= clean($c['email']) ?><br>
                            <span style="font-size: 0.8rem; color: var(--secondary);"><?= clean($c['telephone']) ?: 'Non renseigné' ?></span>
                        </td>
                        <td><?= formatDate($c['date_creation']) ?></td>
                        <td>
                            <span class="badge badge-<?= $c['statut'] === 'actif' ? 'success' : ($c['statut'] === 'suspendu' ? 'danger' : 'warning') ?>">
                                <?= ucfirst($c['statut']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="flex gap-1">
                                <?php if ($c['statut'] === 'actif'): ?>
                                    <a href="clients.php?action=suspend&id=<?= $c['id'] ?>" class="btn btn-warning btn-sm">Suspendre</a>
                                <?php else: ?>
                                    <a href="clients.php?action=activate&id=<?= $c['id'] ?>" class="btn btn-success btn-sm">Activer</a>
                                <?php endif; ?>
                                <a href="clients.php?action=delete&id=<?= $c['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce client ?')">Supprimer</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
