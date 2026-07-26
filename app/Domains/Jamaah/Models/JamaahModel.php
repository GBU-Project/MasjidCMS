<?php

namespace App\Domains\Jamaah\Models;

use CodeIgniter\Model;

/**
 * Class JamaahModel
 *
 * CI4 Model adapter untuk tabel jamaahs.
 */
class JamaahModel extends Model
{
    protected $table            = 'jamaahs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'id',
        'member_no',
        'nik',
        'full_name',
        'gender',
        'birth_place',
        'birth_date',
        'address',
        'district',
        'city',
        'province',
        'postal_code',
        'phone',
        'email',
        'occupation',
        'education',
        'marital_status',
        'family_id',
        'status',
        'notes',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = true;
    protected $cleanValidationRules = true;
}
