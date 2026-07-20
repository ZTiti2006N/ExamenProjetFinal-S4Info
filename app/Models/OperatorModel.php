<?php

namespace App\Models;

use CodeIgniter\Model;

class OperatorModel extends Model
{
    protected $table         = 'operators';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['name', 'prefix'];
    protected $useTimestamps = true;
    protected $useSoftDeletes = false;

    protected $validationRules = [
        'name'   => 'required|min_length[2]|max_length[100]',
        'prefix' => 'required|min_length[2]|max_length[10]|is_unique[operators.prefix]',
    ];

    protected $validationMessages = [
        'prefix' => [
            'is_unique' => 'Ce préfixe existe déjà.',
        ],
    ];
}