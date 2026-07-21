-- ============================================================
-- Base de données ExamFinal - Projet S4 Info
-- SQLite
-- Version 1 : Structure initiale (tables + vues)
-- Données dans seeds.sql
-- ============================================================

-- ============================================================
-- SUPPRESSION DES TABLES (ordre inverse des dépendances)
-- ============================================================
DROP TABLE IF EXISTS operations;
DROP TABLE IF EXISTS fee_scales;
DROP TABLE IF EXISTS operation_types;
DROP TABLE IF EXISTS accounts;
DROP TABLE IF EXISTS clients;
DROP TABLE IF EXISTS operators;

-- ============================================================
-- TABLES
-- ============================================================

-- Opérateur (configuration des préfixes valables)
CREATE TABLE IF NOT EXISTS operators (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    name        VARCHAR(100) NOT NULL,                     -- Nom de l'opérateur (ex: "Orange", "Djezzy")
    prefix      VARCHAR(10) NOT NULL UNIQUE,               -- Préfixe (ex: "033", "037")
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Clients
CREATE TABLE IF NOT EXISTS clients (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    phone           VARCHAR(20) NOT NULL UNIQUE,           -- Numéro de téléphone (login)
    first_name      VARCHAR(100) NOT NULL,
    last_name       VARCHAR(100) NOT NULL,
    operator_id     INTEGER NOT NULL,                      -- Référence à l'opérateur (via préfixe)
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (operator_id) REFERENCES operators(id)
);

-- Comptes clients
CREATE TABLE IF NOT EXISTS accounts (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id       INTEGER NOT NULL UNIQUE,               -- Un seul compte par client
    balance         DECIMAL(15,2) NOT NULL DEFAULT 0.00,   -- Solde actuel
    epargne_balance DECIMAL(15,2) NOT NULL DEFAULT 0.00,   -- Solde d'épargne
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id)
);

-- Types d'opérations (dépôt, retrait, transfert)
CREATE TABLE IF NOT EXISTS operation_types (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    code            VARCHAR(20) NOT NULL UNIQUE,           -- Code interne (DEPOSIT, WITHDRAWAL, TRANSFER)
    label           VARCHAR(100) NOT NULL,                 -- Libellé (Dépôt, Retrait, Transfert)
    has_fees        INTEGER NOT NULL DEFAULT 1,            -- 1 = applique des frais, 0 = pas de frais
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Barèmes de frais par tranche de montant (modifiable)
CREATE TABLE IF NOT EXISTS fee_scales (
    id                  INTEGER PRIMARY KEY AUTOINCREMENT,
    operation_type_id   INTEGER NOT NULL,                  -- Type d'opération concerné
    min_amount          DECIMAL(15,2) NOT NULL,            -- Montant minimum de la tranche
    max_amount          DECIMAL(15,2) NOT NULL,            -- Montant maximum de la tranche (NULL = illimité)
    fee_fixed           DECIMAL(15,2) NOT NULL DEFAULT 0.00, -- Frais fixes
    fee_percentage      DECIMAL(5,2) NOT NULL DEFAULT 0.00,  -- Frais en pourcentage (ex: 1.50 = 1.50%)
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (operation_type_id) REFERENCES operation_types(id)
);

-- Opérations (historique)
CREATE TABLE IF NOT EXISTS operations (
    id                  INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id           INTEGER NOT NULL,                  -- Client émetteur
    operation_type_id   INTEGER NOT NULL,                  -- Type d'opération
    amount              DECIMAL(15,2) NOT NULL,            -- Montant de l'opération
    fee_amount          DECIMAL(15,2) NOT NULL DEFAULT 0.00, -- Montant des frais appliqués
    recipient_phone     VARCHAR(20),                       -- Pour les transferts : destinataire
    recipient_client_id INTEGER,                           -- Pour les transferts : ID du destinataire
    balance_before      DECIMAL(15,2) NOT NULL,            -- Solde avant l'opération
    balance_after       DECIMAL(15,2) NOT NULL,            -- Solde après l'opération
    status              VARCHAR(20) NOT NULL DEFAULT 'completed', -- completed, pending, failed
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (operation_type_id) REFERENCES operation_types(id),
    FOREIGN KEY (recipient_client_id) REFERENCES clients(id)
);

-- ============================================================
-- VUES
-- ============================================================

-- Vue : Situation des gains via les frais (retrait et transfert)
CREATE VIEW IF NOT EXISTS v_fees_summary AS
SELECT
    ot.code            AS operation_type,
    ot.label           AS operation_label,
    COUNT(o.id)        AS total_operations,
    SUM(o.fee_amount)  AS total_fees_collected,
    SUM(o.amount)      AS total_amount_transacted
FROM operations o
JOIN operation_types ot ON o.operation_type_id = ot.id
WHERE ot.code IN ('WITHDRAWAL', 'TRANSFER')
  AND o.status = 'completed'
GROUP BY ot.code, ot.label;

-- Vue : Situation des comptes clients
CREATE VIEW IF NOT EXISTS v_accounts_summary AS
SELECT
    c.id                AS client_id,
    c.last_name         AS nom,
    c.first_name        AS prenom,
    c.phone             AS telephone,
    op.name             AS operateur,
    a.balance           AS solde_actuel,
    COUNT(o.id)         AS total_operations,
    COALESCE(SUM(CASE WHEN o.operation_type_id = (SELECT id FROM operation_types WHERE code = 'DEPOSIT') THEN o.amount END), 0)   AS total_depots,
    COALESCE(SUM(CASE WHEN o.operation_type_id = (SELECT id FROM operation_types WHERE code = 'WITHDRAWAL') THEN o.amount END), 0) AS total_retraits,
    COALESCE(SUM(CASE WHEN o.operation_type_id = (SELECT id FROM operation_types WHERE code = 'TRANSFER') AND o.client_id = c.id THEN o.amount END), 0) AS total_transferts_emis,
    COALESCE(SUM(CASE WHEN o.operation_type_id = (SELECT id FROM operation_types WHERE code = 'TRANSFER') AND o.recipient_client_id = c.id THEN o.amount END), 0) AS total_transferts_recus
FROM clients c
JOIN operators op ON c.operator_id = op.id
JOIN accounts a ON c.id = a.client_id
LEFT JOIN operations o ON c.id = o.client_id AND o.status = 'completed'
GROUP BY c.id;

