<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

$error = '';
$success = '';

// Suppression (Soft Delete)
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $stmt = $pdo->prepare("UPDATE vehicules SET is_deleted = 1 WHERE id = :id");
    $stmt->execute([':id' => $deleteId]);
    setFlash('success', "Véhicule supprimé avec succès (archivé).");
    redirect('/admin/vehicles.php');
}

// Ajout / Modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marque = clean($_POST['marque']);
    $modele = clean($_POST['modele']);
    $annee = (int)$_POST['annee'];
    $type_carburant = $_POST['type_carburant'];
    $transmission = $_POST['transmission'];
    $nombre_places = (int)$_POST['nombre_places'];
    $prix_jour = (float)$_POST['prix_jour'];
    $statut = $_POST['statut'];
    $description = clean($_POST['description']);
    $caracteristiques = clean($_POST['caracteristiques']);

    // Gestion de l'upload d'images (Principale + 3 Supplémentaires)
    $images_data = [
        'image' => isset($_POST['old_image']) ? $_POST['old_image'] : '',
        'image2' => isset($_POST['old_image2']) ? $_POST['old_image2'] : '',
        'image3' => isset($_POST['old_image3']) ? $_POST['old_image3'] : '',
        'image4' => isset($_POST['old_image4']) ? $_POST['old_image4'] : '',
    ];

    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'jfif'];

    foreach (['image', 'image2', 'image3', 'image4'] as $img_key) {
        if (isset($_FILES[$img_key]) && $_FILES[$img_key]['error'] === 0) {
            $filename = $_FILES[$img_key]['name'];
            $filetype = pathinfo($filename, PATHINFO_EXTENSION);
            $filesize = $_FILES[$img_key]['size'];

            if (in_array(strtolower($filetype), $allowed) && $filesize <= 5 * 1024 * 1024) {
                $new_name = uniqid('veh_') . '_' . $img_key . '.' . $filetype;
                $upload_path = '../assets/images/vehicules/' . $new_name;
                if (move_uploaded_file($_FILES[$img_key]['tmp_name'], $upload_path)) {
                    $images_data[$img_key] = $new_name;
                }
            }
        }
    }

    if (!$error) {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            // Update
            $stmt = $pdo->prepare("UPDATE vehicules SET marque=:mq, modele=:md, annee=:an, type_carburant=:tc, transmission=:tr, nombre_places=:np, prix_jour=:pj, statut=:st, description=:ds, caracteristiques=:ct, image=:img, image2=:img2, image3=:img3, image4=:img4 WHERE id=:id");
            $stmt->execute([
                ':mq' => $marque, ':md' => $modele, ':an' => $annee, ':tc' => $type_carburant, ':tr' => $transmission, ':np' => $nombre_places, ':pj' => $prix_jour, ':st' => $statut, ':ds' => $description, ':ct' => $caracteristiques, 
                ':img' => $images_data['image'], ':img2' => $images_data['image2'], ':img3' => $images_data['image3'], ':img4' => $images_data['image4'],
                ':id' => $_POST['id']
            ]);
            setFlash('success', "Véhicule mis à jour avec succès.");
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO vehicules (marque, modele, annee, type_carburant, transmission, nombre_places, prix_jour, statut, description, caracteristiques, image, image2, image3, image4) VALUES (:mq, :md, :an, :tc, :tr, :np, :pj, :st, :ds, :ct, :img, :img2, :img3, :img4)");
            $stmt->execute([
                ':mq' => $marque, ':md' => $modele, ':an' => $annee, ':tc' => $type_carburant, ':tr' => $transmission, ':np' => $nombre_places, ':pj' => $prix_jour, ':st' => $statut, ':ds' => $description, ':ct' => $caracteristiques, 
                ':img' => $images_data['image'], ':img2' => $images_data['image2'], ':img3' => $images_data['image3'], ':img4' => $images_data['image4']
            ]);
            setFlash('success', "Véhicule ajouté avec succès.");
        }
        redirect('/admin/vehicles.php');
    }
}

$stmt = $pdo->query("SELECT * FROM vehicules WHERE is_deleted = 0 ORDER BY date_creation DESC");
$vehicles = $stmt->fetchAll();

$pageTitle = "Gestion des véhicules";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - AutoPartage</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="dashboard">
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <header class="dashboard-header">
            <h1>Gestion des véhicules</h1>
            <button class="btn btn-primary" onclick="openModal()">Ajouter un véhicule</button>
        </header>

        <?php $flash = getFlash(); if ($flash): ?>
            <div class="flash flash-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Véhicule</th>
                        <th>Type</th>
                        <th>Prix / Jour</th>
                        <th>Places</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vehicles as $v): ?>
                    <tr>
                        <td>
                            <div class="flex gap-1">
                                <img src="<?= getVehiculeImage($v['image']) ?>" alt="" style="width: 50px; height: 35px; object-fit: cover; border-radius: 4px;">
                                <strong><?= clean($v['marque'] . ' ' . $v['modele']) ?></strong>
                            </div>
                        </td>
                        <td><?= clean($v['type_carburant']) ?> / <?= clean($v['transmission']) ?></td>
                        <td><?= formatPrix($v['prix_jour']) ?></td>
                        <td><?= $v['nombre_places'] ?></td>
                        <td><?= getVehiculeStatutBadge($v['statut']) ?></td>
                        <td>
                            <div class="flex gap-1">
                                <button class="btn btn-outline btn-sm" onclick='editVehicle(<?= json_encode($v) ?>)'>Éditer</button>
                                <a href="vehicles.php?delete=<?= $v['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce véhicule ?')">Supprimer</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal Ajout/Modification -->
    <div id="vehicleModal" class="modal-overlay">
        <div class="modal" style="max-width: 900px; padding: 40px; border-radius: 20px;">
            <h3 id="modalTitle" style="font-size: 1.5rem; margin-bottom: 30px; border-bottom: 2px solid var(--bg-alt); padding-bottom: 15px;">Ajouter un véhicule</h3>
            <form action="vehicles.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="v_id">
                <input type="hidden" name="old_image" id="v_old_image">
                <input type="hidden" name="old_image2" id="v_old_image2">
                <input type="hidden" name="old_image3" id="v_old_image3">
                <input type="hidden" name="old_image4" id="v_old_image4">
                <div class="grid-2">
                    <div class="form-group">
                        <label for="marque">Marque</label>
                        <input type="text" name="marque" id="v_marque" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="modele">Modèle</label>
                        <input type="text" name="modele" id="v_modele" class="form-control" required>
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label for="image">Image principale</label>
                        <input type="file" name="image" id="v_image" class="form-control" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label for="image2">Image 2 (Supplé.)</label>
                        <input type="file" name="image2" id="v_image2" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label for="image3">Image 3 (Supplé.)</label>
                        <input type="file" name="image3" id="v_image3" class="form-control" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label for="image4">Image 4 (Supplé.)</label>
                        <input type="file" name="image4" id="v_image4" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="grid-3">
                    <div class="form-group">
                        <label for="annee">Année</label>
                        <input type="number" name="annee" id="v_annee" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="type_carburant">Carburant</label>
                        <select name="type_carburant" id="v_carburant" class="form-control">
                            <option value="Essence">Essence</option>
                            <option value="Diesel">Diesel</option>
                            <option value="Électrique">Électrique</option>
                            <option value="Hybride">Hybride</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="transmission">Boîte</label>
                        <select name="transmission" id="v_transmission" class="form-control">
                            <option value="Manuelle">Manuelle</option>
                            <option value="Automatique">Automatique</option>
                        </select>
                    </div>
                </div>
                <div class="grid-3">
                    <div class="form-group">
                        <label for="nombre_places">Places</label>
                        <input type="number" name="nombre_places" id="v_places" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="prix_jour">Prix / Jour (FCFA)</label>
                        <input type="number" name="prix_jour" id="v_prix" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="statut">Statut</label>
                        <select name="statut" id="v_statut" class="form-control">
                            <option value="disponible">Disponible</option>
                            <option value="reserve">Réservé</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="v_description" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="caracteristiques">Caractéristiques (ex: Clim, GPS, etc.)</label>
                    <textarea name="caracteristiques" id="v_caracteristiques" class="form-control" rows="2"></textarea>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-outline" onclick="closeModal()">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('vehicleModal');
        
        function openModal() {
            document.getElementById('modalTitle').textContent = "Ajouter un véhicule";
            document.getElementById('v_id').value = "";
            document.getElementById('v_old_image').value = "";
            document.getElementById('v_old_image2').value = "";
            document.getElementById('v_old_image3').value = "";
            document.getElementById('v_old_image4').value = "";
            document.getElementById('v_image').value = "";
            document.getElementById('v_image2').value = "";
            document.getElementById('v_image3').value = "";
            document.getElementById('v_image4').value = "";
            document.getElementById('v_marque').value = "";
            document.getElementById('v_modele').value = "";
            document.getElementById('v_annee').value = new Date().getFullYear();
            document.getElementById('v_prix').value = "";
            document.getElementById('v_places').value = 5;
            document.getElementById('v_description').value = "";
            document.getElementById('v_caracteristiques').value = "";
            modal.classList.add('show');
        }

        function closeModal() {
            modal.classList.remove('show');
        }

        function editVehicle(v) {
            document.getElementById('modalTitle').textContent = "Modifier le véhicule";
            document.getElementById('v_id').value = v.id;
            document.getElementById('v_old_image').value = v.image || "";
            document.getElementById('v_old_image2').value = v.image2 || "";
            document.getElementById('v_old_image3').value = v.image3 || "";
            document.getElementById('v_old_image4').value = v.image4 || "";
            document.getElementById('v_image').value = "";
            document.getElementById('v_image2').value = "";
            document.getElementById('v_image3').value = "";
            document.getElementById('v_image4').value = "";
            document.getElementById('v_marque').value = v.marque;
            document.getElementById('v_modele').value = v.modele;
            document.getElementById('v_annee').value = v.annee;
            document.getElementById('v_carburant').value = v.type_carburant;
            document.getElementById('v_transmission').value = v.transmission;
            document.getElementById('v_places').value = v.nombre_places;
            document.getElementById('v_prix').value = v.prix_jour;
            document.getElementById('v_statut').value = v.statut;
            document.getElementById('v_description').value = v.description;
            document.getElementById('v_caracteristiques').value = v.caracteristiques;
            modal.classList.add('show');
        }
    </script>
</body>
</html>
