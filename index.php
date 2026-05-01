<?php
// index.php
require_once __DIR__ . '/includes/header.php';

// Récupérer les véhicules en vedette (3 véhicules disponibles)
$stmt = $pdo->query("SELECT * FROM vehicles WHERE deleted_at IS NULL AND statut != 'maintenance' ORDER BY created_at DESC LIMIT 4");
$vedettes = $stmt->fetchAll();
?>

<!-- Hero Section -->
<div style="background-color: var(--surface-color); padding: 5rem 0 7rem; position: relative;">
    <div class="container d-flex align-items-center justify-content-between" style="gap: 2rem;">
        <div style="flex: 1; max-width: 550px;">
            <h1 style="font-size: 3.5rem; margin-bottom: 1.5rem; letter-spacing: -0.02em; font-weight: 800;">Louez une voiture<br>en toute simplicité</h1>
            <p style="font-size: 1.15rem; color: var(--gray-500); margin-bottom: 2.5rem; font-weight: 400; line-height: 1.6;">La meilleure plateforme d'autopartage.<br>Rapide, sécurisée et économique.</p>
            <div class="d-flex" style="gap: 1rem;">
                <a href="/Projet_Auto/client/vehicles.php" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1.125rem;">Réserver maintenant</a>
                <a href="/Projet_Auto/client/vehicles.php" class="btn btn-outline" style="padding: 1rem 2rem; font-size: 1.125rem;">Voir les véhicules</a>
            </div>
        </div>
        <div style="flex: 1.2; text-align: right;">
            <img src="/Projet_Auto/assets/images/image voiture.jfif" alt="Voiture de location" style="max-width: 100%; border-radius: var(--border-radius); box-shadow: var(--shadow-lg);">
        </div>
    </div>
</div>

<!-- Véhicules en vedette -->
<div class="container" style="padding: 5rem 0;">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h2 style="font-size: 2rem; letter-spacing: -0.02em;">Nos véhicules en vedette</h2>
        <a href="/Projet_Auto/client/vehicles.php" style="color: var(--gray-500); font-weight: 500; font-size: 0.95rem;">Voir tout</a>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem;">
        <?php foreach($vedettes as $v): ?>
        <div class="card" style="border-radius: var(--border-radius-btn);">
            <div style="height: 160px; background-color: var(--gray-50); display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 1rem;">
                <?php if($v['image']): ?>
                    <img src="/Projet_Auto/assets/images/<?php echo htmlspecialchars($v['image']); ?>" alt="Voiture" style="width: 100%; height: 100%; object-fit: contain;">
                <?php else: ?>
                    <i class="fa fa-car" style="font-size: 4rem; color: var(--gray-300);"></i>
                <?php endif; ?>
            </div>
            <div class="card-body" style="padding: 1rem;">
                <h3 style="font-size: 1.125rem; margin-bottom: 0.25rem; font-family: var(--font-primary); font-weight: 600; color: var(--primary-color);"><?php echo htmlspecialchars($v['marque'] . ' ' . $v['modele']); ?></h3>
                <p style="color: var(--gray-500); font-size: 0.8rem; margin-bottom: 1rem;">
                    <?php echo htmlspecialchars($v['carburant']); ?>
                </p>
                <div style="font-weight: 700; font-size: 1.125rem; color: var(--primary-color);">
                    <?php echo formatPrice($v['prix_jour']); ?> <span style="font-weight: 400; color: var(--gray-500); font-size: 0.8rem;">/ jour</span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Pourquoi nous choisir -->
<div class="container" style="padding: 5rem 0 7rem;">
    <h2 style="font-size: 2rem; margin-bottom: 3rem; letter-spacing: -0.02em;">Pourquoi choisir AutoPartage ?</h2>
    
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 2rem;">
        <div style="border: 1px solid var(--gray-200); padding: 2rem; border-radius: var(--border-radius-btn); background-color: var(--surface-color);">
            <i class="fa-solid fa-bolt" style="font-size: 1.5rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
            <h3 style="font-size: 1.125rem; margin-bottom: 0.5rem; font-family: var(--font-primary); font-weight: 600;">Rapide</h3>
            <p style="color: var(--gray-500); font-size: 0.85rem;">Réservation en quelques clics.</p>
        </div>
        <div style="border: 1px solid var(--gray-200); padding: 2rem; border-radius: var(--border-radius-btn); background-color: var(--surface-color);">
            <i class="fa-solid fa-shield-halved" style="font-size: 1.5rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
            <h3 style="font-size: 1.125rem; margin-bottom: 0.5rem; font-family: var(--font-primary); font-weight: 600;">Sécurisé</h3>
            <p style="color: var(--gray-500); font-size: 0.85rem;">Paiements sécurisés et véhicules assurés.</p>
        </div>
        <div style="border: 1px solid var(--gray-200); padding: 2rem; border-radius: var(--border-radius-btn); background-color: var(--surface-color);">
            <i class="fa-solid fa-route" style="font-size: 1.5rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
            <h3 style="font-size: 1.125rem; margin-bottom: 0.5rem; font-family: var(--font-primary); font-weight: 600;">Flexible</h3>
            <p style="color: var(--gray-500); font-size: 0.85rem;">Annulation gratuite et dates flexibles.</p>
        </div>
        <div style="border: 1px solid var(--gray-200); padding: 2rem; border-radius: var(--border-radius-btn); background-color: var(--surface-color);">
            <i class="fa-solid fa-wallet" style="font-size: 1.5rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
            <h3 style="font-size: 1.125rem; margin-bottom: 0.5rem; font-family: var(--font-primary); font-weight: 600;">Économique</h3>
            <p style="color: var(--gray-500); font-size: 0.85rem;">Les meilleurs prix du marché sans frais cachés.</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
