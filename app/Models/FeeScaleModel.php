<?php

namespace App\Models;

use CodeIgniter\Model;

class FeeScaleModel extends Model
{
    protected $table         = 'fee_scales';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['operation_type_id', 'min_amount', 'max_amount', 'fee_fixed', 'fee_percentage'];
    protected $useTimestamps = true;
    protected $useSoftDeletes = false;

    protected $validationRules = [
        'operation_type_id' => 'required|is_natural_no_zero',
        'min_amount'        => 'required|numeric|greater_than_equal_to[0]',
        'max_amount'        => 'required|numeric|greater_than_equal_to[0]',
        'fee_fixed'         => 'required|numeric|greater_than_equal_to[0]',
    ];
}