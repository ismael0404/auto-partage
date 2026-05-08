# Documentation Client - AutoPartage

Cette documentation explique en détail le fonctionnement de la plateforme AutoPartage du point de vue de l'utilisateur final (Client).

## 1. Accès et Authentification
### Inscription
Le nouveau client doit créer un compte en fournissant ses informations de base : Nom, Prénom, Email, Téléphone et un mot de passe sécurisé. 
- **Sécurité** : Le mot de passe est haché via l'algorithme `BCRYPT` avant stockage.

### Connexion
L'accès à l'espace personnel nécessite une authentification par email et mot de passe. Une session PHP sécurisée est alors ouverte, contenant les informations d'identité du client.

## 2. Recherche et Consultation de Véhicules
Le client accède à un catalogue complet de la flotte disponible.

### Filtres et Recherche
- **Navigation intuitive** : Tri par marque, catégorie ou prix.
- **Détails Techniques** : Chaque véhicule possède une fiche détaillée présentant ses caractéristiques (immatriculation, type de carburant, transmission, nombre de places).
- **Disponibilité en temps réel** : Seuls les véhicules avec le statut `disponible` peuvent être sélectionnés pour une nouvelle réservation.

## 3. Processus de Réservation (Workflow)
La réservation d'un véhicule suit un workflow structuré et sécurisé :

### Étape 1 : Sélection des Dates
Le client choisit ses dates de début et de fin. Le système vérifie instantanément :
1. Que les dates ne sont pas dans le passé.
2. Que la date de fin est postérieure à la date de début.
3. Que le véhicule n'est pas déjà réservé sur ce créneau (vérification SQL croisée).

### Étape 2 : Calcul du Prix
Le coût total est calculé automatiquement : `Prix journalier * Nombre de jours`. Une durée minimale de 1 jour est appliquée par défaut.

### Étape 3 : Validation et Attente
La demande est enregistrée avec le statut `en_attente`. Le client est redirigé vers son historique.

## 4. Paiement et Facturation
Une fois que l'administrateur a **confirmé** la réservation, le bouton "Payer" devient actif.

### Simulation de Paiement Multi-canal
Le client dispose de deux modes de règlement :
1. **Paiement sur place** : Un reçu est immédiatement généré indiquant que le règlement se fera à l'agence lors de la récupération des clés.
2. **Paiement en ligne (Simulé)** :
   - Choix de l'opérateur (Wave, Orange Money, Moov, MTN).
   - Saisie du numéro de téléphone.
   - Validation par code **OTP (toujours fixé à 1212)**.
   - Confirmation immédiate et passage du statut de paiement à `paye`.

### Téléchargement du Reçu
Le client peut télécharger son reçu au format PDF en un clic. Le document inclut :
- Détails de la réservation (Dates, Véhicule).
- Détails financiers (Prix total, Mode de paiement).
- QR Code (optionnel) et mentions légales.

## 5. Gestion du Profil et Historique
### Tableau de Bord Client
Le client suit son activité via des cartes statistiques (Total réservations, messages non lus, notifications).

### Historique des Réservations
Un tableau complet permet de suivre l'état de chaque demande :
- **En attente** : Attente de validation admin.
- **Confirmée** : Prêt pour le paiement.
- **Annulée** : Réservation refusée ou annulée.
- **Terminée** : Location close.

### Messagerie et Notifications
- **Support Chat** : Possibilité de discuter directement avec l'administrateur pour toute assistance.
- **Notifications** : Réception d'alertes en temps réel lors de la confirmation d'une réservation ou de messages importants de la plateforme.

## 6. Sécurité Client
- **Protection XSS** : Toutes les données affichées sont nettoyées via la fonction `clean()`.
- **Contrôle d'Intégrité** : Un client ne peut pas accéder aux réservations ou reçus d'un autre utilisateur (vérification stricte de `utilisateur_id` dans chaque requête).
- **CSRF** : Utilisation de jetons CSRF pour protéger les formulaires sensibles (modification profil, réservation).
