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
        $redirect = $this->checkAuth();
        if ($redirect) return $redirect;

        $db = db_connect();
        $clientId = session()->get('client_id');

        $balance = 0;
        try {
            $balance = (float)$db->query("SELECT balance FROM accounts WHERE client_id = $clientId")->getRow()->balance;
        } catch (\Exception $e) { $balance = 0; }

        $history = [];
        try {
            $history = $db->query("
                SELECT o.id, ot.label as type, o.amount, o.fee_amount, 
                       o.recipient_phone, o.balance_before, o.balance_after, 
                       o.status, o.created_at, o.include_fees
                FROM operations o
                JOIN operation_types ot ON o.operation_type_id = ot.id
                WHERE o.client_id = $clientId
                ORDER BY o.created_at DESC
                LIMIT 10
            ")->getResultArray();
        } catch (\Exception $e) { $history = []; }

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

        if (!$amount || !is_numeric($amount) || $amount <= 0) {
            return redirect()->back()->with('error', 'Montant invalide.');
        }

        $db = db_connect();
        $clientId = session()->get('client_id');

        try {
            $depositType = $db->query("SELECT id FROM operation_types WHERE code = 'DEPOSIT'")->getRow();
            if (!$depositType) {
                throw new \Exception('Type d\'opération DÉPÔT introuvable.');
            }

            $account = $db->query("SELECT id, balance FROM accounts WHERE client_id = $clientId")->getRow();
            if (!$account) {
                throw new \Exception('Compte introuvable.');
            }

            $balanceBefore = (float)$account->balance;
            $balanceAfter = $balanceBefore + (float)$amount;
            $feeAmount = 0;

            $feeScale = $db->query("SELECT fee_fixed, fee_percentage FROM fee_scales 
                WHERE operation_type_id = {$depositType->id} 
                AND $amount BETWEEN min_amount AND max_amount 
                LIMIT 1")->getRow();
            
            if ($feeScale) {
                $feeAmount = (float)$feeScale->fee_fixed + ((float)$amount * (float)$feeScale->fee_percentage / 100);
                $balanceAfter -= $feeAmount;
            }

            $db->query("INSERT INTO operations (client_id, operation_type_id, amount, fee_amount, balance_before, balance_after, status, created_at, include_fees) 
                VALUES ($clientId, {$depositType->id}, " . (float)$amount . ", $feeAmount, $balanceBefore, $balanceAfter, 'completed', datetime('now'), 0)");

            $db->query("UPDATE accounts SET balance = $balanceAfter, updated_at = datetime('now') WHERE client_id = $clientId");

            $db->close();
            return redirect()->to('/client')->with('success', 'Dépôt de ' . number_format($amount, 0, ',', ' ') . ' Ar effectué avec succès.' . ($feeAmount > 0 ? ' (Frais: ' . number_format($feeAmount, 0, ',', ' ') . ' Ar)' : ''));
        } catch (\Exception $e) {
            $db->close();
            return redirect()->back()->with('error', 'Erreur lors du dépôt : ' . $e->getMessage());
        }
    }

    // ===== RETRAIT (avec option inclure frais) =====
    public function withdrawal()
    {
        $redirect = $this->checkAuth();
        if ($redirect) return $redirect;

        $amount = $this->request->getPost('amount');
        $includeFees = $this->request->getPost('include_fees') ? 1 : 0;

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

            if ($includeFees) {
                // Le montant demandé inclut les frais : on retire (montant demandé) du solde
                // Le client reçoit = montant demandé - frais
                $totalDeduction = $amountFloat;
                $netAmount = $amountFloat - $feeAmount;
                
                if ($netAmount <= 0) {
                    $db->close();
                    return redirect()->back()->with('error', 'Les frais (' . number_format($feeAmount, 0, ',', ' ') . ' Ar) dépassent le montant demandé.');
                }
                
                if ($balanceBefore < $totalDeduction) {
                    $db->close();
                    return redirect()->back()->with('error', 'Solde insuffisant. Vous avez ' . number_format($balanceBefore, 0, ',', ' ') . ' Ar.');
                }

                $balanceAfter = $balanceBefore - $totalDeduction;

                $db->query("INSERT INTO operations (client_id, operation_type_id, amount, fee_amount, balance_before, balance_after, status, created_at, include_fees) 
                    VALUES ($clientId, {$withdrawalType->id}, $netAmount, $feeAmount, $balanceBefore, $balanceAfter, 'completed', datetime('now'), 1)");
            } else {
                // Mode normal: montant + frais sont déduits
                $totalDeduction = $amountFloat + $feeAmount;

                if ($balanceBefore < $totalDeduction) {
                    $db->close();
                    return redirect()->back()->with('error', 'Solde insuffisant. Vous avez ' . number_format($balanceBefore, 0, ',', ' ') . ' Ar, mais le retrait de ' . number_format($amountFloat, 0, ',', ' ') . ' Ar + frais de ' . number_format($feeAmount, 0, ',', ' ') . ' Ar = ' . number_format($totalDeduction, 0, ',', ' ') . ' Ar.');
                }

                $balanceAfter = $balanceBefore - $totalDeduction;

                $db->query("INSERT INTO operations (client_id, operation_type_id, amount, fee_amount, balance_before, balance_after, status, created_at, include_fees) 
                    VALUES ($clientId, {$withdrawalType->id}, $amountFloat, $feeAmount, $balanceBefore, $balanceAfter, 'completed', datetime('now'), 0)");
            }

            $db->query("UPDATE accounts SET balance = $balanceAfter, updated_at = datetime('now') WHERE client_id = $clientId");

            $db->close();
            $netMsg = $includeFees ? ' (frais inclus dans le montant)' : '';
            return redirect()->to('/client')->with('success', 'Retrait de ' . number_format($amountFloat, 0, ',', ' ') . ' Ar effectué.' . $netMsg . ' Frais: ' . number_format($feeAmount, 0, ',', ' ') . ' Ar.');
        } catch (\Exception $e) {
            $db->close();
            return redirect()->back()->with('error', 'Erreur lors du retrait : ' . $e->getMessage());
        }
    }

    // ===== TRANSFERT MULTIPLE =====
    public function transfer()
    {
        $redirect = $this->checkAuth();
        if ($redirect) return $redirect;

        $recipientPhones = $this->request->getPost('recipient_phones');
        $totalAmount = $this->request->getPost('amount');

        // Validation
        if (empty($recipientPhones) || !is_array($recipientPhones)) {
            return redirect()->back()->with('error', 'Veuillez saisir au moins un destinataire.');
        }

        if (!$totalAmount || !is_numeric($totalAmount) || $totalAmount <= 0) {
            return redirect()->back()->with('error', 'Montant total invalide.');
        }

        // Filtrer les numéros vides
        $recipientPhones = array_filter($recipientPhones, function($p) { return !empty(trim($p)); });
        $numRecipients = count($recipientPhones);

        if ($numRecipients === 0) {
            return redirect()->back()->with('error', 'Veuillez saisir au moins un destinataire.');
        }

        $amountFloat = (float)$totalAmount;
        // Montant divisé par nombre de destinataires
        $amountPerRecipient = $amountFloat / $numRecipients;

        $db = db_connect();
        $clientId = session()->get('client_id');

        try {
            $transferType = $db->query("SELECT id FROM operation_types WHERE code = 'TRANSFER'")->getRow();
            if (!$transferType) {
                throw new \Exception('Type d\'opération TRANSFERT introuvable.');
            }

            $senderAccount = $db->query("SELECT id, balance FROM accounts WHERE client_id = $clientId")->getRow();
            if (!$senderAccount) {
                throw new \Exception('Compte introuvable.');
            }

            // Calculer les frais sur le montant total
            $feeAmount = 0;
            $feeScale = $db->query("SELECT fee_fixed, fee_percentage FROM fee_scales 
                WHERE operation_type_id = {$transferType->id} 
                AND $amountFloat BETWEEN min_amount AND max_amount 
                LIMIT 1")->getRow();
            
            if ($feeScale) {
                $feeAmount = (float)$feeScale->fee_fixed + ($amountFloat * (float)$feeScale->fee_percentage / 100);
            }

            $totalDeduction = $amountFloat + $feeAmount;
            $balanceBefore = (float)$senderAccount->balance;

            if ($balanceBefore < $totalDeduction) {
                $db->close();
                return redirect()->back()->with('error', 'Solde insuffisant. Vous avez ' . number_format($balanceBefore, 0, ',', ' ') . ' Ar, le transfert total de ' . number_format($amountFloat, 0, ',', ' ') . ' Ar + frais ' . number_format($feeAmount, 0, ',', ' ') . ' Ar = ' . number_format($totalDeduction, 0, ',', ' ') . ' Ar.');
            }

            $balanceAfter = $balanceBefore - $totalDeduction;
            $successfulTransfers = [];
            $errors = [];

            foreach ($recipientPhones as $phone) {
                $phone = trim($phone);

                // Validation du numéro
                if (!preg_match('/^\d{10}$/', $phone)) {
                    $errors[] = "Le numéro $phone n'est pas valide (10 chiffres requis).";
                    continue;
                }

                // Vérifier que le destinataire n'est pas l'expéditeur
                if ($phone === session()->get('phone')) {
                    $errors[] = "Vous ne pouvez pas vous envoyer de l'argent à vous-même ($phone).";
                    continue;
                }

                // Trouver le destinataire
                $recipient = $db->query("SELECT id FROM clients WHERE phone = '$phone'")->getRow();
                if (!$recipient) {
                    $errors[] = "Le numéro $phone n'est pas un client enregistré.";
                    continue;
                }

                $recipientAccount = $db->query("SELECT id, balance FROM accounts WHERE client_id = {$recipient->id}")->getRow();
                if (!$recipientAccount) {
                    $errors[] = "Compte du destinataire $phone introuvable.";
                    continue;
                }

                $recipientBalanceBefore = (float)$recipientAccount->balance;
                $recipientBalanceAfter = $recipientBalanceBefore + $amountPerRecipient;

                // Insérer l'opération pour l'expéditeur
                $db->query("INSERT INTO operations (client_id, operation_type_id, amount, fee_amount, recipient_phone, recipient_client_id, balance_before, balance_after, status, created_at, include_fees) 
                    VALUES ($clientId, {$transferType->id}, $amountPerRecipient, 0, '$phone', {$recipient->id}, $balanceBefore, $balanceAfter, 'completed', datetime('now'), 0)");

                // Créditer le destinataire
                $db->query("INSERT INTO operations (client_id, operation_type_id, amount, fee_amount, balance_before, balance_after, status, created_at, include_fees) 
                    VALUES ({$recipient->id}, {$transferType->id}, $amountPerRecipient, 0, $recipientBalanceBefore, $recipientBalanceAfter, 'completed', datetime('now'), 0)");

                $db->query("UPDATE accounts SET balance = $recipientBalanceAfter, updated_at = datetime('now') WHERE client_id = {$recipient->id}");

                $successfulTransfers[] = $phone;
            }

            if (!empty($successfulTransfers)) {
                // Mettre à jour le solde de l'expéditeur seulement une fois à la fin
                // Note: On utilise le balanceAfter déjà calculé pour l'ensemble
                // Comme on a déjà inséré avec balance_before/after, on met à jour le compte
                $db->query("UPDATE accounts SET balance = $balanceAfter, updated_at = datetime('now') WHERE client_id = $clientId");
            }

            $db->close();

            $msg = '';
            if (!empty($successfulTransfers)) {
                $msg = 'Transfert de ' . number_format($amountFloat, 0, ',', ' ') . ' Ar divisé entre ' . count($successfulTransfers) . ' destinataire(s). Frais: ' . number_format($feeAmount, 0, ',', ' ') . ' Ar.';
            }
            if (!empty($errors)) {
                $msg .= ' Erreurs: ' . implode(', ', $errors);
            }

            return redirect()->to('/client')->with('success', $msg ?: 'Aucun transfert effectué.');
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
                       o.status, o.created_at, o.include_fees
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