# Diagrammes de Séquence - AutoPartage

Ce document présente les flux d'interactions détaillés entre les différents composants du système AutoPartage pour les processus critiques.

---

## 1. Processus d'Inscription (S'inscrire)
Ce diagramme détaille la création d'un compte utilisateur avec validation des données et vérification d'unicité.

```mermaid
sequenceDiagram
    autonumber
    actor U as Utilisateur
    participant F as Frontend
    participant S as Server
    participant B as Base de données

    U->>F: Remplit le formulaire d'inscription
    F->>F: Validation côté client (Champs requis, Format email)
    F->>S: Requête POST (nom, prenom, email, mdp, tel)
    
    S->>S: Nettoyage & Sécurisation des entrées (Sanitization)
    S->>B: SELECT count(*) FROM utilisateurs WHERE email = ?
    B-->>S: Résultat (0 ou 1)

    alt Email déjà utilisé
        S-->>F: Retourne une erreur "Email déjà enregistré"
        F-->>U: Affiche le message d'alerte
    else Email disponible
        S->>S: Hachage du mot de passe (password_hash)
        S->>B: INSERT INTO utilisateurs (role='client', is_deleted=0, ...)
        B-->>S: Confirmation (ID généré)
        S-->>F: Redirection vers login.php (Success Flash)
        F-->>U: Affiche "Compte créé avec succès !"
    end
```

---

## 2. Processus de Connexion (Se connecter)
Ce diagramme illustre l'authentification sécurisée et l'initialisation de la session utilisateur.

```mermaid
sequenceDiagram
    autonumber
    actor U as Utilisateur
    participant F as Frontend
    participant S as Server
    participant B as Base de données

    U->>F: Saisit ses identifiants (Email/MDP)
    F->>S: Requête POST (login)
    
    S->>B: SELECT * FROM utilisateurs WHERE email = ? AND is_deleted = 0
    B-->>S: Données utilisateur (dont mot_de_passe haché)

    alt Utilisateur trouvé
        S->>S: Vérification du mot de passe (password_verify)
        if Mot de passe valide
            S->>S: Initialisation de la Session ($_SESSION)
            S-->>F: Redirection vers le tableau de bord
            F-->>U: Affiche l'espace personnel
        else Mot de passe invalide
            S-->>F: Retourne une erreur "Identifiants incorrects"
            F-->>U: Affiche le message d'erreur
        end
    else Utilisateur non trouvé
        S-->>F: Retourne une erreur "Identifiants incorrects"
        F-->>U: Affiche le message d'erreur
    end
```

---

## 3. Processus de Réservation (Réserver un véhicule)
Ce diagramme montre la logique de vérification de disponibilité et d'enregistrement d'une demande.

```mermaid
sequenceDiagram
    autonumber
    actor U as Utilisateur
    participant F as Frontend
    participant S as Server
    participant B as Base de données

    U->>F: Sélectionne un véhicule et des dates
    F->>S: Requête POST (vehicule_id, date_debut, date_fin)
    
    S->>B: Vérifie les conflits de dates (disponibilité)
    B-->>S: Résultat (Disponible / Indisponible)

    alt Véhicule Indisponible
        S-->>F: Retourne erreur "Véhicule déjà loué pour ces dates"
        F-->>U: Affiche l'indisponibilité
    else Véhicule Disponible
        S->>S: Calcul de la durée et du prix total
        S->>B: INSERT INTO reservations (statut='en_attente', ...)
        B-->>S: Confirmation ID Réservation
        
        S->>B: INSERT INTO messages (Notification Admin)
        B-->>S: Message enregistré
        
        S-->>F: Redirection vers l'historique
        F-->>U: Affiche "Réservation en attente de confirmation admin"
    end
```

---

## 4. Processus de Paiement (Simulation OTP)
Ce diagramme détaille la validation d'un paiement sécurisé par simulation de code.

```mermaid
sequenceDiagram
    autonumber
    actor U as Utilisateur
    participant F as Frontend
    participant S as Server
    participant B as Base de données

    U->>F: Clique sur "Payer la réservation"
    F-->>U: Affiche le modal de saisie OTP
    
    U->>F: Saisit le code (1212)
    F->>S: Requête POST (code_otp, reservation_id)
    
    alt Code Correct (1212)
        S->>B: UPDATE reservations SET statut_paiement='paye'
        B-->>S: Succès
        S->>B: INSERT INTO messages (Notification Succès)
        B-->>S: Enregistré
        S-->>F: Redirection (Succès)
        F-->>U: Affiche "Paiement validé, reçu disponible"
    else Code Incorrect
        S-->>F: Retourne erreur "Code OTP invalide"
        F-->>U: Demande une nouvelle saisie
    end
```

---

## 5. Processus de Confirmation Admin
Ce diagramme illustre le workflow de validation par l'administrateur.

```mermaid
sequenceDiagram
    autonumber
    actor U as Utilisateur (Admin)
    participant F as Frontend
    participant S as Server
    participant B as Base de données

    U->>F: Consulte les réservations "en attente"
    F->>S: Requête GET (pending_reservations)
    S->>B: SELECT * FROM reservations WHERE statut = 'en_attente'
    B-->>S: Liste des réservations
    S-->>F: Affiche la liste
    
    U->>F: Clique sur "Confirmer" une réservation
    F->>S: Requête POST (confirm_reservation, res_id)
    
    S->>B: UPDATE reservations SET statut='confirmee'
    B-->>S: Succès
    
    S->>B: INSERT INTO messages (Notification Client)
    B-->>S: Message envoyé au client
    
    S-->>F: Actualise le tableau de bord
    F-->>U: Affiche "Réservation confirmée avec succès"
```
