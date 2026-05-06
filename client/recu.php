<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireClient();

$userId = $_SESSION['user_id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT r.*, v.marque, v.modele, u.prenom, u.nom 
                       FROM reservations r 
                       JOIN vehicules v ON r.vehicule_id = v.id 
                       JOIN utilisateurs u ON r.utilisateur_id = u.id 
                       WHERE r.id = :id AND r.utilisateur_id = :uid");
$stmt->execute([':id' => $id, ':uid' => $userId]);
$res = $stmt->fetch();

if (!$res || !$res['mode_paiement']) {
    setFlash('error', "Reçu non disponible.");
    redirect('/client/reservations.php');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu #<?= $res['id'] ?> - AutoPartage</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f5f5f5; padding: 40px; color: #111; }
        .receipt { max-width: 600px; margin: 0 auto; background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #111; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-weight: 800; font-size: 1.5rem; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .label { font-size: 0.8rem; color: #666; text-transform: uppercase; margin-bottom: 4px; }
        .value { font-weight: 600; font-size: 1rem; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .table th { text-align: left; padding: 12px 0; border-bottom: 1px solid #eee; font-size: 0.85rem; color: #666; }
        .table td { padding: 12px 0; border-bottom: 1px solid #eee; }
        .total-row { display: flex; justify-content: flex-end; gap: 20px; font-size: 1.2rem; font-weight: 700; margin-top: 20px; }
        .payment-status { text-align: center; padding: 15px; border-radius: 6px; margin-top: 40px; font-weight: 700; text-transform: uppercase; }
        .status-paid { background: #dcfce7; color: #166534; }
        .status-onplace { background: #fef3c7; color: #92400e; }
        .no-print { text-align: center; margin-top: 30px; }
        .btn { padding: 10px 24px; border-radius: 6px; font-weight: 600; text-decoration: none; cursor: pointer; border: none; }
        .btn-print { background: #111; color: #fff; }
        @media print { .no-print { display: none; } body { padding: 0; background: #fff; } .receipt { box-shadow: none; border: 1px solid #eee; } }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div class="logo">AutoPartage</div>
            <div style="text-align: right;">
                <div class="value">Reçu #<?= $res['id'] ?></div>
                <div class="label"><?= date('d/m/Y') ?></div>
            </div>
        </div>

        <div class="info-grid">
            <div>
                <div class="label">Client</div>
                <div class="value"><?= clean($res['prenom'] . ' ' . $res['nom']) ?></div>
            </div>
            <div>
                <div class="label">Véhicule</div>
                <div class="value"><?= clean($res['marque'] . ' ' . $res['modele']) ?></div>
            </div>
            <div>
                <div class="label">Date début</div>
                <div class="value"><?= formatDateTime($res['date_debut']) ?></div>
            </div>
            <div>
                <div class="label">Date fin</div>
                <div class="value"><?= formatDateTime($res['date_fin']) ?></div>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Montant</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Location de véhicule (<?= $res['duree_jours'] ?> jours x <?= formatPrix($res['prix_unitaire']) ?>)</td>
                    <td style="text-align: right;"><?= formatPrix($res['prix_total']) ?></td>
                </tr>
            </tbody>
        </table>

        <div class="total-row">
            <span>TOTAL</span>
            <span><?= formatPrix($res['prix_total']) ?></span>
        </div>

        <?php if ($res['mode_paiement'] === 'sur_place'): ?>
            <div class="payment-status status-onplace">Paiement sur place</div>
        <?php else: ?>
            <div class="payment-status status-paid">Payé en ligne</div>
        <?php endif; ?>
    </div>

    <div class="no-print">
        <button onclick="downloadPDF()" class="btn btn-print"><i class="fas fa-download"></i> Télécharger le Reçu</button>
        <a href="reservations.php" style="margin-left: 20px; color: #666; font-size: 0.9rem;">Retour</a>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            const element = document.querySelector('.receipt');
            const opt = {
                margin:       0.5,
                filename:     'Recu_AutoPartage_<?= $res['id'] ?>.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>
