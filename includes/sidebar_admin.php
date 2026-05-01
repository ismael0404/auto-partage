<?php
// includes/sidebar_admin.php
?>
<div style="width: 250px; background-color: var(--surface-color); border-right: 1px solid var(--gray-200); padding: 2rem 1rem; min-height: calc(100vh - 70px);">
    <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.5rem;">
        <li>
            <a href="/Projet_Auto/admin/dashboard.php" class="btn btn-block text-left" style="text-align: left; background-color: var(--gray-100); color: var(--gray-900);">
                <i class="fa-solid fa-chart-pie" style="width: 25px;"></i> Tableau de bord
            </a>
        </li>
        <li>
            <a href="/Projet_Auto/admin/clients.php" class="btn btn-block text-left btn-outline" style="text-align: left; border: none;">
                <i class="fa-solid fa-users" style="width: 25px;"></i> Clients
            </a>
        </li>
        <li>
            <a href="/Projet_Auto/admin/vehicles.php" class="btn btn-block text-left btn-outline" style="text-align: left; border: none;">
                <i class="fa-solid fa-car" style="width: 25px;"></i> VÃ©hicules
            </a>
        </li>
        <li>
            <a href="/Projet_Auto/admin/reservations.php" class="btn btn-block text-left btn-outline" style="text-align: left; border: none;">
                <i class="fa-solid fa-calendar-check" style="width: 25px;"></i> RÃ©servations
            </a>
        </li>
        <li style="margin-top: 2rem;">
            <a href="/Projet_Auto/auth/logout.php" class="btn btn-block text-left" style="text-align: left; color: var(--error);">
                <i class="fa-solid fa-sign-out-alt" style="width: 25px;"></i> DÃ©connexion
            </a>
        </li>
    </ul>
</div>

