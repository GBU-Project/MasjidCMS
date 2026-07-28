<?php

namespace App\Models;

use CodeIgniter\Model;

class LayananModel extends Model
{
    protected $table            = 'layanan_masjid';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'nama', 'slug', 'icon', 'gambar', 'deskripsi',
        'persyaratan', 'jam_layanan', 'kontak', 'lokasi',
        'status', 'urutan'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'nama' => 'required|min_length[3]',
        'slug' => 'required',
    ];

    public function getActiveServices()
    {
        return $this->where('status', 'ACTIVE')
                    ->orderBy('urutan', 'ASC')
                    ->findAll();
    }
}
