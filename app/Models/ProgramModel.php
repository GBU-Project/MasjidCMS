<?php

namespace App\Models;

use CodeIgniter\Model;

class ProgramModel extends Model
{
    protected $table            = 'program_kegiatan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'bidang_id', 'nama', 'slug', 'ringkasan', 'deskripsi',
        'gambar', 'penanggung_jawab', 'tanggal_mulai', 'tanggal_selesai',
        'lokasi', 'status', 'featured'
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

    public function getActivePrograms()
    {
        return $this->select('program_kegiatan.*, bidang.name as bidang_name')
                    ->join('bidang', 'bidang.id = program_kegiatan.bidang_id', 'left')
                    ->where('program_kegiatan.status', 'ACTIVE')
                    ->orderBy('program_kegiatan.created_at', 'DESC')
                    ->findAll();
    }
}
