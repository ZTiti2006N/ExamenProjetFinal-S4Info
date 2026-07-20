# 📋 Tâches réalisées par livraison

## Projet ExamenFinal - S4 Info

### Membres du groupe

- ETU 004203 : RATOVOHERINIAINA Nasandratriniavo Ny Tiavina
- ETU 004126 : RANDRIAMAHEFA Jean Jordie

---

## Version v1

### Coté Opérateur

| # | Tâche                                                       | Responsable | Statut|
|---|-------------------------------------------------------------|-------------|-------|
| 1 | Configuration des préfixes valables (ex: 033 et 037)        |   Tiavina   |  [X]  |
| 2 | Création des types d'opérations (dépôt, retrait, transfert) |   Tiavina   |  [X]  |
| 3 | Barèmes de frais par tranche de montant (modifiable)        |   Tiavina   |  [X]  |
| 4 | Situation des gains via les frais (retrait et transfert)    |   Tiavina   |  [X]  |
| 5 | Situation des comptes clients                               |   Tiavina   |  [X]  |

### Coté Client

| # | Tâche                                                            | Responsable | Statut |
|---|------------------------------------------------------------------|-------------|--------|
| 1 | Login automatique avec le numéro de téléphone (sans inscription) |   Tiavina   |  [X]   |
| 2 | Consultation du solde                                            |   Tiavina   |  [X]   |
| 3 | Faire un dépôt (automatique)                                     |   Tiavina   |  [X]   |
| 4 | Faire un retrait (automatique)                                   |   Tiavina   |  [X]   |
| 5 | Faire un transfert                                               |   Tiavina   |  [X]   |
| 6 | Voir l'historique des opérations                                 |   Tiavina   |  [X]   |

### Base de données

| # | Tâche                                               | Responsable | Statut |
|---|-----------------------------------------------------|-------------|--------|
| 1 | Conception et création du schéma SQL (tables, vues) |   Tiavina   |  [X]   |

---

## Version v2

### Coté Opérateur

| # | Tâche                                                                    | Responsable | Statut |
|---|--------------------------------------------------------------------------|-------------|--------|
| 1 | Configuration des préfixes additionnels (other_prefixes)                 |   Tiavina   |  [X]   |
| 2 | Commission en % pour transferts vers d'autres opérateurs                 |   Tiavina   |  [X]   |
| 3 | Gains séparés par opérateur (page Situation des gains)                   |   Tiavina   |  [X]   |
| 4 | Montants à envoyer à chaque opérateur (page /reports/operator-payouts)   |   Tiavina   |  [X]   |
| 5 | Correction boutons "Accueil" vers /log_admin dans toutes les pages admin |   Tiavina   |  [X]   |

### Coté Client

| # | Tâche                                                       | Responsable | Statut |
|---|-------------------------------------------------------------|-------------|--------|
| 1 | Option "Inclure les frais" dans le retrait                  |   Tiavina   |  [X]   |
| 2 | Transfert multiple vers plusieurs numéros (montant divisé)  |   Tiavina   |  [X]   |
| 3 | Graphique "Opérations par type" corrigé (0 au lieu de 1)    |   Tiavina   |  [X]   |
| 4 | Redirection après login vers /client (espace client)        |   Tiavina   |  [X]   |
| 5 | Page d'accueil (/) = Login, /log_admin = Dashboard admin    |   Tiavina   |  [X]   |

### Base de données

| # | Tâche                                                                    | Responsable | Statut |
|---|--------------------------------------------------------------------------|-------------|--------|
| 1 | Migration : ajout champ other_prefixes dans operators                    |   Tiavina   |  [X]   |
| 2 | Migration : ajout champ include_fees dans operations                     |   Tiavina   |  [X]   |
| 3 | Migration : création table operator_commissions                          |   Tiavina   |  [X]   |
| 4 | Migration : mise à jour vue v_fees_summary (regroupée par opérateur)     |   Tiavina   |  [X]   |
| 5 | Migration : création vue v_operator_payouts (montants à envoyer)         |   Tiavina   |  [X]   |

---

## Version v3
<!-- À compléter -->

| Tâche | Responsable | Statut |
|-------|-------------|--------|
|       |   Tiavina   |        |