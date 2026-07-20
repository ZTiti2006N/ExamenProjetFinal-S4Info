<?php

namespace App\Controllers;

use App\Models\FeeScaleModel;

class FeeScales extends BaseController
{
    public function index($operationTypeId = null)
    {
        $model = new FeeScaleModel();
        $data['feeScales'] = $model->where('operation_type_id', $operationTypeId)
                                    ->orderBy('min_amount', 'ASC')
                                    ->findAll();
        $data['operationTypeId'] = $operationTypeId;

        return view('fee_scales/index', $data);
    }

    public function store($operationTypeId = null)
    {
        $model = new FeeScaleModel();

        $minAmounts  = $this->request->getPost('min_amount');
        $maxAmounts  = $this->request->getPost('max_amount');
        $feeFixeds   = $this->request->getPost('fee_fixed');
        $feePercents = $this->request->getPost('fee_percentage');

        if (! $minAmounts) {
            return redirect()->back()->with('error', 'Aucune tranche à enregistrer.');
        }

        // Validation anti-chevauchement
        $ranges = [];
        foreach ($minAmounts as $index => $min) {
            $minVal = (float) $min;
            $maxVal = isset($maxAmounts[$index]) ? (float) $maxAmounts[$index] : 999999999;

            if ($minVal < 0) continue;
            if ($minVal >= $maxVal) {
                return redirect()->back()->withInput()->with('error', "Tranche " . ($index + 1) . " : le montant min doit être inférieur au montant max.");
            }
            $ranges[] = ['min' => $minVal, 'max' => $maxVal];
        }

        for ($i = 0; $i < count($ranges); $i++) {
            for ($j = $i + 1; $j < count($ranges); $j++) {
                if ($ranges[$i]['min'] < $ranges[$j]['max'] && $ranges[$j]['min'] < $ranges[$i]['max']) {
                    return redirect()->back()->withInput()->with('error', "Les tranches " . ($i + 1) . " et " . ($j + 1) . " se chevauchent.");
                }
            }
        }

        // Supprimer les anciens et insérer les nouveaux
        $model->where('operation_type_id', $operationTypeId)->delete();

        foreach ($minAmounts as $index => $minAmount) {
            $model->save([
                'operation_type_id' => $operationTypeId,
                'min_amount'        => (float) $minAmount,
                'max_amount'        => isset($maxAmounts[$index]) ? (float) $maxAmounts[$index] : 999999999,
                'fee_fixed'         => isset($feeFixeds[$index]) ? (float) $feeFixeds[$index] : 0,
                'fee_percentage'    => isset($feePercents[$index]) ? (float) $feePercents[$index] : 0,
            ]);
        }

        return redirect()->to('/operation-types/detail/' . $operationTypeId)->with('success', 'Barèmes mis à jour.');
    }

    public function deleteAll($operationTypeId = null)
    {
        $model = new FeeScaleModel();
        $model->where('operation_type_id', $operationTypeId)->delete();

        return redirect()->to('/operation-types/detail/' . $operationTypeId)->with('success', 'Tous les barèmes ont été supprimés.');
    }
}