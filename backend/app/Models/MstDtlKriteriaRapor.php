<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MstDtlKriteriaRapor extends Model
{
    use SoftDeletes;

    protected $table = 'mst_dtl_kriteria_rapor';

    protected $fillable = [
        'jabatan_id',
        'kriteria_id',
        'is_active', 
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $dates = ['deleted_at']; 

    protected $casts = [
        'is_active' => 'string'
    ];

    // Relasi ke tabel jabatan
    public function jabatan()
    {
        return $this->belongsTo(hr_jabatan::class, 'jabatan_id', 'id');
    }

    // Relasi ke tabel kriteria - Perbaiki foreign key dan local key
    public function kriteria()
    {
        return $this->belongsTo(MstKriteriaRapor::class, 'kriteria_id', 'id');
    }
}