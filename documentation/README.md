# AutoPartage - Plateforme de Location de Véhicules

AutoPartage est une application web full-stack moderne permettant la gestion complète de locations de véhicules. Elle offre une expérience utilisateur premium tant pour les clients que pour les administrateurs, avec une gestion intégrée du cycle de vie des réservations, des paiements et de la communication.

---

## 📖 Présentation du Projet

### Contexte
Dans un environnement urbain de plus en plus mobile, la location de véhicules nécessite des outils agiles, rapides et sécurisés. AutoPartage répond à ce besoin en digitalisant l'ensemble du processus de location.

### Problématique
Comment simplifier la mise en relation entre un loueur de voitures et ses clients tout en garantissant la disponibilité des véhicules et la sécurité des transactions ?

### Objectifs
- Offrir une interface d'auto-service pour les clients.
- Automatiser le calcul des tarifs et la vérification des disponibilités.
- Fournir un outil de pilotage analytique pour l'administrateur.
- Assurer une communication fluide entre les acteurs.

---

## ⚙️ Description Générale

### Fonctionnement Global
La plateforme fonctionne comme une marketplace privée. L'administrateur alimente le parc automobile, et les clients inscrits réservent les véhicules pour des périodes définies. Le système gère les conflits de dates et les statuts de paiement.

### Acteurs
- **Client** : S'inscrit, consulte, réserve, paie et télécharge ses reçus.
- **Administrateur** : Gère la flotte, valide les demandes, supervise les revenus et assure le support.

---

## 🚀 Technologies Utilisées

- **Frontend** : HTML5, CSS3 (Vanilla avec variables pour thémisation), JavaScript (ES6+).
- **Backend** : PHP 8.x (Architecture procédurale propre).
- **Base de Données** : MySQL (Moteur InnoDB pour les relations).
- **Bibliothèques Tierces** : 
  - **Chart.js** : Visualisation de données.
  - **Font Awesome 6** : Iconographie.
  - **Html2Pdf.js** : Génération de documents PDF.
  - **Google Fonts** : Typographie Inter & Outfit.

---

## 🏗️ Architecture du Projet

### Structure des Dossiers
- `/admin` : Contrôleurs et vues de l'espace administration.
- `/auth` : Logique d'authentification (login, register).
- `/client` : Espace personnel client et workflow de réservation.
- `/config` : Configuration système et connexion base de données.
- `/includes` : Fonctions utilitaires, header/footer partagés, sidebar.
- `/assets` : Ressources statiques (CSS, JS, images).
- `/documentation` : Documentation technique détaillée.
- `/diagrammes` : Modélisation UML du système.

---

## 🔒 Sécurité

- **Gestion des Sessions** : Sessions PHP sécurisées avec `session_start()` et contrôles d'accès par rôle.
- **Hachage des Mots de Passe** : Utilisation de `password_hash()` avec `PASSWORD_BCRYPT`.
- **Prévention Injections SQL** : Usage systématique des requêtes préparées avec l'objet `PDO`.
- **Protection XSS** : Filtrage de toutes les sorties utilisateur via `htmlspecialchars`.
- **Protection CSRF** : Implémentation de tokens de sécurité sur les formulaires critiques.

---

## 📊 Base de Données

### Tables Principales
- **utilisateurs** : Stocke les profils, identifiants et rôles (admin/client).
- **vehicules** : Inventaire technique et tarifaire de la flotte.
- **reservations** : Lien entre utilisateurs et véhicules avec dates, coûts et statuts.
- **messages** : Système de notifications système.
- **chat_messages** : Historique des conversations entre acteurs.

---

## 🛠️ Installation du Projet

1. **Prérequis** : Installer XAMPP ou WAMP (PHP 7.4+ et MySQL).
2. **Copie** : Placer le dossier `hope` dans le répertoire `htdocs`.
3. **Base de Données** : 
   - Ouvrir `phpMyAdmin`.
   - Créer une base de données nommée `autopartage`.
   - Importer le fichier `database.sql` fourni à la racine.
4. **Configuration** : Vérifier les identifiants dans `config/database.php`.
5. **Lancement** : Accéder à `http://localhost/hope`.

---

## 🎨 UI/UX Design

- **Esthétique Premium** : Utilisation du mode sombre/clair moderne, de dégradés subtils et d'une typographie aérée.
- **Responsive Design** : Mise en page adaptative via Flexbox et CSS Grid, compatible mobiles et tablettes.
- **Expérience Utilisateur** : Feedbacks visuels immédiats (notifications flash), transitions fluides et navigation intuitive.

---

## 📈 Perspectives d'Amélioration

- **Intégration API Paiement** : Passer de la simulation à une réelle passerelle de paiement (Stripe ou CinetPay).
- **Géolocalisation** : Suivi GPS des véhicules en temps réel.
- **Application Mobile** : Développement d'une version Flutter ou React Native.
- **Smart Contracts** : Automatisation des cautions de location via la Blockchain.
