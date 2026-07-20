<?php

namespace App\Controllers;

use CodeIgniter\Database\Exceptions\DatabaseException;

class Reports extends BaseController
{
    // -------------------------------------------------
    // SITUATION DES GAINS VIA LES FRAIS
    // -------------------------------------------------
    public function feesSummary()
    {
        $db = db_connect();

        try {
            $data['feesSummary'] = $db->query("SELECT * FROM v_fees_summary")->getResultArray();
        } catch (DatabaseException $e) {
            $data['feesSummary'] = [];
            $data['dbError'] = 'La vue v_fees_summary n\'existe pas. Veuillez exécuter les migrations.';
        }

        // Regrouper par opérateur
        $groupedByOperator = [];
        $totalByOperator = [];
        foreach ($data['feesSummary'] as $row) {
            $opName = $row['client_operator_name'] ?? 'Inconnu';
            $type = $row['operation_type'];
            if (!isset($groupedByOperator[$opName])) {
                $groupedByOperator[$opName] = [];
                $totalByOperator[$opName] = ['total_fees' => 0, 'total_amount' => 0, 'total_ops' => 0];
            }
            $groupedByOperator[$opName][] = $row;
            $totalByOperator[$opName]['total_fees'] += (float)$row['total_fees_collected'];
            $totalByOperator[$opName]['total_amount'] += (float)$row['total_amount_transacted'];
            $totalByOperator[$opName]['total_ops'] += (int)$row['total_operations'];
        }
        $data['groupedByOperator'] = $groupedByOperator;
        $data['totalByOperator'] = $totalByOperator;

        // Grands totaux
        $data['grandTotalFees'] = array_sum(array_column($totalByOperator, 'total_fees'));
        $data['grandTotalAmount'] = array_sum(array_column($totalByOperator, 'total_amount'));

        return view('reports/fees_summary', $data);
    }

    // -------------------------------------------------
    // SITUATION DES COMPTES CLIENTS
    // -------------------------------------------------
    public function accountsSummary()
    {
        $db = db_connect();

        try {
            $data['accountsSummary'] = $db->query("SELECT * FROM v_accounts_summary")->getResultArray();
        } catch (DatabaseException $e) {
            $data['accountsSummary'] = [];
            $data['dbError'] = 'La vue v_accounts_summary n\'existe pas. Veuillez exécuter les migrations.';
        }

        return view('reports/accounts_summary', $data);
    }

    // -------------------------------------------------
    // MONTANTS À ENVOYER À CHAQUE OPÉRATEUR
    // -------------------------------------------------
    public function operatorPayouts()
    {
        $db = db_connect();

        $payouts = [];
        try {
            $payouts = $db->query("SELECT * FROM v_operator_payouts")->getResultArray();
        } catch (DatabaseException $e) {
            $payouts = [];
            $data['dbError'] = 'La vue v_operator_payouts n\'existe pas. Veuillez exécuter les migrations.';
        }

        $data['payouts'] = $payouts;
        return view('reports/operator_payouts', $data);
    }
}