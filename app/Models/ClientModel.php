<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table         = 'clients';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['phone', 'first_name', 'last_name', 'operator_id'];
    protected $useTimestamps = true;
    protected $useSoftDeletes = false;

    protected $validationRules = [
        'phone'       => 'required|numeric|exact_length[10]|is_unique[clients.phone]',
        'first_name'  => 'required|min_length[1]|max_length[100]',
        'last_name'   => 'required|min_length[1]|max_length[100]',
        'operator_id' => 'required|is_natural_no_zero',
    ];

    protected $validationMessages = [
        'phone' => [
            'is_unique' => 'Ce numéro de téléphone existe déjà.',
        ],
    ];
}