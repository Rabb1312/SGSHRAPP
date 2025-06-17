<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class hr_jabatan extends Model
{
    use SoftDeletes;
    protected $dates  = ['deleted_at'];
    protected $table = 'hr_jabatan';
    protected $guarded = [];
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getData()
    {
        $data = DB::select('SELECT * FROM ' . $this->table . " WHERE  deleted_at IS NULL");
        return $data;
    }

    public function get_data_($search, $arr_pagination)
    {
        if (!empty($search)) {
            $arr_pagination['offset'] = 0;
        }

        $search = strtolower($search);

        $data = hr_jabatan::whereRaw("
        (lower(jabatan) LIKE '%$search%' 
        OR lower(level_atas) LIKE '%$search%' 
        OR lower(level_atas1) LIKE '%$search%' 
        OR lower(level_atas2) LIKE '%$search%' )
        AND deleted_by IS NULL
    ")
            ->select('id', 'jabatan', 'level_atas', 'level_atas1', 'level_atas2')
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();

        return $data;
    }

    public function details()
    {
        return $this->hasMany(MstDtlKriteriaRapor::class, 'jabatan_id', 'id');
    }
}