<?php
// includes/navbar.php
?>
<nav class="navbar">
    <div class="container">
        <a href="/Projet_Auto/" class="navbar-brand">
            <i class="fa-solid fa-car"></i> AutoPartage
        </a>
        <ul class="navbar-nav align-items-center">
            <li><a href="/Projet_Auto/" class="nav-link">Accueil</a></li>
            <li><a href="/Projet_Auto/client/vehicles.php" class="nav-link">Véhicules</a></li>
            <li><a href="#" class="nav-link">Comment ça marche</a></li>
            <li><a href="#" class="nav-link">A propos</a></li>
            
            <?php if (isLoggedIn()): ?>
                <?php if (hasRole('admin')): ?>
                    <li><a href="/Projet_Auto/admin/dashboard.php" class="nav-link">Dashboard Admin</a></li>
                <?php else: ?>
                    <li><a href="/Projet_Auto/client/dashboard.php" class="nav-link">Mon Espace</a></li>
                <?php endif; ?>
                <li>
                    <a href="/Projet_Auto/auth/logout.php" class="btn btn-outline" style="padding: 0.25rem 0.75rem;">
                        <i class="fa-solid fa-sign-out-alt"></i>
                    </a>
                </li>
            <?php else: ?>
                <li><a href="/Projet_Auto/auth/login.php" class="nav-link">Se connecter</a></li>
                <li><a href="/Projet_Auto/auth/register.php" class="btn btn-primary">S'inscrire</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

