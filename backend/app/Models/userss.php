<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class users extends Model
{
    use SoftDeletes;
    protected $dates  = ['deleted_at'];
    protected $table = 'users';
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

        $data = users::whereRaw("
        (lower(email) LIKE '%$search%' 
        OR lower(username) LIKE '%$search%' 
        OR lower(pwd_hash) LIKE '%$search%' 
        OR lower(status) LIKE '%$search%' 
        OR lower(verified_token) LIKE '%$search%' 
        OR lower(pwd_changed) LIKE '%$search%' )
        AND deleted_by IS NULL
    ")
            ->select('id','email','username', 'pwd_hash','status','verified_token','pwd_changed') 
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();

        return $data;
    }
}