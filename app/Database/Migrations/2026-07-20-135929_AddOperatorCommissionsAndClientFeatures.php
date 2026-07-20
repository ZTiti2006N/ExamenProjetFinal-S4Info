<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOperatorCommissionsAndClientFeatures extends Migration
{
    public function up()
    {
        // ========== COMMISSIONS ENTRE OPÉRATEURS ==========
        // Pourcentage de commission sur les transferts vers d'autres opérateurs
        $this->forge->addField([
            'id'                => ['type' => 'INTEGER', 'constraint' => 5, 'unsigned' => true, 'auto_increment' => true],
            'operator_id'       => ['type' => 'INTEGER', 'constraint' => 5, 'unsigned' => true],
            'target_operator_id' => ['type' => 'INTEGER', 'constraint' => 5, 'unsigned' => true],
            'commission_percent' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0.00],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('operator_id', 'operators', 'id');
        $this->forge->addForeignKey('target_operator_id', 'operators', 'id');
        $this->forge->createTable('operator_commissions');

        // ========== AJOUT DU CHAMP other_prefixes DANS operators ==========
        // Format: liste des préfixes séparés par des virgules (ex: "032,031")
        $this->forge->addColumn('operators', [
            'other_prefixes' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => ''],
        ]);

        // ========== AJOUT DU CHAMP include_fees DANS operations ==========
        // Pour savoir si le client a choisi d'inclure les frais dans le montant
        $this->forge->addColumn('operations', [
            'include_fees' => ['type' => 'INTEGER', 'constraint' => 1, 'default' => 0],
        ]);

        // ========== MISE À JOUR DES VUES ==========
        // Supprimer les anciennes vues
        $this->db->query("DROP VIEW IF EXISTS v_fees_summary");
        $this->db->query("DROP VIEW IF EXISTS v_accounts_summary");

        // Nouvelle vue : Situation des gains - séparée par opérateur
        $this->db->query("
            CREATE VIEW v_fees_summary AS
            SELECT
                ot.code            AS operation_type,
                ot.label           AS operation_label,
                c.operator_id      AS client_operator_id,
                op.name            AS client_operator_name,
                COUNT(o.id)        AS total_operations,
                SUM(o.fee_amount)  AS total_fees_collected,
                SUM(o.amount)      AS total_amount_transacted
            FROM operations o
            JOIN operation_types ot ON o.operation_type_id = ot.id
            JOIN clients c ON o.client_id = c.id
            JOIN operators op ON c.operator_id = op.id
            WHERE ot.code IN ('WITHDRAWAL', 'TRANSFER')
              AND o.status = 'completed'
            GROUP BY ot.code, ot.label, c.operator_id, op.name
        ");

        // Nouvelle vue : Situation des comptes clients
        $this->db->query("
            CREATE VIEW v_accounts_summary AS
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
            GROUP BY c.id
        ");

        // Nouvelle vue : Montants à envoyer à chaque opérateur (commission inter-opérateur)
        $this->db->query("
            CREATE VIEW v_operator_payouts AS
            SELECT
                op.id               AS operator_id,
                op.name             AS operator_name,
                COUNT(o.id)         AS total_transferts_recus,
                COALESCE(SUM(o.amount), 0) AS total_montants_recus,
                COALESCE(SUM(o.fee_amount), 0) AS total_frais_percus,
                COALESCE(SUM(o.amount) * COALESCE(oc.commission_percent, 0) / 100, 0) AS commission_due
            FROM operators op
            LEFT JOIN clients c ON c.operator_id = op.id
            LEFT JOIN operations o ON o.recipient_client_id = c.id AND o.status = 'completed' AND o.operation_type_id = (SELECT id FROM operation_types WHERE code = 'TRANSFER')
            LEFT JOIN operator_commissions oc ON oc.target_operator_id = op.id
            GROUP BY op.id, op.name
        ");
    }

    public function down()
    {
        // Supprimer les vues
        $this->db->query("DROP VIEW IF EXISTS v_operator_payouts");
        $this->db->query("DROP VIEW IF EXISTS v_fees_summary");
        $this->db->query("DROP VIEW IF EXISTS v_accounts_summary");

        // Supprimer la colonne other_prefixes
        $this->forge->dropColumn('operators', 'other_prefixes');

        // Supprimer la colonne include_fees
        $this->forge->dropColumn('operations', 'include_fees');

        // Supprimer la table des commissions
        $this->forge->dropTable('operator_commissions');

        // Recréer les anciennes vues
        $this->db->query("
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
            GROUP BY ot.code, ot.label
        ");

        $this->db->query("
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
            GROUP BY c.id
        ");
    }
}