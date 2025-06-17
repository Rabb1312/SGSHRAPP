<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class hr_unit extends Model
{
    use SoftDeletes;
    protected $dates  = ['deleted_at'];
    protected $table = 'hr_unit';
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

        $data = hr_unit::whereRaw("
        (lower(unit_number) LIKE '%$search%' 
        OR lower(unit_code) LIKE '%$search%'
        OR lower(unit_name) LIKE '%$search%' )
        AND deleted_by IS NULL
    ")
            ->select('id', 'unit_number', 'unit_code','unit_name')
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();

        return $data;
    }
}