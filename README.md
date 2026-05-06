# AutoPartage - Application d'Autopartage

Une plateforme web complète de location de véhicules (autopartage) développée avec PHP, MySQL et une interface moderne.

## Fonctionnalités

### Côté Client
- **Catalogue interactif** : Recherche et filtrage par marque, type de carburant et prix.
- **Réservation intelligente** : Vérification automatique de la disponibilité et calcul du prix selon la durée.
- **Tableau de bord** : Suivi des réservations (En attente, Confirmée, Terminée).
- **Profil & Messages** : Gestion des informations personnelles et centre de notifications.

### Côté Administrateur
- **Dashboard Global** : Statistiques en temps réel sur les clients, véhicules et revenus.
- **Gestion de Flotte (CRUD)** : Ajout, modification et suppression de véhicules.
- **Modération des Réservations** : Système de validation (Confirmer/Annuler) avec notification automatique au client.
- **Gestion des Clients** : Activation ou suspension des comptes utilisateurs.

## Stack Technique
- **Backend** : PHP 8+ (PDO obligatoire)
- **Base de données** : MySQL / MariaDB
- **Frontend** : HTML5, CSS3 (Vanilla), Font Awesome 6, JavaScript (ES6)
- **Sécurité** : `password_hash()`, Sessions PHP, Protection XSS, Requêtes préparées.

## Installation

1. Copiez le dossier `hope` dans votre répertoire `htdocs` (XAMPP).
2. Créez une base de données nommée `autopartage` dans phpMyAdmin.
3. Importez le fichier `database.sql` situé à la racine du projet.
4. Accédez à l'application via `http://localhost/hope`.

### Identifiants de test
- **Admin** : `admin@autopartage.com` / `admin123`
- **Client** : `jean.dupont@email.com` / `password`

## 📁 Structure du Projet
- `/admin` : Pages réservées à l'administration.
- `/auth` : Système de connexion et d'inscription.
- `/client` : Espace utilisateur et catalogue.
- `/config` : Paramètres de base de données.
- `/includes` : Composants réutilisables (header, footer, sidebar) et fonctions.
- `/assets` : Ressources CSS, JS et images.
