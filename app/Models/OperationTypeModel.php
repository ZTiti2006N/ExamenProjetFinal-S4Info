<?php

namespace App\Models;

use CodeIgniter\Model;

class OperationTypeModel extends Model
{
    protected $table         = 'operation_types';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['code', 'label', 'has_fees'];
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;

    protected $validationRules = [
        'code'  => 'required|min_length[2]|max_length[20]|is_unique[operation_types.code]',
        'label' => 'required|min_length[2]|max_length[100]',
    ];

    protected $validationMessages = [
        'code' => [
            'is_unique' => 'Ce code existe déjà.',
        ],
    ];
}