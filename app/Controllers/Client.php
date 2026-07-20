<?php

namespace App\Controllers;

use App\Models\FeeScaleModel;
use App\Models\OperationTypeModel;

class Client extends BaseController
{
    private function checkAuth()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/')->with('error', 'Veuillez vous connecter d\'abord.');
        }
    }

    // ===== TABLEAU DE BORD CLIENT =====
    public function index()
    {
        // Vérifier connexion
        $redirect = $this->checkAuth();
        if ($redirect) return $redirect;

        $db = db_connect();
        $clientId = session()->get('client_id');

        // Récupérer le solde
        $balance = 0;
        try {
            $balance = (float)$db->query("SELECT balance FROM accounts WHERE client_id = $clientId")->getRow()->balance;
        } catch (\Exception $e) { $balance = 0; }

        // Récupérer l'historique des 10 dernières opérations
        $history = [];
        try {
            $history = $db->query("
                SELECT o.id, ot.label as type, o.amount, o.fee_amount, 
                       o.recipient_phone, o.balance_before, o.balance_after, 
                       o.status, o.created_at
                FROM operations o
                JOIN operation_types ot ON o.operation_type_id = ot.id
                WHERE o.client_id = $clientId
                ORDER BY o.created_at DESC
                LIMIT 10
            ")->getResultArray();
        } catch (\Exception $e) { $history = []; }

        // Types d'opérations pour les formulaires
        $otModel = new OperationTypeModel();
        $operationTypes = $otModel->findAll();

        $db->close();

        $data = [
            'balance'         => $balance,
            'history'         => $history,
            'operationTypes'  => $operationTypes,
            'phone'           => session()->get('phone'),
            'first_name'      => session()->get('first_name'),
            'last_name'       => session()->get('last_name'),
        ];

        return view('client/dashboard', $data);
    }

    // ===== CONSULTATION SOLDE =====
    public function balance()
    {
        $redirect = $this->checkAuth();
        if ($redirect) return $redirect;

        $db = db_connect();
        $clientId = session()->get('client_id');
        $balance = 0;
        try {
            $balance = (float)$db->query("SELECT balance FROM accounts WHERE client_id = $clientId")->getRow()->balance;
        } catch (\Exception $e) { $balance = 0; }
        $db->close();

        return $this->response->setJSON(['balance' => $balance]);
    }

    // ===== DÉPÔT =====
    public function deposit()
    {
        $redirect = $this->checkAuth();
        if ($redirect) return $redirect;

        $amount = $this->request->getPost('amount');

        // Validation
        if (!$amount || !is_numeric($amount) || $amount <= 0) {
            return redirect()->back()->with('error', 'Montant invalide.');
        }

        $db = db_connect();
        $clientId = session()->get('client_id');

        try {
            // Récupérer l'ID du type d'opération DEPOSIT
            $depositType = $db->query("SELECT id FROM operation_types WHERE code = 'DEPOSIT'")->getRow();
            if (!$depositType) {
                throw new \Exception('Type d\'opération DÉPÔT introuvable.');
            }

            // Récupérer le solde actuel
            $account = $db->query("SELECT id, balance FROM accounts WHERE client_id = $clientId")->getRow();
            if (!$account) {
                throw new \Exception('Compte introuvable.');
            }

            $balanceBefore = (float)$account->balance;
            $balanceAfter = $balanceBefore + (float)$amount;
            $feeAmount = 0;

            // Vérifier les frais éventuels pour dépôt
            $feeScale = $db->query("SELECT fee_fixed, fee_percentage FROM fee_scales 
                WHERE operation_type_id = {$depositType->id} 
                AND $amount BETWEEN min_amount AND max_amount 
                LIMIT 1")->getRow();
            
            if ($feeScale) {
                $feeAmount = (float)$feeScale->fee_fixed + ((float)$amount * (float)$feeScale->fee_percentage / 100);
                $balanceAfter -= $feeAmount;
            }

            // Insérer l'opération
            $db->query("INSERT INTO operations (client_id, operation_type_id, amount, fee_amount, balance_before, balance_after, status, created_at) 
                VALUES ($clientId, {$depositType->id}, " . (float)$amount . ", $feeAmount, $balanceBefore, $balanceAfter, 'completed', datetime('now'))");

            // Mettre à jour le solde
            $db->query("UPDATE accounts SET balance = $balanceAfter, updated_at = datetime('now') WHERE client_id = $clientId");

            $db->close();
            return redirect()->to('/client')->with('success', 'Dépôt de ' . number_format($amount, 0, ',', ' ') . ' Ar effectué avec succès.' . ($feeAmount > 0 ? ' (Frais: ' . number_format($feeAmount, 0, ',', ' ') . ' Ar)' : ''));
        } catch (\Exception $e) {
            $db->close();
            return redirect()->back()->with('error', 'Erreur lors du dépôt : ' . $e->getMessage());
        }
    }

    // ===== RETRAIT =====
    public function withdrawal()
    {
        $redirect = $this->checkAuth();
        if ($redirect) return $redirect;

        $amount = $this->request->getPost('amount');

        if (!$amount || !is_numeric($amount) || $amount <= 0) {
            return redirect()->back()->with('error', 'Montant invalide.');
        }

        $db = db_connect();
        $clientId = session()->get('client_id');

        try {
            $withdrawalType = $db->query("SELECT id FROM operation_types WHERE code = 'WITHDRAWAL'")->getRow();
            if (!$withdrawalType) {
                throw new \Exception('Type d\'opération RETRAIT introuvable.');
            }

            $account = $db->query("SELECT id, balance FROM accounts WHERE client_id = $clientId")->getRow();
            if (!$account) {
                throw new \Exception('Compte introuvable.');
            }

            $balanceBefore = (float)$account->balance;
            $amountFloat = (float)$amount;
            $feeAmount = 0;

            // Calculer les frais
            $feeScale = $db->query("SELECT fee_fixed, fee_percentage FROM fee_scales 
                WHERE operation_type_id = {$withdrawalType->id} 
                AND $amountFloat BETWEEN min_amount AND max_amount 
                LIMIT 1")->getRow();
            
            if ($feeScale) {
                $feeAmount = (float)$feeScale->fee_fixed + ($amountFloat * (float)$feeScale->fee_percentage / 100);
            }

            $totalDeduction = $amountFloat + $feeAmount;

            if ($balanceBefore < $totalDeduction) {
                $db->close();
                return redirect()->back()->with('error', 'Solde insuffisant. Vous avez ' . number_format($balanceBefore, 0, ',', ' ') . ' Ar, mais le retrait de ' . number_format($amountFloat, 0, ',', ' ') . ' Ar + frais de ' . number_format($feeAmount, 0, ',', ' ') . ' Ar = ' . number_format($totalDeduction, 0, ',', ' ') . ' Ar.');
            }

            $balanceAfter = $balanceBefore - $totalDeduction;

            $db->query("INSERT INTO operations (client_id, operation_type_id, amount, fee_amount, balance_before, balance_after, status, created_at) 
                VALUES ($clientId, {$withdrawalType->id}, $amountFloat, $feeAmount, $balanceBefore, $balanceAfter, 'completed', datetime('now'))");

            $db->query("UPDATE accounts SET balance = $balanceAfter, updated_at = datetime('now') WHERE client_id = $clientId");

            $db->close();
            return redirect()->to('/client')->with('success', 'Retrait de ' . number_format($amountFloat, 0, ',', ' ') . ' Ar effectué. Frais: ' . number_format($feeAmount, 0, ',', ' ') . ' Ar.');
        } catch (\Exception $e) {
            $db->close();
            return redirect()->back()->with('error', 'Erreur lors du retrait : ' . $e->getMessage());
        }
    }

    // ===== TRANSFERT =====
    public function transfer()
    {
        $redirect = $this->checkAuth();
        if ($redirect) return $redirect;

        $recipientPhone = $this->request->getPost('recipient_phone');
        $amount = $this->request->getPost('amount');

        // Validation
        if (!$recipientPhone || !$amount || !is_numeric($amount) || $amount <= 0) {
            return redirect()->back()->with('error', 'Données invalides.');
        }

        if (!preg_match('/^\d{10}$/', $recipientPhone)) {
            return redirect()->back()->with('error', 'Le numéro du destinataire doit contenir exactement 10 chiffres.');
        }

        $db = db_connect();
        $clientId = session()->get('client_id');

        try {
            // Vérifier que le destinataire n'est pas l'expéditeur
            if ($recipientPhone === session()->get('phone')) {
                $db->close();
                return redirect()->back()->with('error', 'Vous ne pouvez pas vous transférer de l\'argent à vous-même.');
            }

            $transferType = $db->query("SELECT id FROM operation_types WHERE code = 'TRANSFER'")->getRow();
            if (!$transferType) {
                throw new \Exception('Type d\'opération TRANSFERT introuvable.');
            }

            // Trouver le destinataire
            $recipient = $db->query("SELECT id FROM clients WHERE phone = '$recipientPhone'")->getRow();
            if (!$recipient) {
                $db->close();
                return redirect()->back()->with('error', 'Le numéro ' . $recipientPhone . ' n\'est pas un client enregistré.');
            }

            $recipientAccount = $db->query("SELECT id, balance FROM accounts WHERE client_id = {$recipient->id}")->getRow();
            if (!$recipientAccount) {
                throw new \Exception('Compte du destinataire introuvable.');
            }

            $senderAccount = $db->query("SELECT id, balance FROM accounts WHERE client_id = $clientId")->getRow();
            if (!$senderAccount) {
                throw new \Exception('Compte introuvable.');
            }

            $balanceBefore = (float)$senderAccount->balance;
            $amountFloat = (float)$amount;
            $feeAmount = 0;

            // Calculer les frais
            $feeScale = $db->query("SELECT fee_fixed, fee_percentage FROM fee_scales 
                WHERE operation_type_id = {$transferType->id} 
                AND $amountFloat BETWEEN min_amount AND max_amount 
                LIMIT 1")->getRow();
            
            if ($feeScale) {
                $feeAmount = (float)$feeScale->fee_fixed + ($amountFloat * (float)$feeScale->fee_percentage / 100);
            }

            $totalDeduction = $amountFloat + $feeAmount;

            if ($balanceBefore < $totalDeduction) {
                $db->close();
                return redirect()->back()->with('error', 'Solde insuffisant. Vous avez ' . number_format($balanceBefore, 0, ',', ' ') . ' Ar, le transfert de ' . number_format($amountFloat, 0, ',', ' ') . ' Ar + frais ' . number_format($feeAmount, 0, ',', ' ') . ' Ar = ' . number_format($totalDeduction, 0, ',', ' ') . ' Ar.');
            }

            $balanceAfter = $balanceBefore - $totalDeduction;
            $recipientBalanceBefore = (float)$recipientAccount->balance;
            $recipientBalanceAfter = $recipientBalanceBefore + $amountFloat;

            // Insérer l'opération pour l'expéditeur
            $db->query("INSERT INTO operations (client_id, operation_type_id, amount, fee_amount, recipient_phone, recipient_client_id, balance_before, balance_after, status, created_at) 
                VALUES ($clientId, {$transferType->id}, $amountFloat, $feeAmount, '$recipientPhone', {$recipient->id}, $balanceBefore, $balanceAfter, 'completed', datetime('now'))");

            // Mettre à jour le solde de l'expéditeur
            $db->query("UPDATE accounts SET balance = $balanceAfter, updated_at = datetime('now') WHERE client_id = $clientId");

            // Créditer le destinataire
            // On insère aussi une opération pour le destinataire (avec montant positif)
            $db->query("INSERT INTO operations (client_id, operation_type_id, amount, fee_amount, balance_before, balance_after, status, created_at) 
                VALUES ({$recipient->id}, {$transferType->id}, $amountFloat, 0, $recipientBalanceBefore, $recipientBalanceAfter, 'completed', datetime('now'))");

            $db->query("UPDATE accounts SET balance = $recipientBalanceAfter, updated_at = datetime('now') WHERE client_id = {$recipient->id}");

            $db->close();
            return redirect()->to('/client')->with('success', 'Transfert de ' . number_format($amountFloat, 0, ',', ' ') . ' Ar vers ' . $recipientPhone . ' effectué. Frais: ' . number_format($feeAmount, 0, ',', ' ') . ' Ar.');
        } catch (\Exception $e) {
            $db->close();
            return redirect()->back()->with('error', 'Erreur lors du transfert : ' . $e->getMessage());
        }
    }

    // ===== HISTORIQUE COMPLET =====
    public function history()
    {
        $redirect = $this->checkAuth();
        if ($redirect) return $redirect;

        $db = db_connect();
        $clientId = session()->get('client_id');

        $history = [];
        try {
            $history = $db->query("
                SELECT o.id, ot.label as type, o.amount, o.fee_amount, 
                       o.recipient_phone, o.balance_before, o.balance_after, 
                       o.status, o.created_at
                FROM operations o
                JOIN operation_types ot ON o.operation_type_id = ot.id
                WHERE o.client_id = $clientId
                ORDER BY o.created_at DESC
            ")->getResultArray();
        } catch (\Exception $e) { $history = []; }

        $db->close();

        $data = [
            'history' => $history,
            'phone'   => session()->get('phone'),
        ];

        return view('client/history', $data);
    }
}