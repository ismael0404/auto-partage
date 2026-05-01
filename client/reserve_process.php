<?php
// client/reserve_process.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole('client');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /Projet_Auto/client/vehicles.php');
    exit();
}

$vehicle_id = filter_input(INPUT_POST, 'vehicle_id', FILTER_VALIDATE_INT);
$date_debut = $_POST['date_debut'] ?? '';
$date_fin = $_POST['date_fin'] ?? '';

if (!$vehicle_id || empty($date_debut) || empty($date_fin)) {
    setFlashMessage("Toutes les informations sont requises.", "error");
    header("Location: /Projet_Auto/client/reserve.php?id=" . $vehicle_id);
    exit();
}

$start = new DateTime($date_debut);
$end = new DateTime($date_fin);
$now = new DateTime();

if ($start < $now) {
    setFlashMessage("La date de dÃ©but ne peut pas Ãªtre dans le passÃ©.", "error");
    header("Location: /Projet_Auto/client/reserve.php?id=" . $vehicle_id);
    exit();
}

if ($end <= $start) {
    setFlashMessage("La date de fin doit Ãªtre ultÃ©rieure Ã  la date de dÃ©but.", "error");
    header("Location: /Projet_Auto/client/reserve.php?id=" . $vehicle_id);
    exit();
}

// RÃ©cupÃ©rer le vÃ©hicule et vÃ©rifier son statut
$stmt = $pdo->prepare("SELECT prix_jour, statut FROM vehicles WHERE id = ? AND deleted_at IS NULL");
$stmt->execute([$vehicle_id]);
$vehicle = $stmt->fetch();

if (!$vehicle || $vehicle['statut'] === 'maintenance') {
    setFlashMessage("Ce vÃ©hicule n'est pas disponible.", "error");
    header("Location: /Projet_Auto/client/vehicles.php");
    exit();
}

// VÃ©rification des conflits (chevauchement)
$stmtConflict = $pdo->prepare("
    SELECT id FROM reservations 
    WHERE vehicle_id = ? 
    AND statut NOT IN ('annulee', 'terminee')
    AND (
        (date_debut <= ? AND date_fin >= ?) OR
        (date_debut <= ? AND date_fin >= ?) OR
        (date_debut >= ? AND date_fin <= ?)
    )
");
$stmtConflict->execute([$vehicle_id, $date_debut, $date_debut, $date_fin, $date_fin, $date_debut, $date_fin]);
if ($stmtConflict->fetch()) {
    setFlashMessage("Ce vÃ©hicule est dÃ©jÃ  rÃ©servÃ© pour tout ou partie de cette pÃ©riode.", "error");
    header("Location: /Projet_Auto/client/reserve.php?id=" . $vehicle_id);
    exit();
}

// Calcul du prix total
$interval = $start->diff($end);
$days = $interval->days;
if ($interval->h > 0 || $interval->i > 0) {
    $days++; // Toute journÃ©e entamÃ©e est due
}
$prix_total = $days * $vehicle['prix_jour'];

try {
    $pdo->beginTransaction();
    
    // Insertion de la rÃ©servation
    $stmt = $pdo->prepare("INSERT INTO reservations (user_id, vehicle_id, date_debut, date_fin, prix_total, statut) VALUES (?, ?, ?, ?, ?, 'en_attente')");
    $stmt->execute([$_SESSION['user_id'], $vehicle_id, $date_debut, $date_fin, $prix_total]);
    
    // On ne change pas le statut du vÃ©hicule tout de suite, on attend la confirmation de l'admin
    // (Mais si on veut le bloquer directement on pourrait le faire ici. La consigne dit: "rÃ©servation confirmÃ©e â†’ vÃ©hicule = rÃ©servÃ©")
    
    $pdo->commit();
    setFlashMessage("Votre demande de rÃ©servation a Ã©tÃ© enregistrÃ©e. Elle est en attente de confirmation.", "success");
    header("Location: /Projet_Auto/client/reservations.php");
    exit();
} catch (Exception $e) {
    $pdo->rollBack();
    setFlashMessage("Une erreur est survenue lors de la rÃ©servation.", "error");
    header("Location: /Projet_Auto/client/reserve.php?id=" . $vehicle_id);
    exit();
}

