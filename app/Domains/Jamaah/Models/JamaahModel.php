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
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'code',
        'name',
        'slug',
        'status',
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
