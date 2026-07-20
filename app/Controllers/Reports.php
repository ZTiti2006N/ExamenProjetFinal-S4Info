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
}