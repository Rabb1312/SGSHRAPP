<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class hr_organisasi extends Model
{
    use SoftDeletes;
    protected $dates  = ['deleted_at'];
    protected $table = 'hr_organisasi';
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

        $data = hr_organisasi::whereRaw("
        (lower(supplier_name) LIKE '%$search%' 
        OR lower(supplier_address) LIKE '%$search%' 
        OR lower(supplier_phone) LIKE '%$search%' 
        OR lower(supplier_city) LIKE '%$search%' 
        OR lower(supplier_postalcode) LIKE '%$search%' )
        AND deleted_by IS NULL
    ")
            ->select('id', 'supplier_name', 'supplier_address','supplier_phone','supplier_city','supplier_postalcode')
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();

        return $data;
    }
}