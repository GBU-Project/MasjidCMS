<?php

namespace App\Models;

use CodeIgniter\Model;

class PengurusModel extends Model
{
    protected $table            = 'pengurus';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'bidang_id', 'nama', 'jabatan', 'foto', 'jenis_kelamin',
        'telepon', 'email', 'alamat', 'bio', 'periode_mulai',
        'periode_selesai', 'urutan', 'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'nama'    => 'required|min_length[3]',
        'jabatan' => 'required',
    ];

    public function getPengurusWithBidang()
    {
        return $this->select('pengurus.*, bidang.name as bidang_name')
                    ->join('bidang', 'bidang.id = pengurus.bidang_id', 'left')
                    ->orderBy('pengurus.urutan', 'ASC')
                    ->findAll();
    }
}
