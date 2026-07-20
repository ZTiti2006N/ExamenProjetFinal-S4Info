<?php

namespace App\Controllers;

use App\Models\OperationTypeModel;
use App\Models\OperatorModel;
use App\Models\FeeScaleModel;

class Home extends BaseController
{
    public function index(): string
    {
        $db = db_connect();

        $otModel = new OperationTypeModel();
        $opModel = new OperatorModel();
        $fsModel = new FeeScaleModel();

        // Statistiques générales
        $data['totalOperators']     = $opModel->countAllResults();
        $data['totalOperationTypes'] = $otModel->countAllResults();
        $data['totalFeeScales']     = $fsModel->countAllResults();

        // Compter les opérations depuis la table operations
        $totalOps = 0;
        try {
            $totalOps = $db->query("SELECT COUNT(*) as cnt FROM operations")->getRow()->cnt;
        } catch (\Exception $e) { $totalOps = 0; }
        $data['totalOperations'] = $totalOps;

        // Totaux des frais collectés - toujours 3 barres (Dépôt, Retrait, Transfert)
        $codes = ['DEPOSIT' => 'Dépôt', 'WITHDRAWAL' => 'Retrait', 'TRANSFER' => 'Transfert'];
        $feesData = [];
        $totalFeesCollected = 0;
        try {
            $raw = $db->query("SELECT ot.code, ot.label, COALESCE(SUM(o.fee_amount), 0) as total_fees, COUNT(o.id) as total_ops
                FROM operation_types ot
                LEFT JOIN operations o ON o.operation_type_id = ot.id AND o.status = 'completed'
                GROUP BY ot.code, ot.label
                ORDER BY ot.id")->getResultArray();
            foreach ($raw as $row) {
                $feesData[] = [
                    'operation_type'     => $row['code'],
                    'operation_label'    => $row['label'],
                    'total_fees_collected' => (float)$row['total_fees'],
                    'total_operations'   => (int)$row['total_ops'],
                    'total_amount_transacted' => 0
                ];
                $totalFeesCollected += (float)$row['total_fees'];
            }
        } catch (\Exception $e) {
            foreach ($codes as $code => $label) {
                $feesData[] = [
                    'operation_type'        => $code,
                    'operation_label'       => $label,
                    'total_fees_collected'  => 0,
                    'total_operations'      => 0,
                    'total_amount_transacted' => 0
                ];
            }
        }
        // Ensure we always have 3 items
        if (count($feesData) < 3) {
            $existingCodes = array_column($feesData, 'operation_type');
            foreach ($codes as $code => $label) {
                if (!in_array($code, $existingCodes)) {
                    $feesData[] = [
                        'operation_type'        => $code,
                        'operation_label'       => $label,
                        'total_fees_collected'  => 0,
                        'total_operations'      => 0,
                        'total_amount_transacted' => 0
                    ];
                }
            }
        }
        $data['feesSummary'] = $feesData;
        $data['totalFeesCollected'] = $totalFeesCollected;

        // Nombre de clients
        $totalClients = 0;
        try {
            $totalClients = $db->query("SELECT COUNT(*) as cnt FROM clients")->getRow()->cnt;
        } catch (\Exception $e) { $totalClients = 0; }
        $data['totalClients'] = $totalClients;

        // Types d'opérations avec leurs barèmes et nombre d'opérations effectuées
        $operationTypes = $otModel->findAll();
        foreach ($operationTypes as &$ot) {
            $ot['fee_count'] = $fsModel->where('operation_type_id', $ot['id'])->countAllResults();
            $ot['op_count'] = 0;
            try {
                $count = $db->query("SELECT COUNT(*) as cnt FROM operations WHERE operation_type_id = " . $ot['id'])->getRow()->cnt;
                $ot['op_count'] = (int)$count;
            } catch (\Exception $e) { $ot['op_count'] = 0; }
        }
        $data['operationTypes'] = $operationTypes;

        // Notifications : dernières opérations
        $data['recentNotifications'] = [];
        try {
            $data['recentNotifications'] = $db->query("
                SELECT o.id, ot.label as type, o.amount, o.fee_amount, o.created_at, c.last_name, c.first_name
                FROM operations o
                JOIN operation_types ot ON o.operation_type_id = ot.id
                JOIN clients c ON o.client_id = c.id
                ORDER BY o.created_at DESC LIMIT 5
            ")->getResultArray();
        } catch (\Exception $e) { $data['recentNotifications'] = []; }

        // Dernière activité
        $data['lastActivity'] = '';
        try {
            $last = $db->query("SELECT MAX(created_at) as last_time FROM operations")->getRow()->last_time;
            $data['lastActivity'] = $last ? $last : 'Aucune opération';
        } catch (\Exception $e) { $data['lastActivity'] = 'Aucune opération'; }

        $db->close();
        return view('welcome_message', $data);
    }
}