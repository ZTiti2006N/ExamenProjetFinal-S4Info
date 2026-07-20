<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialData extends Seeder
{
    public function run()
    {
        // ========== OPERATEURS ==========
        $this->db->table('operators')->insertBatch([
            ['name' => 'Airtel', 'prefix' => '033'],
            ['name' => 'Orange', 'prefix' => '037'],
        ]);

        // ========== TYPES D'OPÉRATIONS ==========
        $this->db->table('operation_types')->insertBatch([
            ['code' => 'DEPOSIT',    'label' => 'Dépôt',     'has_fees' => 0],
            ['code' => 'WITHDRAWAL', 'label' => 'Retrait',   'has_fees' => 1],
            ['code' => 'TRANSFER',   'label' => 'Transfert', 'has_fees' => 1],
        ]);

        // Récupération des IDs des types d'opérations
        $withdrawalId = $this->db->table('operation_types')
            ->select('id')
            ->where('code', 'WITHDRAWAL')
            ->get()
            ->getRow()
            ->id;

        $transferId = $this->db->table('operation_types')
            ->select('id')
            ->where('code', 'TRANSFER')
            ->get()
            ->getRow()
            ->id;

        // ========== BARÈMES RETRAITS ==========
        $this->db->table('fee_scales')->insertBatch([
            ['operation_type_id' => $withdrawalId, 'min_amount' => 0,      'max_amount' => 10000,    'fee_fixed' => 50,   'fee_percentage' => 0.00],
            ['operation_type_id' => $withdrawalId, 'min_amount' => 10001,  'max_amount' => 50000,    'fee_fixed' => 100,  'fee_percentage' => 0.50],
            ['operation_type_id' => $withdrawalId, 'min_amount' => 50001,  'max_amount' => 100000,   'fee_fixed' => 200,  'fee_percentage' => 1.00],
            ['operation_type_id' => $withdrawalId, 'min_amount' => 100001, 'max_amount' => 999999999, 'fee_fixed' => 500, 'fee_percentage' => 1.50],
        ]);

        // ========== BARÈMES TRANSFERTS ==========
        $this->db->table('fee_scales')->insertBatch([
            ['operation_type_id' => $transferId, 'min_amount' => 0,      'max_amount' => 10000,    'fee_fixed' => 25,  'fee_percentage' => 0.00],
            ['operation_type_id' => $transferId, 'min_amount' => 10001,  'max_amount' => 50000,    'fee_fixed' => 50,  'fee_percentage' => 0.25],
            ['operation_type_id' => $transferId, 'min_amount' => 50001,  'max_amount' => 100000,   'fee_fixed' => 100, 'fee_percentage' => 0.50],
        ]);
    }
}