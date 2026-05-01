<?php
// includes/functions.php

/**
 * DÃ©finit un message flash
 */
function setFlashMessage($message, $type = 'info') {
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type // success, error, warning, info
    ];
}

/**
 * Affiche le message flash s'il existe
 */
function displayFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        echo '<div class="alert alert-' . htmlspecialchars($flash['type']) . '">' . htmlspecialchars($flash['message']) . '</div>';
        unset($_SESSION['flash']);
    }
}

/**
 * Upload d\'une image de maniÃ¨re sÃ©curisÃ©e
 * Retourne le nom du fichier gÃ©nÃ©rÃ© ou false en cas d\'erreur
 */
function uploadImage($fileArray, $destinationFolder) {
    if ($fileArray['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $maxSize = 2 * 1024 * 1024; // 2 MB

    if ($fileArray['size'] > $maxSize) {
        setFlashMessage("L'image ne doit pas dÃ©passer 2Mo.", 'error');
        return false;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $fileArray['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedMimeTypes)) {
        setFlashMessage("Seuls les formats JPG, PNG et WEBP sont autorisÃ©s.", 'error');
        return false;
    }

    $extension = pathinfo($fileArray['name'], PATHINFO_EXTENSION);
    $newName = uniqid('veh_', true) . '.' . $extension;
    $destination = $destinationFolder . '/' . $newName;

    if (move_uploaded_file($fileArray['tmp_name'], $destination)) {
        return $newName;
    }

    return false;
}

/**
 * SÃ©curisation des donnÃ©es entrantes (XSS)
 */
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Formatage d'un prix
 */
function formatPrice($price) {
    return number_format($price, 0, ',', ' ') . ' FCFA';
}

