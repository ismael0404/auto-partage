# Diagrammes de Cas d'Utilisation - AutoPartage

Cette section présente les interactions détaillées entre les acteurs et le système AutoPartage via des diagrammes de cas d'utilisation UML complets et professionnels.

---

## 1. Diagramme de l'Acteur : Client
Ce diagramme illustre toutes les fonctionnalités accessibles à un utilisateur final, de la recherche de véhicule à la finalisation du paiement.

```mermaid
useCaseDiagram
    actor "Client" as C
    
    package "AutoPartage (Espace Client)" {
        usecase "S'inscrire" as UC_REG
        usecase "Se connecter" as UC_LOG
        usecase "Gérer son profil" as UC_PROF
        usecase "Consulter le catalogue de véhicules" as UC_CAT
        usecase "Voir les détails techniques d'un véhicule" as UC_DET
        usecase "Vérifier la disponibilité (Dates)" as UC_AVAIL
        usecase "Effectuer une réservation" as UC_RES
        usecase "Consulter l'historique de ses réservations" as UC_HIST
        usecase "Annuler une réservation" as UC_CANCEL
        usecase "Choisir le mode de paiement" as UC_PAY_CHOICE
        usecase "Payer sur place" as UC_PAY_SITE
        usecase "Payer en ligne (Simulation OTP)" as UC_PAY_ONLINE
        usecase "Télécharger le reçu (PDF)" as UC_PDF
        usecase "Discuter avec l'administrateur (Chat)" as UC_CHAT
        usecase "Consulter ses notifications" as UC_NOTIF
        usecase "Se déconnecter" as UC_OUT
    }
    
    C --> UC_REG
    C --> UC_LOG
    C --> UC_PROF
    C --> UC_CAT
    C --> UC_DET
    C --> UC_HIST
    C --> UC_CHAT
    C --> UC_NOTIF
    C --> UC_OUT
    
    UC_PROF ..> UC_LOG : <<include>>
    UC_CAT ..> UC_DET : <<extend>>
    UC_DET ..> UC_AVAIL : <<include>>
    UC_RES ..> UC_DET : <<include>>
    UC_RES ..> UC_LOG : <<include>>
    UC_HIST ..> UC_RES : <<extend>>
    UC_HIST ..> UC_CANCEL : <<extend>>
    UC_HIST ..> UC_PAY_CHOICE : <<extend>>
    UC_PAY_CHOICE <|-- UC_PAY_SITE
    UC_PAY_CHOICE <|-- UC_PAY_ONLINE
    UC_PDF ..> UC_PAY_CHOICE : <<include>>
```

---

## 2. Diagramme de l'Acteur : Administrateur
Ce diagramme détaille les outils de gestion, de modération et de pilotage analytique réservés à l'administrateur.

```mermaid
useCaseDiagram
    actor "Administrateur" as A
    
    package "AutoPartage (Console Admin)" {
        usecase "S'authentifier (Admin)" as UA_LOG
        usecase "Consulter le tableau de bord" as UA_DASH
        usecase "Visualiser les Analytics (Graphiques)" as UA_STATS
        usecase "Gérer la flotte de véhicules (CRUD)" as UA_V
        usecase "Ajouter un véhicule" as UA_V_ADD
        usecase "Modifier un véhicule" as UA_V_EDIT
        usecase "Supprimer un véhicule (Soft Delete)" as UA_V_DEL
        usecase "Gérer les comptes clients" as UA_U
        usecase "Gérer les réservations globales" as UA_R
        usecase "Confirmer une réservation" as UA_R_CONF
        usecase "Refuser/Annuler une réservation" as UA_R_CAN
        usecase "Clôturer une location (Terminée)" as UA_R_END
        usecase "Répondre aux messages clients (Chat)" as UA_CHAT
        usecase "Envoyer des notifications système" as UA_NOTIF
        usecase "Marquer ses alertes comme lues" as UA_READ
        usecase "Se déconnecter" as UA_OUT
    }
    
    A --> UA_LOG
    A --> UA_DASH
    A --> UA_V
    A --> UA_U
    A --> UA_R
    A --> UA_CHAT
    A --> UA_NOTIF
    A --> UA_OUT
    
    UA_DASH ..> UA_STATS : <<include>>
    UA_V <|-- UA_V_ADD
    UA_V <|-- UA_V_EDIT
    UA_V <|-- UA_V_DEL
    UA_R <|-- UA_R_CONF
    UA_R <|-- UA_R_CAN
    UA_R <|-- UA_R_END
    UA_READ ..> UA_NOTIF : <<include>>
    UA_DASH ..> UA_LOG : <<include>>
```

---

## 3. Descriptions Fonctionnelles Détaillées

### Acteur Client
- **Payer en ligne (Simulation OTP)** : Le système demande un code de validation (1212) pour valider la transaction fictive.
- **Télécharger le reçu (PDF)** : Génération dynamique d'un document PDF après sélection du mode de paiement ou confirmation du règlement.
- **Chat Support** : Interaction asynchrone avec l'administrateur pour l'assistance technique ou commerciale.

### Acteur Administrateur
- **Visualiser les Analytics** : Graphique de revenus sur 15 jours permettant un pilotage financier précis.
- **Confirmer une réservation** : Action nécessaire pour débloquer le processus de paiement côté client.
- **Gestion Véhicules** : Contrôle total sur les caractéristiques (immatriculation, prix, transmission) et l'état de disponibilité.
- **Soft Delete** : Désactivation des entités sans suppression physique pour maintenir l'intégrité des données comptables.
