<?php
/**
 * Sidebar pour le tableau de bord (Client ou Admin)
 */
$base = BASE_URL;
$role = $_SESSION['user_role'] ?? 'client';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <a href="<?= $base ?>/index.php" class="logo">
        <span class="icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
        </span> AutoPartage
    </a>
    
    <nav class="sidebar-nav">
        <?php if ($role === 'admin'): ?>
            <a href="<?= $base ?>/admin/dashboard.php" class="<?= $current_page === 'dashboard.php' ? 'active' : '' ?>">
                <span class="icon"><i class="fas fa-chart-line"></i></span> <span>Tableau de bord</span>
            </a>
            <a href="<?= $base ?>/admin/clients.php" class="<?= $current_page === 'clients.php' ? 'active' : '' ?>">
                <span class="icon"><i class="fas fa-user-friends"></i></span> <span>Clients</span>
            </a>
            <a href="<?= $base ?>/admin/vehicles.php" class="<?= $current_page === 'vehicles.php' ? 'active' : '' ?>">
                <span class="icon"><i class="fas fa-car"></i></span> <span>Véhicules</span>
            </a>
            <a href="<?= $base ?>/admin/reservations.php" class="<?= $current_page === 'reservations.php' ? 'active' : '' ?>">
                <span class="icon"><i class="fas fa-calendar-check"></i></span> <span>Réservations</span>
            </a>
            <a href="<?= $base ?>/admin/notifications.php" class="<?= $current_page === 'notifications.php' ? 'active' : '' ?>">
                <span class="icon"><i class="fas fa-bell"></i></span> <span>Notifications</span>
            </a>
            <a href="<?= $base ?>/admin/messages.php" class="<?= $current_page === 'messages.php' ? 'active' : '' ?>">
                <span class="icon"><i class="fas fa-comment-dots"></i></span> <span>Messagerie</span>
            </a>
            <a href="<?= $base ?>/admin/settings.php" class="<?= $current_page === 'settings.php' ? 'active' : '' ?>">
                <span class="icon"><i class="fas fa-cog"></i></span> <span>Paramètres</span>
            </a>
        <?php else: ?>
            <a href="<?= $base ?>/client/dashboard.php" class="<?= $current_page === 'dashboard.php' ? 'active' : '' ?>">
                <span class="icon"><i class="fas fa-chart-line"></i></span> <span>Tableau de bord</span>
            </a>
            <a href="<?= $base ?>/client/profile.php" class="<?= $current_page === 'profile.php' ? 'active' : '' ?>">
                <span class="icon"><i class="fas fa-user"></i></span> <span>Mon profil</span>
            </a>
            <a href="<?= $base ?>/client/vehicles.php" class="<?= $current_page === 'vehicles.php' ? 'active' : '' ?>">
                <span class="icon"><i class="fas fa-car"></i></span> <span>Véhicules</span>
            </a>
            <a href="<?= $base ?>/client/reservations.php" class="<?= $current_page === 'reservations.php' ? 'active' : '' ?>">
                <span class="icon"><i class="fas fa-calendar-check"></i></span> <span>Mes réservations</span>
            </a>
            <a href="<?= $base ?>/client/notifications.php" class="<?= $current_page === 'notifications.php' ? 'active' : '' ?>">
                <span class="icon"><i class="fas fa-bell"></i></span> <span>Notifications</span>
            </a>
            <a href="<?= $base ?>/client/messages.php" class="<?= $current_page === 'messages.php' ? 'active' : '' ?>">
                <span class="icon"><i class="fas fa-comment-dots"></i></span> <span>Messagerie</span>
            </a>
        <?php endif; ?>
        
        <div style="margin-top: auto; padding-top: 20px; border-top: 1px solid var(--border);">
            <a href="<?= $base ?>/auth/logout.php" style="color: var(--danger);">
                <span class="icon"><i class="fas fa-sign-out-alt"></i></span> <span>Déconnexion</span>
            </a>
        </div>
    </nav>
</aside>
