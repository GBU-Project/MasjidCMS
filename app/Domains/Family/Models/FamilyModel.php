<?php

namespace App\Domains\Family\Models;

use CodeIgniter\Model;

/**
 * Class FamilyModel
 *
 * CI4 Model adapter untuk tabel families.
 */
class FamilyModel extends Model
{
    protected $table            = 'families';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'id',
        'family_no',
        'kk_number',
        'name',
        'head_jamaah_id',
        'address',
        'district',
        'city',
        'province',
        'postal_code',
        'family_status',
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
