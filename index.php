<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = "Accueil";
include 'includes/header.php';

// Récupérer quelques véhicules en vedette
$stmt = $pdo->query("SELECT * FROM vehicules WHERE statut = 'disponible' AND is_deleted = 0 LIMIT 4");
$featuredVehicles = $stmt->fetchAll();
?>

<section class="hero container" style="padding: 80px 0;">
    <div class="hero-content fade-in">
        <div style="display: inline-block; padding: 8px 16px; background: rgba(0,0,0,0.05); border-radius: 50px; font-size: 0.8rem; font-weight: 700; margin-bottom: 24px; color: var(--primary); text-transform: uppercase; letter-spacing: 1px;">✨ Redéfinissez votre mobilité</div>
        <h1 style="font-size: 3.5rem; margin-bottom: 20px;">Louez une voiture en toute simplicité</h1>
        <p style="font-size: 1.2rem; margin-bottom: 32px;">La meilleure plateforme d'autopartage rapide, sécurisée et économique. Profitez de nos tarifs compétitifs et de notre large gamme de véhicules.</p>
        <div class="hero-actions">
            <a href="client/vehicles.php" class="btn btn-primary btn-lg" style="box-shadow: 0 10px 20px rgba(0,0,0,0.1);">Réserver maintenant</a>
            <a href="client/vehicles.php" class="btn btn-outline btn-lg">Voir les véhicules</a>
        </div>
        <div class="hero-stats mt-4 flex gap-4" style="margin-top: 48px;">
            <div class="flex items-center gap-1">
                <span style="font-weight: 800; font-size: 1.4rem;">2k+</span>
                <span style="font-size: 0.85rem; color: var(--secondary); text-transform: uppercase;">Membres</span>
            </div>
            <div style="width: 1px; height: 35px; background: #ddd;"></div>
            <div class="flex items-center gap-1">
                <span style="font-weight: 800; font-size: 1.4rem;">50+</span>
                <span style="font-size: 0.85rem; color: var(--secondary); text-transform: uppercase;">Véhicules</span>
            </div>
            <div style="width: 1px; height: 35px; background: #ddd;"></div>
            <div class="flex items-center gap-1">
                <span style="font-weight: 800; font-size: 1.4rem;">4.9/5</span>
                <span style="font-size: 0.85rem; color: var(--secondary); text-transform: uppercase;">Avis</span>
            </div>
        </div>
    </div>
    <div class="hero-image scale-up">
        <div style="position: relative; padding: 20px;">
            <div style="position: absolute; top: 0; right: 0; width: 80%; height: 100%; background: var(--bg-alt); border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; z-index: -1;"></div>
            <img src="assets/images/hero-car.png" alt="Car sharing hero" style="box-shadow: 0 30px 60px rgba(0,0,0,0.2); border-radius: 20px;">
            <div style="position: absolute; bottom: 40px; left: -20px; background: #fff; padding: 18px 28px; border-radius: 18px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); border: 1px solid var(--border);" class="hover-lift">
                <div class="flex gap-3">
                    <div class="icon" style="background: var(--success); color: #fff; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;"><i class="fas fa-check" style="margin:0"></i></div>
                    <div>
                        <div style="font-weight: 800; font-size: 0.95rem;">Disponibilité 24/7</div>
                        <div style="font-size: 0.8rem; color: var(--secondary);">Réservation en 2 minutes</div>
                    </div>
                </div>
            </div>
        </div>
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

<section class="section" id="how-it-works" style="background: #111; color: #fff; padding: 100px 0;">
    <div class="container">
        <div class="section-title text-center" style="display: block; margin-bottom: 60px;">
            <h2 class="mb-4" style="color: #fff; font-size: 2.5rem; font-weight: 800;">Pourquoi choisir AutoPartage ?</h2>
            <p style="color: #888; max-width: 600px; margin: 0 auto; font-size: 1.1rem;">Une expérience de mobilité repensée pour être plus fluide, plus sûre et plus intelligente.</p>
        </div>
        <div class="features grid-4">
            <div class="feature-item hover-lift reveal" style="background: rgba(255,255,255,0.03); border-radius: 24px; border: 1px solid rgba(255,255,255,0.08); padding: 40px 30px;">
                <div class="feature-icon" style="background: #fff; color: #111; width: 60px; height: 60px; font-size: 1.5rem;"><i class="fas fa-bolt"></i></div>
                <h3 style="color: #fff; font-size: 1.2rem; margin-bottom: 12px;">Ultra Rapide</h3>
                <p style="color: #888; font-size: 0.9rem; line-height: 1.6;">Inscrivez-vous et réservez votre véhicule en moins de 5 minutes chrono.</p>
            </div>
            <div class="feature-item hover-lift reveal" style="background: rgba(255,255,255,0.03); border-radius: 24px; border: 1px solid rgba(255,255,255,0.08); padding: 40px 30px;">
                <div class="feature-icon" style="background: #fff; color: #111; width: 60px; height: 60px; font-size: 1.5rem;"><i class="fas fa-shield-alt"></i></div>
                <h3 style="color: #fff; font-size: 1.2rem; margin-bottom: 12px;">100% Sécurisé</h3>
                <p style="color: #888; font-size: 0.9rem; line-height: 1.6;">Paiements cryptés et assurance premium incluse pour chaque trajet.</p>
            </div>
            <div class="feature-item hover-lift reveal" style="background: rgba(255,255,255,0.03); border-radius: 24px; border: 1px solid rgba(255,255,255,0.08); padding: 40px 30px;">
                <div class="feature-icon" style="background: #fff; color: #111; width: 60px; height: 60px; font-size: 1.5rem;"><i class="fas fa-sync-alt"></i></div>
                <h3 style="color: #fff; font-size: 1.2rem; margin-bottom: 12px;">Flexibilité Totale</h3>
                <p style="color: #888; font-size: 0.9rem; line-height: 1.6;">Annulez ou modifiez votre réservation sans frais jusqu'à 2h avant.</p>
            </div>
            <div class="feature-item hover-lift reveal" style="background: rgba(255,255,255,0.03); border-radius: 24px; border: 1px solid rgba(255,255,255,0.08); padding: 40px 30px;">
                <div class="feature-icon" style="background: #fff; color: #111; width: 60px; height: 60px; font-size: 1.5rem;"><i class="fas fa-tags"></i></div>
                <h3 style="color: #fff; font-size: 1.2rem; margin-bottom: 12px;">Prix Juste</h3>
                <p style="color: #888; font-size: 0.9rem; line-height: 1.6;">Aucun frais caché. Vous payez ce que vous voyez, au meilleur tarif.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" id="about" style="background: linear-gradient(135deg, #fff 0%, #f9f9f9 100%);">
    <div class="container">
        <div class="about-card fade-in" style="background: #fff; padding: 60px 40px; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.05); border: 1px solid var(--border); text-align: center;">
            <div style="display: inline-block; padding: 12px 24px; background: #f0f0f0; border-radius: 100px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 24px; color: var(--secondary);">Notre Histoire & Vision</div>
            <h2 style="font-size: 2.5rem; margin-bottom: 24px; font-weight: 800;">Révolutionner la Mobilité Urbaine</h2>
            <div style="max-width: 800px; margin: 0 auto;">
                <p style="font-size: 1.1rem; color: var(--secondary); margin-bottom: 32px; line-height: 1.8;">
                    AutoPartage est né de la volonté de simplifier la mobilité urbaine. Nous croyons que la possession d'un véhicule ne devrait pas être un frein à votre liberté de mouvement.
                </p>
                <div class="grid-3" style="margin: 40px 0; border-top: 1px solid #eee; border-bottom: 1px solid #eee; padding: 40px 0;">
                    <div>
                        <h4 style="font-size: 2.2rem; font-weight: 800; color: var(--primary); margin-bottom: 5px;">500k+</h4>
                        <p style="font-size: 0.85rem; color: var(--secondary); text-transform: uppercase; letter-spacing: 1px;">Kilomètres parcourus</p>
                    </div>
                    <div>
                        <h4 style="font-size: 2.2rem; font-weight: 800; color: var(--primary); margin-bottom: 5px;">98%</h4>
                        <p style="font-size: 0.85rem; color: var(--secondary); text-transform: uppercase; letter-spacing: 1px;">Satisfaction Client</p>
                    </div>
                    <div>
                        <h4 style="font-size: 2.2rem; font-weight: 800; color: var(--primary); margin-bottom: 5px;">15min</h4>
                        <p style="font-size: 0.85rem; color: var(--secondary); text-transform: uppercase; letter-spacing: 1px;">Temps de réservation</p>
                    </div>
                </div>
                <p style="font-size: 1.1rem; color: var(--secondary); margin-bottom: 40px; line-height: 1.8;">
                    Notre mission est de fournir un accès facile, abordable et durable à une flotte de véhicules modernes pour tous vos besoins, qu'il s'agisse d'un week-end à la campagne ou d'un rendez-vous d'affaires en ville.
                </p>
                <div class="flex-center" style="display: flex; justify-content: center; gap: 20px;">
                    <a href="auth/register.php" class="btn btn-primary btn-lg">Rejoindre l'aventure</a>
                    <a href="client/vehicles.php" class="btn btn-outline btn-lg">Découvrir la flotte</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
