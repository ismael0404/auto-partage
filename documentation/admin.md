# Documentation Administrateur - AutoPartage

Cette documentation détaille les fonctionnalités, la logique métier et l'architecture de l'interface d'administration de la plateforme AutoPartage.

## 1. Rôle et Privilèges de l'Administrateur
L'administrateur est le garant du bon fonctionnement opérationnel de la plateforme. Son rôle est de superviser les actifs (véhicules), de valider les interactions clients (réservations) et de suivre les performances financières.

### Permissions Admin
- Accès total au CRUD des véhicules.
- Gestion des comptes utilisateurs (activation/suspension/suppression).
- Contrôle du cycle de vie des réservations (confirmation, annulation, clôture).
- Communication centralisée (messagerie chat et notifications système).
- Visualisation analytique des données (revenus, taux d'occupation).

## 2. Dashboard Administrateur (Tableau de Bord)
Le tableau de bord est le centre de commande de l'application. Il offre une vue d'ensemble immédiate sur l'activité.

### Statistiques en Temps Réel
- **Cartes de Performance** : Affichage du nombre total de clients actifs, des réservations globales, et du revenu total généré (FCFA).
- **Alertes de Notifications** : Un badge dynamique signale les nouvelles notifications non lues (nouvelles demandes, paiements confirmés).
- **Graphique Analytics** : Utilisation de **Chart.js** pour afficher l'évolution quotidienne des revenus sur les 15 derniers jours, permettant de détecter les pics d'activité.

### Actions Rapides
Un panneau de raccourcis permet d'accéder instantanément aux tâches fréquentes :
- Ajout d'un nouveau véhicule.
- Envoi d'alertes système.
- Accès au support client (Chat).

## 3. Gestion des Véhicules
L'administrateur gère la flotte de véhicules disponible sur la plateforme.

### Attributs Gérés
- **Identification** : Marque, Modèle, Immatriculation.
- **Technique** : Énergie (Essence/Diesel/Électrique), Transmission (Manuelle/Automatique), Kilométrage, Nombre de places.
- **Financier** : Prix de location par jour.
- **Visuel** : Gestion des images associées.

### Statuts des Véhicules
- **Disponible** : Le véhicule apparaît dans le catalogue client.
- **Réservé** : Le véhicule est actuellement loué ou en attente de prise en charge.
- **Maintenance** : Le véhicule est retiré du catalogue pour entretien technique.

## 4. Gestion des Réservations
C'est le cœur de la logique métier admin.

### Workflow de Validation
1. **Réception** : Une nouvelle réservation arrive avec le statut `en_attente`.
2. **Examen** : L'admin vérifie les dates et la disponibilité réelle.
3. **Action** : 
   - **Confirmer** : Passage du statut à `confirmee`. Le client peut alors procéder au paiement.
   - **Annuler** : Passage à `annulee` (ex: problème technique sur le véhicule).
4. **Finalisation** : Une fois la location terminée, le statut passe à `terminee`.

### Suivi des Paiements
L'admin suit en temps réel le mode de paiement choisi par le client :
- **En ligne** (Wave, Orange Money, etc.) : Marqué automatiquement comme `paye` après validation OTP.
- **Sur place** : Marqué manuellement par l'admin lors de la remise des clés.

## 5. Gestion des Utilisateurs
L'admin possède une vue sur tous les clients inscrits.
- Consultation des profils et historiques de location.
- Capacité de supprimer un compte (soft-delete via la colonne `is_deleted`) pour préserver l'intégrité des données historiques.

## 6. Communication et Support
### Messagerie WhatsApp-style
L'admin dispose d'une interface de chat centralisée pour répondre aux interrogations des clients. Il peut rechercher un client par nom pour engager une discussion proactive.

### Système de Notifications
L'admin reçoit des alertes système pour :
- Chaque nouvelle réservation effectuée.
- Chaque paiement validé par un client.

## 7. Sécurité et Architecture Admin
### Contrôle d'Accès
L'accès est protégé par la fonction `requireAdmin()`. Si un utilisateur non-admin tente d'accéder à `/admin/*`, il est systématiquement redirigé vers l'accueil avec un message d'erreur.

### Intégrité des Données
- **Requêtes Préparées (PDO)** : Toutes les interactions SQL utilisent des paramètres liés pour prévenir les injections SQL.
- **Validation Côté Serveur** : Chaque modification (prix, dates, statuts) est vérifiée avant insertion pour éviter les incohérences métiers (ex: prix négatif).
- **Soft Delete** : Les véhicules et utilisateurs ne sont jamais totalement supprimés physiquement pour maintenir la cohérence des rapports financiers.

## 8. Logiciel et Architecture
L'interface admin suit une structure modulaire :
- `dashboard.php` : Logique analytique.
- `vehicles.php` : Gestion de la flotte.
- `reservations.php` : Gestion du workflow de location.
- `messages.php` & `notifications.php` : Couche communication.
