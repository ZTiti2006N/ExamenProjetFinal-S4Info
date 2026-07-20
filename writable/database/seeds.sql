-- ============================================================
-- Seeds ExamFinal - Projet S4 Info
-- SQLite
-- Version 1 : Données initiales
-- ============================================================

-- ============================================================
-- Opérateurs (préfixes)
-- ============================================================
INSERT INTO operators (name, prefix) VALUES
    ('Airtel', '033'),
    ('Orange', '037');

-- ============================================================
-- Types d'opérations
-- ============================================================
INSERT INTO operation_types (code, label, has_fees) VALUES
    ('DEPOSIT',    'Dépôt',     0),
    ('WITHDRAWAL', 'Retrait',   1),
    ('TRANSFER',   'Transfert', 1);

-- ============================================================
-- Barèmes de frais pour les retraits
-- ============================================================
INSERT INTO fee_scales (operation_type_id, min_amount, max_amount, fee_fixed, fee_percentage) VALUES
    ((SELECT id FROM operation_types WHERE code = 'WITHDRAWAL'), 0,     10000,   50,   0.00),
    ((SELECT id FROM operation_types WHERE code = 'WITHDRAWAL'), 10001, 50000,   100,  0.50),
    ((SELECT id FROM operation_types WHERE code = 'WITHDRAWAL'), 50001, 100000,  200,  1.00),
    ((SELECT id FROM operation_types WHERE code = 'WITHDRAWAL'), 100001, 999999999, 500, 1.50);

-- ============================================================
-- Barèmes de frais pour les transferts
-- ============================================================
INSERT INTO fee_scales (operation_type_id, min_amount, max_amount, fee_fixed, fee_percentage) VALUES
    ((SELECT id FROM operation_types WHERE code = 'TRANSFER'), 0,     10000,   25,   0.00),
    ((SELECT id FROM operation_types WHERE code = 'TRANSFER'), 10001, 50000,   50,   0.25),
    ((SELECT id FROM operation_types WHERE code = 'TRANSFER'), 50001, 100000,  100,  0.50);
