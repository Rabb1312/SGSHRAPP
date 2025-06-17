<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MstKriteriaRapor extends Model
{
    use SoftDeletes;

    protected $table = 'mst_kriteria_rapor';
    protected $fillable = ['kriteria', 'is_active', 'created_by', 'updated_by'];

    public function details()
    {
        return $this->hasMany(MstDtlKriteriaRapor::class, 'kriteria_id', 'id');
    }
}