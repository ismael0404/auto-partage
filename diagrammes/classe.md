# Diagramme de Classes - AutoPartage

Ce document présente la structure statique du système AutoPartage, détaillant les entités, leurs attributs, méthodes et relations.

## 1. Modélisation Conceptuelle (Classes)

```mermaid
classDiagram
    class Utilisateur {
        +int id
        +string nom
        +string prenom
        +string email
        +string telephone
        +string mot_de_passe
        +enum role
        +bool is_deleted
        +datetime date_creation
        +login()
        +register()
        +updateProfile()
    }

    class Client {
        +consulterCatalogue()
        +verifierDisponibilite()
        +reserver()
        +consulterHistorique()
        +payer()
        +downloadRecu()
    }

    class Admin {
        +gererVehicules()
        +gererUtilisateurs()
        +gererReservations()
        +consulterStats()
        +repondreChat()
        +envoyerNotification()
    }

    class Vehicule {
        +int id
        +string marque
        +string modele
        +string immatriculation
        +enum energie
        +enum transmission
        +int nb_places
        +decimal prix_jour
        +enum statut
        +string image
        +add()
        +update()
        +delete()
        +checkAvailability()
    }

    class Reservation {
        +int id
        +int utilisateur_id
        +int vehicule_id
        +datetime date_debut
        +datetime date_fin
        +int duree_jours
        +decimal prix_unitaire
        +decimal prix_total
        +enum statut
        +enum mode_paiement
        +enum statut_paiement
        +calculateTotal()
        +confirm()
        +cancel()
        +pay()
    }

    class Message {
        +int id
        +int utilisateur_id
        +string titre
        +text contenu
        +enum type
        +bool lu
        +datetime date_creation
        +send()
        +markAsRead()
    }

    class ChatMessage {
        +int id
        +int expediteur_id
        +int destinataire_id
        +text message
        +bool lu
        +datetime date_envoi
        +send()
    }

    class Database {
        +PDO connection
        +connect()
        +query()
        +prepare()
    }

    Utilisateur <|-- Client
    Utilisateur <|-- Admin
    Client "1" -- "0..*" Reservation : effectue
    Vehicule "1" -- "0..*" Reservation : est loué
    Admin "1" -- "0..*" Vehicule : gère
    Admin "1" -- "0..*" Message : envoie
    Client "1" -- "0..*" Message : reçoit
    Utilisateur "1" -- "0..*" ChatMessage : envoie/reçoit
```

## 2. Détails des Relations
- **Héritage (Utilisateur)** : `Client` et `Admin` héritent des attributs de base de `Utilisateur` (authentification, profil).
- **Client <-> Reservation** : Relation 1 à N. Un client effectue des réservations.
- **Vehicule <-> Reservation** : Relation 1 à N. Un véhicule est loué via des réservations.
- **Admin <-> Vehicule** : L'administrateur gère (CRUD) le parc automobile.
- **Admin -> Message -> Client** : L'administrateur envoie des notifications (Message) reçues par les clients.
- **Utilisateur <-> ChatMessage** : Relation réflexive pour la messagerie instantanée entre clients et admins.

## 3. Analyse des Services (Helpers)
Le projet utilise des services transversaux non instanciés (fonctions procédurales dans `functions.php`) agissant comme des classes utilitaires :

| Service | Fonctions Clés |
| :--- | :--- |
| **AuthService** | `requireLogin()`, `requireAdmin()`, `isLoggedIn()` |
| **FormatService** | `formatPrix()`, `formatDate()`, `formatDateTime()` |
| **ValidationService** | `clean()`, `verifyCSRF()`, `vehiculeDisponible()` |
| **FinanceService** | `calculerDuree()`, `calculateTotal()` |

## 4. Cardinalités et Contraintes
- **Cardinalité [1..1]** : Une réservation doit obligatoirement être rattachée à un utilisateur et à un véhicule existant.
- **Contrainte de Statut** : Le statut d'une réservation dépend de la logique d'état (Workflow State Pattern) : `en_attente` -> `confirmee` -> `terminee`.
- **Intégrité Référentielle** : Les suppressions sont gérées par `is_deleted` (Soft Delete) pour éviter de casser les liens historiques dans la table `reservations`.
