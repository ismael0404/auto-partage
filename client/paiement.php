<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireClient();

$userId = $_SESSION['user_id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Vérifier que la réservation appartient au client et est confirmée
$stmt = $pdo->prepare("SELECT r.*, v.marque, v.modele FROM reservations r JOIN vehicules v ON r.vehicule_id = v.id WHERE r.id = :id AND r.utilisateur_id = :uid AND r.statut = 'confirmee'");
$stmt->execute([':id' => $id, ':uid' => $userId]);
$res = $stmt->fetch();

if (!$res) {
    setFlash('error', "Réservation introuvable ou non éligible au paiement.");
    redirect('/client/reservations.php');
}

$step = isset($_POST['step']) ? $_POST['step'] : 'choice';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mode_place'])) {
        // Paiement sur place
        $stmt = $pdo->prepare("UPDATE reservations SET mode_paiement = 'sur_place' WHERE id = :id");
        $stmt->execute([':id' => $id]);

        // Notification pour l'admin
        $admin_notif_stmt = $pdo->prepare("INSERT INTO messages (utilisateur_id, titre, contenu, type) VALUES (1, :titre, :contenu, 'warning')");
        $admin_notif_stmt->execute([
            ':titre' => "Mode de paiement : Sur Place",
            ':contenu' => $_SESSION['user_prenom'] . " a choisi de payer sur place pour la réservation #" . $id . " (" . formatPrix($res['prix_total']) . ")."
        ]);

        setFlash('success', "Mode de paiement 'sur place' enregistré. Vous pouvez télécharger votre reçu.");
        redirect('/client/reservations.php');
    }

    if ($step === 'simulate' && isset($_POST['mode_ligne'])) {
        $step = 'details';
    }

    if ($step === 'process_details') {
        $step = 'otp';
    }

    if ($step === 'verify_otp') {
        $otp = $_POST['otp'];
        if ($otp === '1212') {
            $stmt = $pdo->prepare("UPDATE reservations SET mode_paiement = 'ligne', statut_paiement = 'paye' WHERE id = :id");
            $stmt->execute([':id' => $id]);

            // Notification pour l'admin
            $admin_notif_stmt = $pdo->prepare("INSERT INTO messages (utilisateur_id, titre, contenu, type) VALUES (1, :titre, :contenu, 'success')");
            $admin_notif_stmt->execute([
                ':titre' => "Paiement en ligne réussi",
                ':contenu' => $_SESSION['user_prenom'] . " a réglé " . formatPrix($res['prix_total']) . " en ligne pour la réservation #" . $id . "."
            ]);

            setFlash('success', "Paiement effectué avec succès !");
            redirect('/client/reservations.php');
        } else {
            $error = "Code OTP incorrect. Veuillez réessayer.";
            $step = 'otp';
        }
    }
}

$pageTitle = "Règlement de la réservation";
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
    <style>
        .provider-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-top: 20px; }
        .provider-item { border: 2px solid var(--border); border-radius: 12px; padding: 15px; cursor: pointer; transition: all 0.3s ease; text-align: center; }
        .provider-item:hover { border-color: var(--primary); background: var(--bg-alt); }
        .provider-item input { display: none; }
        .provider-item input:checked + .provider-content { color: var(--primary); font-weight: 700; }
        .provider-item input:checked + .provider-content i { transform: scale(1.2); color: var(--primary); }
        .provider-item.selected { border-color: var(--primary); background: rgba(0,0,0,0.02); }
    </style>
</head>
<body class="dashboard">
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <header class="dashboard-header">
            <h1>Paiement de votre réservation</h1>
        </header>

        <div class="profile-card" style="margin: 0 auto; max-width: 500px;">
            <div style="text-align: center; margin-bottom: 30px;">
                <i class="fas fa-credit-card" style="font-size: 2.5rem; color: var(--primary); margin-bottom: 10px;"></i>
                <h2 style="font-size: 1.5rem;">Montant : <?= formatPrix($res['prix_total']) ?></h2>
                <p style="color: var(--secondary); font-size: 0.9rem;"><?= clean($res['marque'] . ' ' . $res['modele']) ?></p>
            </div>

            <?php if (isset($error)): ?>
                <div class="flash flash-error"><?= $error ?></div>
            <?php endif; ?>

            <?php if ($step === 'choice'): ?>
                <form action="paiement.php?id=<?= $id ?>" method="POST" class="grid-2">
                    <button type="submit" name="mode_place" class="btn btn-outline" style="height: 140px; flex-direction: column; gap: 12px;">
                        <i class="fas fa-hand-holding-usd" style="font-size: 1.8rem;"></i>
                        <span style="font-size: 0.9rem;">Paiement sur place</span>
                    </button>
                    <button type="submit" name="mode_ligne" class="btn btn-primary" style="height: 140px; flex-direction: column; gap: 12px;">
                        <i class="fas fa-mobile-alt" style="font-size: 1.8rem;"></i>
                        <span style="font-size: 0.9rem;">Paiement maintenant</span>
                        <input type="hidden" name="step" value="simulate">
                    </button>
                </form>

            <?php elseif ($step === 'details'): ?>
                <form action="paiement.php?id=<?= $id ?>" method="POST">
                    <h3 class="text-center mb-3">Sélectionnez votre opérateur</h3>
                    <div class="provider-grid">
                        <label class="provider-item" onclick="selectProvider(this)">
                            <input type="radio" name="provider" value="wave" required>
                            <div class="provider-content">
                                <img src="../assets/images/paiement/wave logo.jfif" alt="Wave" style="width: 50px; height: 50px; object-fit: contain; margin-bottom: 8px;"><br>
                                <span>Wave</span>
                            </div>
                        </label>
                        <label class="provider-item" onclick="selectProvider(this)">
                            <input type="radio" name="provider" value="orange">
                            <div class="provider-content">
                                <img src="../assets/images/paiement/orange logo.jfif" alt="Orange" style="width: 50px; height: 50px; object-fit: contain; margin-bottom: 8px;"><br>
                                <span>Orange Money</span>
                            </div>
                        </label>
                        <label class="provider-item" onclick="selectProvider(this)">
                            <input type="radio" name="provider" value="moov">
                            <div class="provider-content">
                                <img src="../assets/images/paiement/moov logo.jfif" alt="Moov" style="width: 50px; height: 50px; object-fit: contain; margin-bottom: 8px;"><br>
                                <span>Moov Money</span>
                            </div>
                        </label>
                        <label class="provider-item" onclick="selectProvider(this)">
                            <input type="radio" name="provider" value="mtn">
                            <div class="provider-content">
                                <img src="../assets/images/paiement/mtn logo.jfif" alt="MTN" style="width: 50px; height: 50px; object-fit: contain; margin-bottom: 8px;"><br>
                                <span>MTN MoMo</span>
                            </div>
                        </label>
                    </div>

                    <div class="form-group mt-4">
                        <label for="phone">Numéro de téléphone</label>
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="07 00 00 00 00" required>
                    </div>

                    <input type="hidden" name="step" value="process_details">
                    <button type="submit" class="btn btn-primary btn-block mt-4">Continuer vers le paiement</button>
                    <p class="text-center mt-3"><a href="paiement.php?id=<?= $id ?>" style="color: var(--secondary); font-size: 0.8rem;">Retour</a></p>
                </form>

                <script>
                    function selectProvider(el) {
                        document.querySelectorAll('.provider-item').forEach(p => p.classList.remove('selected'));
                        el.classList.add('selected');
                    }
                </script>

            <?php elseif ($step === 'otp'): ?>
                <form action="paiement.php?id=<?= $id ?>" method="POST" style="max-width: 400px; margin: 0 auto; text-align: center;">
                    <div style="background: #f0f7ff; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                        <i class="fas fa-shield-alt" style="font-size: 2rem; color: var(--info); margin-bottom: 10px;"></i>
                        <h3>Vérification OTP</h3>
                        <p style="font-size: 0.85rem; color: var(--secondary);">Un code a été envoyé au numéro saisi. Veuillez l'entrer ci-dessous.</p>
                    </div>
                    <div class="form-group">
                        <label for="otp" style="font-weight: 700;">Code de confirmation (Test: 1212)</label>
                        <input type="text" name="otp" id="otp" class="form-control text-center" style="font-size: 1.8rem; letter-spacing: 15px; font-weight: 800;" maxlength="4" required autofocus>
                    </div>
                    <input type="hidden" name="step" value="verify_otp">
                    <button type="submit" class="btn btn-primary btn-block mt-4">Confirmer le débit</button>
                    <p class="mt-4"><a href="paiement.php?id=<?= $id ?>" style="color: var(--secondary); font-size: 0.8rem;">Annuler</a></p>
                </form>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
