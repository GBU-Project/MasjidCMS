<?php

namespace App\Domains\Masjid\Models;

use CodeIgniter\Model;

/**
 * Class MasjidModel
 *
 * CI4 Model adapter untuk tabel masjids.
 * Berfungsi sebagai metadata adapter untuk BaseRepository.
 */
class MasjidModel extends Model
{
    protected $table            = 'masjids';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'code',
        'name',
        'slug',
        'type',
        'email',
        'phone',
        'website',
        'address',
        'district',
        'city',
        'province',
        'postal_code',
        'latitude',
        'longitude',
        'timezone',
        'logo_media_id',
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
