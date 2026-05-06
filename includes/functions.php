<?php
/**
 * AutoPartage - Fonctions utilitaires
 */

/**
 * Nettoyer les entrées utilisateur (protection XSS)
 */
function clean($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Rediriger vers une URL
 */
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit;
}

/**
 * Vérifier si l'utilisateur est connecté
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Vérifier si l'utilisateur est admin
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Vérifier si l'utilisateur est client
 */
function isClient() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'client';
}

/**
 * Protéger une page (rediriger si non connecté)
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Veuillez vous connecter pour accéder à cette page.'];
        redirect('/auth/login.php');
    }
}

/**
 * Protéger une page admin
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Accès refusé. Vous n\'avez pas les droits administrateur.'];
        redirect('/index.php');
    }
}

/**
 * Protéger une page client
 */
function requireClient() {
    requireLogin();
    if (!isClient()) {
        redirect('/admin/dashboard.php');
    }
}

/**
 * Définir un message flash
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Afficher et consommer un message flash
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Formater un prix en FCFA
 */
function formatPrix($prix) {
    return number_format($prix, 0, ',', ' ') . ' FCFA';
}

/**
 * Formater une date
 */
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

/**
 * Formater une date avec heure
 */
function formatDateTime($date) {
    return date('d/m/Y H:i', strtotime($date));
}

/**
 * Calculer la durée en jours entre deux dates
 */
function calculerDuree($dateDebut, $dateFin) {
    $d1 = new DateTime($dateDebut);
    $d2 = new DateTime($dateFin);
    $diff = $d1->diff($d2);
    return max(1, $diff->days);
}

/**
 * Vérifier la disponibilité d'un véhicule
 */
function vehiculeDisponible($pdo, $vehiculeId, $dateDebut, $dateFin, $excludeReservationId = null) {
    $sql = "SELECT COUNT(*) FROM reservations 
            WHERE vehicule_id = :vid 
            AND statut IN ('en_attente', 'confirmee')
            AND NOT (date_fin <= :debut OR date_debut >= :fin)";
    
    if ($excludeReservationId) {
        $sql .= " AND id != :rid";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':vid', $vehiculeId, PDO::PARAM_INT);
    $stmt->bindParam(':debut', $dateDebut);
    $stmt->bindParam(':fin', $dateFin);
    
    if ($excludeReservationId) {
        $stmt->bindParam(':rid', $excludeReservationId, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    return $stmt->fetchColumn() == 0;
}

/**
 * Obtenir le badge de statut pour les réservations
 */
function getStatutBadge($statut) {
    $badges = [
        'en_attente' => '<span class="badge badge-warning">En attente</span>',
        'confirmee'  => '<span class="badge badge-success">Confirmée</span>',
        'annulee'    => '<span class="badge badge-danger">Annulée</span>',
        'terminee'   => '<span class="badge badge-info">Terminée</span>'
    ];
    return $badges[$statut] ?? '<span class="badge">' . $statut . '</span>';
}

/**
 * Obtenir le badge de statut pour les véhicules
 */
function getVehiculeStatutBadge($statut) {
    $badges = [
        'disponible'  => '<span class="badge badge-success">Disponible</span>',
        'reserve'     => '<span class="badge badge-warning">Réservé</span>',
        'maintenance' => '<span class="badge badge-danger">Maintenance</span>'
    ];
    return $badges[$statut] ?? '<span class="badge">' . $statut . '</span>';
}

/**
 * Compter les messages non lus
 */
function countUnreadMessages($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE utilisateur_id = :uid AND lu = 0");
    $stmt->execute([':uid' => $userId]);
    return $stmt->fetchColumn();
}

/**
 * Compter les messages de chat non lus
 */
function countUnreadChatMessages($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM chat_messages WHERE destinataire_id = :uid AND lu = 0");
    $stmt->execute([':uid' => $userId]);
    return $stmt->fetchColumn();
}

/**
 * Obtenir l'image du véhicule ou une image par défaut
 */
function getVehiculeImage($image) {
    $path = $_SERVER['DOCUMENT_ROOT'] . BASE_URL . '/assets/images/vehicules/' . $image;
    if ($image && file_exists($path)) {
        return BASE_URL . '/assets/images/vehicules/' . $image;
    }
    return 'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=400&h=300&fit=crop';
}

/**
 * Générer un token CSRF
 */
function generateCSRF() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifier un token CSRF
 */
function verifyCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
