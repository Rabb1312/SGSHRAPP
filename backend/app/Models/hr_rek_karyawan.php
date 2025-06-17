<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class hr_rek_karyawan extends Model
{
    use SoftDeletes;
    protected $dates  = ['deleted_at'];
    protected $table = 'hr_rek_karyawan';
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

        return DB::table($this->table . ' as r')
            ->select(
                'r.id',
                'r.profiles_id',
                'r.bank_id',
                'r.karyawan_rek',
                'r.karyawan_rek_kcp',
                'r.karyawan_name_rek'
            )
            ->whereNull('r.deleted_at')
            ->where(function($query) use ($search) {
                $query->whereRaw('LOWER(r.profiles_id) LIKE ?', ['%'.$search.'%'])
                      ->orWhereRaw('LOWER(r.karyawan_rek) LIKE ?', ['%'.$search.'%']);
            })
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('r.id', 'ASC')
            ->get();
    }
}