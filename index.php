<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = "Accueil";
include 'includes/header.php';

// Récupérer quelques véhicules en vedette
$stmt = $pdo->query("SELECT * FROM vehicules WHERE statut = 'disponible' AND is_deleted = 0 LIMIT 4");
$featuredVehicles = $stmt->fetchAll();
?>

<section class="hero container">
    <div class="hero-content">
        <h1>Louez une voiture en toute simplicité</h1>
        <p>La meilleure plateforme d'autopartage rapide, sécurisée et économique. Profitez de nos tarifs compétitifs et de notre large gamme de véhicules.</p>
        <div class="hero-actions">
            <a href="client/vehicles.php" class="btn btn-primary btn-lg">Réserver maintenant</a>
            <a href="client/vehicles.php" class="btn btn-outline btn-lg">Voir les véhicules</a>
        </div>
    </div>
    <div class="hero-image">
        <img src="assets/images/vehicules/image voiture.jfif" alt="Car sharing hero">
    </div>
</section>

<section class="section container">
    <div class="section-title">
        <h2>Nos véhicules en vedette</h2>
        <a href="client/vehicles.php">Voir tout &rarr;</a>
    </div>
    
    <div class="grid-4">
        <?php foreach ($featuredVehicles as $vehicle): ?>
        <div class="vehicle-card">
            <img src="<?= getVehiculeImage($vehicle['image']) ?>" alt="<?= clean($vehicle['marque'] . ' ' . $vehicle['modele']) ?>" class="vehicle-card-img">
            <div class="vehicle-card-body">
                <p class="type"><?= clean($vehicle['type_carburant']) ?></p>
                <h3><?= clean($vehicle['marque'] . ' ' . $vehicle['modele']) ?></h3>
                <p class="price"><?= formatPrix($vehicle['prix_jour']) ?> <span>/ jour</span></p>
                <a href="client/vehicle_detail.php?id=<?= $vehicle['id'] ?>" class="btn btn-primary btn-sm btn-block mt-2">Détails</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="section bg-alt" id="how-it-works" style="background-color: var(--bg-alt);">
    <div class="container">
        <div class="section-title text-center" style="display: block;">
            <h2 class="mb-4">Pourquoi choisir AutoPartage ?</h2>
        </div>
        <div class="features">
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-bolt"></i></div>
                <h3>Rapide</h3>
                <p>Réservation en quelques clics seulement.</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                <h3>Sécurisé</h3>
                <p>Paiements sécurisés et véhicules assurés.</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-sync-alt"></i></div>
                <h3>Flexible</h3>
                <p>Annulation gratuite et modification flexible.</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-tags"></i></div>
                <h3>Économique</h3>
                <p>Les meilleurs prix du marché garantis.</p>
            </div>
        </div>
    </div>
</section>

<section class="section container" id="about">
    <div class="grid-2" style="align-items: center;">
        <div>
            <img src="https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?q=80&w=1000&auto=format&fit=crop" alt="About us" style="border-radius: var(--radius);">
        </div>
        <div>
            <h2 class="mb-3">À propos de nous</h2>
            <p class="mb-3">AutoPartage est né de la volonté de simplifier la mobilité urbaine. Nous croyons que la possession d'un véhicule ne devrait pas être un frein à votre liberté de mouvement.</p>
            <p class="mb-3">Notre mission est de fournir un accès facile, abordable et durable à une flotte de véhicules modernes pour tous vos besoins, qu'il s'agisse d'un week-end à la campagne ou d'un rendez-vous d'affaires en ville.</p>
            <a href="auth/register.php" class="btn btn-primary">Rejoignez-nous</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
