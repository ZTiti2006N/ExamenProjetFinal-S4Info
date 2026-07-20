<?php

namespace App\Controllers;

use App\Models\OperationTypeModel;
use App\Models\FeeScaleModel;

class OperationTypes extends BaseController
{
    // -------------------------------------------------
    // LISTE des types d'opérations (sans les barèmes)
    // -------------------------------------------------
    public function index()
    {
        $model = new OperationTypeModel();
        $data['operationTypes'] = $model->findAll();

        return view('operation_types/index', $data);
    }

    // -------------------------------------------------
    // AJOUTER un type d'opération (avec détection des doublons)
    // -------------------------------------------------
    public function create()
    {
        $otModel = new OperationTypeModel();
        $existingCodes = $otModel->select('code')->findAll();
        $data['existingCodes'] = array_column($existingCodes, 'code');

        return view('operation_types/create', $data);
    }

    public function store()
    {
        $model = new OperationTypeModel();
        $feeModel = new FeeScaleModel();

        $rules = [
            'code'  => 'required|min_length[2]|max_length[20]|is_unique[operation_types.code]',
            'label' => 'required|min_length[2]|max_length[100]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model->save([
            'code'     => $this->request->getPost('code'),
            'label'    => $this->request->getPost('label'),
            'has_fees' => $this->request->getPost('has_fees') ? 1 : 0,
        ]);

        // Enregistrer les barèmes de frais
        $operationTypeId = $model->getInsertID();
        $minAmounts  = $this->request->getPost('min_amount');
        $maxAmounts  = $this->request->getPost('max_amount');
        $feeFixeds   = $this->request->getPost('fee_fixed');

        if ($minAmounts) {
            foreach ($minAmounts as $index => $minAmount) {
                if ($minAmount === '' || $minAmount === null) continue;
                $feeModel->save([
                    'operation_type_id' => $operationTypeId,
                    'min_amount'        => (float) $minAmount,
                    'max_amount'        => isset($maxAmounts[$index]) && $maxAmounts[$index] !== '' ? (float) $maxAmounts[$index] : 999999999,
                    'fee_fixed'         => isset($feeFixeds[$index]) && $feeFixeds[$index] !== '' ? (float) $feeFixeds[$index] : 0,
                ]);
            }
        }

        return redirect()->to('/operation-types')->with('success', 'Type d\'opération ajouté avec succès.');
    }

    // -------------------------------------------------
    // MODIFIER un type d'opération (avec barèmes)
    // -------------------------------------------------
    public function edit($id = null)
    {
        $model = new OperationTypeModel();
        $feeModel = new FeeScaleModel();
        $data['operationType'] = $model->find($id);

        if (! $data['operationType']) {
            return redirect()->to('/operation-types')->with('error', 'Type d\'opération introuvable.');
        }

        $data['feeScales'] = $feeModel->where('operation_type_id', $id)->orderBy('min_amount', 'ASC')->findAll();

        return view('operation_types/edit', $data);
    }

    public function update($id = null)
    {
        $model = new OperationTypeModel();
        $feeModel = new FeeScaleModel();
        $operationType = $model->find($id);

        if (! $operationType) {
            return redirect()->to('/operation-types')->with('error', 'Type d\'opération introuvable.');
        }

        $rules = [
            'code'  => 'required|min_length[2]|max_length[20]|is_unique[operation_types.code,id,' . $id . ']',
            'label' => 'required|min_length[2]|max_length[100]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model->update($id, [
            'code'     => $this->request->getPost('code'),
            'label'    => $this->request->getPost('label'),
            'has_fees' => $this->request->getPost('has_fees') ? 1 : 0,
        ]);

        // Mettre à jour les barèmes : supprimer les anciens et insérer les nouveaux
        $minAmounts  = $this->request->getPost('min_amount');
        $maxAmounts  = $this->request->getPost('max_amount');
        $feeFixeds   = $this->request->getPost('fee_fixed');

        if ($minAmounts && count(array_filter($minAmounts, fn($v) => $v !== '' && $v !== null)) > 0) {
            // Supprimer les anciens barèmes
            $feeModel->where('operation_type_id', $id)->delete();

            foreach ($minAmounts as $index => $minAmount) {
                if ($minAmount === '' || $minAmount === null) continue;
                $feeModel->save([
                    'operation_type_id' => $id,
                    'min_amount'        => (float) $minAmount,
                    'max_amount'        => isset($maxAmounts[$index]) && $maxAmounts[$index] !== '' ? (float) $maxAmounts[$index] : 999999999,
                    'fee_fixed'         => isset($feeFixeds[$index]) && $feeFixeds[$index] !== '' ? (float) $feeFixeds[$index] : 0,
                ]);
            }
        }

        return redirect()->to('/operation-types')->with('success', 'Type d\'opération modifié avec succès.');
    }

    // -------------------------------------------------
    // SUPPRIMER (cascade : barèmes + type)
    // -------------------------------------------------
    public function delete($id = null)
    {
        $otModel = new OperationTypeModel();
        $operationType = $otModel->find($id);

        if ($operationType) {
            // Supprimer les barèmes associés
            $feeModel = new FeeScaleModel();
            $feeModel->where('operation_type_id', $id)->delete();

            // Supprimer le type d'opération
            $otModel->delete($id);
        }

        return redirect()->to('/operation-types')->with('success', 'Type d\'opération et ses barèmes supprimés.');
    }

    // -------------------------------------------------
    // DÉTAIL d'un type d'opération (gestion des barèmes)
    // -------------------------------------------------
    public function detail($id = null)
    {
        $otModel  = new OperationTypeModel();
        $feeModel = new FeeScaleModel();

        $operationType = $otModel->find($id);

        if (! $operationType) {
            return redirect()->to('/operation-types')->with('error', 'Type d\'opération introuvable.');
        }

        $data['operationType'] = $operationType;
        $data['feeScales']     = $feeModel->where('operation_type_id', $id)->orderBy('min_amount', 'ASC')->findAll();

        return view('operation_types/detail', $data);
    }
}
