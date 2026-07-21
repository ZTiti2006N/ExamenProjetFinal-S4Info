<?php

namespace App\Controllers;

use App\Models\OperatorModel;
use App\Models\ClientModel;

class Auth extends BaseController
{
    public function login()
    {
        // If already logged in, go to client dashboard
        if (session()->get('logged_in')) {
            return redirect()->to('/client');
        }

        return view('auth/login');
    }

    public function authenticate()
    {
        $phone = $this->request->getPost('phone');

        // Validation: required, exactly 10 digits, numeric
        $rules = [
            'phone' => 'required|numeric|exact_length[10]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Le numéro de téléphone doit contenir exactement 10 chiffres.');
        }

        // Vérifier le préfixe (3 premiers chiffres) - inclut les préfixes additionnels
        $prefix = substr($phone, 0, 3);
        $opModel = new OperatorModel();
        $operator = $opModel->where('prefix', $prefix)->first();

        // Si pas trouvé avec le préfixe principal, vérifier dans other_prefixes
        if (!$operator) {
            $allOperators = $opModel->findAll();
            foreach ($allOperators as $op) {
                $otherPrefixes = array_map('trim', explode(',', $op['other_prefixes'] ?? ''));
                if (in_array($prefix, $otherPrefixes)) {
                    $operator = $op;
                    break;
                }
            }
        }

        if (! $operator) {
            return redirect()->back()->withInput()->with('error', 'Aucun opérateur trouvé pour le préfixe "' . $prefix . '". Veuillez vérifier votre numéro.');
        }

        // Vérifier si le client existe déjà
        $clientModel = new ClientModel();
        $client = $clientModel->where('phone', $phone)->first();

        if (! $client) {
            // Auto-création du client
            // Extraire le nom à partir du numéro (ex: "033*******")
            $lastName = 'Client-' . substr($phone, 0, 3);
            $firstName = substr($phone, 3);

            $clientId = $clientModel->insert([
                'phone'       => $phone,
                'first_name'  => $firstName,
                'last_name'   => $lastName,
                'operator_id' => $operator['id'],
            ], true);

            // Auto-création du compte avec solde 0
            $db = db_connect();
            $db->query("INSERT INTO accounts (client_id, balance) VALUES ($clientId, 0)");
            $db->close();

            // Recharger le client
            $client = $clientModel->find($clientId);
        }

        // Connecter l'utilisateur en session
        session()->set([
            'logged_in'   => true,
            'client_id'   => $client['id'],
            'phone'       => $client['phone'],
            'first_name'  => $client['first_name'],
            'last_name'   => $client['last_name'],
        ]);

        return redirect()->to('/client')->with('success', 'Bienvenue ' . $client['first_name'] . ' !');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
    public function promotion()
    {
      if $client = session()->get('client_id')
      {
          // Logique pour afficher la promotion pour le client connecté
          return view('auth/promotion');
      } else {
        return redirect()->to('/login')->with('error', 'Veuillez vous connecter pour voir les promotions.');   
      }   
    }
    

}