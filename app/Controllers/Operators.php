<?php

namespace App\Controllers;

use App\Models\OperatorModel;

class Operators extends BaseController
{
    public function index()
    {
        $model = new OperatorModel();
        $data['operators'] = $model->findAll();

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
            'name'   => $this->request->getPost('name'),
            'prefix' => $this->request->getPost('prefix'),
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
            'name'   => $this->request->getPost('name'),
            'prefix' => $this->request->getPost('prefix'),
        ]);

        return redirect()->to('/operators')->with('success', 'Opérateur modifié avec succès.');
    }

    public function delete($id = null)
    {
        $model = new OperatorModel();
        $operator = $model->find($id);

        if ($operator) {
            $model->delete($id);
        }

        return redirect()->to('/operators')->with('success', 'Opérateur supprimé.');
    }
}