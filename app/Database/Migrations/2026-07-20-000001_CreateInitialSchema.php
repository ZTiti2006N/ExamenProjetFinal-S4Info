<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInitialSchema extends Migration
{
    public function up()
    {
        // ========== OPERATORS ==========
        $this->forge->addField([
            'id'         => ['type' => 'INTEGER', 'constraint' => 5, 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'prefix'     => ['type' => 'VARCHAR', 'constraint' => 10],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('prefix', false, true);
        $this->forge->createTable('operators');

        // ========== CLIENTS ==========
        $this->forge->addField([
            'id'          => ['type' => 'INTEGER', 'constraint' => 5, 'unsigned' => true, 'auto_increment' => true],
            'phone'       => ['type' => 'VARCHAR', 'constraint' => 20],
            'first_name'  => ['type' => 'VARCHAR', 'constraint' => 100],
            'last_name'   => ['type' => 'VARCHAR', 'constraint' => 100],
            'operator_id' => ['type' => 'INTEGER', 'constraint' => 5, 'unsigned' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('phone', false, true);
        $this->forge->addForeignKey('operator_id', 'operators', 'id');
        $this->forge->createTable('clients');

        // ========== ACCOUNTS ==========
        $this->forge->addField([
            'id'        => ['type' => 'INTEGER', 'constraint' => 5, 'unsigned' => true, 'auto_increment' => true],
            'client_id' => ['type' => 'INTEGER', 'constraint' => 5, 'unsigned' => true],
            'balance'   => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('client_id', false, true);
        $this->forge->addForeignKey('client_id', 'clients', 'id');
        $this->forge->createTable('accounts');

        // ========== OPERATION_TYPES ==========
        $this->forge->addField([
            'id'        => ['type' => 'INTEGER', 'constraint' => 5, 'unsigned' => true, 'auto_increment' => true],
            'code'      => ['type' => 'VARCHAR', 'constraint' => 20],
            'label'     => ['type' => 'VARCHAR', 'constraint' => 100],
            'has_fees'  => ['type' => 'INTEGER', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('code', false, true);
        $this->forge->createTable('operation_types');

        // ========== FEE_SCALES ==========
        $this->forge->addField([
            'id'                => ['type' => 'INTEGER', 'constraint' => 5, 'unsigned' => true, 'auto_increment' => true],
            'operation_type_id' => ['type' => 'INTEGER', 'constraint' => 5, 'unsigned' => true],
            'min_amount'        => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'max_amount'        => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'fee_fixed'         => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'fee_percentage'    => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0.00],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('operation_type_id', 'operation_types', 'id');
        $this->forge->createTable('fee_scales');

        // ========== OPERATIONS ==========
        $this->forge->addField([
            'id'                => ['type' => 'INTEGER', 'constraint' => 5, 'unsigned' => true, 'auto_increment' => true],
            'client_id'         => ['type' => 'INTEGER', 'constraint' => 5, 'unsigned' => true],
            'operation_type_id' => ['type' => 'INTEGER', 'constraint' => 5, 'unsigned' => true],
            'amount'            => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'fee_amount'        => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'recipient_phone'   => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'recipient_client_id' => ['type' => 'INTEGER', 'constraint' => 5, 'unsigned' => true, 'null' => true],
            'balance_before'    => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'balance_after'     => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'status'            => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'completed'],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('client_id', 'clients', 'id');
        $this->forge->addForeignKey('operation_type_id', 'operation_types', 'id');
        $this->forge->addForeignKey('recipient_client_id', 'clients', 'id');
        $this->forge->createTable('operations');

        // ========== VUES ==========

        // Vue : Situation des gains via les frais (retrait et transfert)
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

        // Vue : Situation des comptes clients
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

    public function down()
    {
        // Suppression des vues
        $this->db->query("DROP VIEW IF EXISTS v_fees_summary");
        $this->db->query("DROP VIEW IF EXISTS v_accounts_summary");

        // Suppression des tables (ordre inverse des dépendances)
        $this->forge->dropTable('operations');
        $this->forge->dropTable('fee_scales');
        $this->forge->dropTable('operation_types');
        $this->forge->dropTable('accounts');
        $this->forge->dropTable('clients');
        $this->forge->dropTable('operators');
    }
}