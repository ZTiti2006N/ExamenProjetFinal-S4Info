<?php

namespace App\Controllers;

use App\Models\OperatorModel;

class Operators extends BaseController
{
    public function index()
    {
        $model = new OperatorModel();
        $data['operators'] = $model->findAll();

        // Récupérer les commissions inter-opérateurs
        $db = db_connect();
        $commissions = [];
        try {
            $commissions = $db->query("
                SELECT oc.*, op.name AS operator_name, top.name AS target_name
                FROM operator_commissions oc
                JOIN operators op ON oc.operator_id = op.id
                JOIN operators top ON oc.target_operator_id = top.id
                ORDER BY op.name, top.name
            ")->getResultArray();
        } catch (\Exception $e) { $commissions = []; }
        $data['commissions'] = $commissions;
        $db->close();

        return view('operators/index', $data);
    }

    public function create()
    {
        helper('form');

        $data = [
            'validation' => \Config\Services::validation(),
        ];

        return view('operators/create', $data);
    }

    public function store()
    {
        $rules = [
            'name'   => 'required|min_length[2]|max_length[100]',
            'prefix' => 'required|min_length[2]|max_length[10]|is_unique[operators.prefix]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new OperatorModel();
        $model->save([
            'name'           => $this->request->getPost('name'),
            'prefix'         => $this->request->getPost('prefix'),
            'other_prefixes' => $this->request->getPost('other_prefixes') ?? '',
        ]);

        return redirect()->to('/operators')->with('success', 'Opérateur ajouté avec succès.');
    }

    public function edit($id = null)
    {
        $model = new OperatorModel();
        $data['operator'] = $model->find($id);

        if (! $data['operator']) {
            return redirect()->to('/operators')->with('error', 'Opérateur introuvable.');
        }

        // Récupérer tous les opérateurs pour les commissions
        $data['all_operators'] = $model->where('id !=', $id)->findAll();

        // Récupérer les commissions existantes
        $db = db_connect();
        $commissions = [];
        try {
            $commissions = $db->query("SELECT * FROM operator_commissions WHERE operator_id = $id")->getResultArray();
        } catch (\Exception $e) { $commissions = []; }
        $data['commissions'] = $commissions;
        $db->close();

        return view('operators/edit', $data);
    }

    public function update($id = null)
    {
        $model = new OperatorModel();
        $operator = $model->find($id);

        if (! $operator) {
            return redirect()->to('/operators')->with('error', 'Opérateur introuvable.');
        }

        $rules = [
            'name'   => 'required|min_length[2]|max_length[100]',
            'prefix' => 'required|min_length[2]|max_length[10]|is_unique[operators.prefix,id,' . $id . ']',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model->update($id, [
            'name'           => $this->request->getPost('name'),
            'prefix'         => $this->request->getPost('prefix'),
            'other_prefixes' => $this->request->getPost('other_prefixes') ?? '',
        ]);

        // Gérer les commissions
        $db = db_connect();
        $targetOperators = $this->request->getPost('target_operator_id');
        $commissionsPerc = $this->request->getPost('commission_percent');

        if (!empty($targetOperators)) {
            // Supprimer les anciennes commissions
            $db->query("DELETE FROM operator_commissions WHERE operator_id = $id");
            
            foreach ($targetOperators as $i => $targetId) {
                if (!empty($targetId) && isset($commissionsPerc[$i]) && $commissionsPerc[$i] > 0) {
                    $percent = (float)$commissionsPerc[$i];
                    $db->query("INSERT INTO operator_commissions (operator_id, target_operator_id, commission_percent) 
                        VALUES ($id, $targetId, $percent)");
                }
            }
        }
        $db->close();

        return redirect()->to('/operators')->with('success', 'Opérateur modifié avec succès.');
    }

    public function delete($id = null)
    {
        $model = new OperatorModel();
        $operator = $model->find($id);

        if ($operator) {
            $db = db_connect();
            $db->query("DELETE FROM operator_commissions WHERE operator_id = $id OR target_operator_id = $id");
            $db->close();
            $model->delete($id);
        }

        return redirect()->to('/operators')->with('success', 'Opérateur supprimé.');
    }
}