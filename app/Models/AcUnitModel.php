<?php

namespace App\Models;

use CodeIgniter\Model;

class AcUnitModel extends Model
{
    protected $table            = 'ac_units';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    // Kolom yang bisa diisi
    protected $allowedFields    = [
        'token',        // <— baru: untuk /ac/{token}
        'kode_qr',
        'nomor_unik',
        'tipe_model',
        'kapasitas_btu',
        'lokasi',
        'status_ac',
        'catatan',
        'foto_path',    // <— baru: path foto
        'created_at','updated_at'
    ];

    // created_at/updated_at dihandle DB
    protected $useTimestamps = false;

    // Longgarkan rules agar form QR yang sekarang tetap lolos
    protected $validationRules = [
        'token'         => 'permit_empty|min_length[8]|max_length[64]',
        'kode_qr'       => 'permit_empty|min_length[3]|max_length[64]',
        'nomor_unik'    => 'permit_empty|min_length[2]|max_length[64]',
        'tipe_model'    => 'permit_empty|max_length[120]',
        'kapasitas_btu' => 'permit_empty|integer|greater_than[0]',
        'lokasi'        => 'permit_empty|max_length[120]',
        'status_ac'     => 'permit_empty|in_list[NORMAL,MENUNGGU_PERBAIKAN,DALAM_PERBAIKAN]',
        'catatan'       => 'permit_empty|string',
        'foto_path'     => 'permit_empty|max_length[255]',
    ];
}
    