<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Users extends Model
{
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];
    protected $table = 'users';
    
    protected $fillable = [
        'email',
        'password',
        'name',
        'role',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'remember_token'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'status' => 'boolean',
        'deleted_at' => 'datetime',
        'created_by' => 'string',  
        'updated_by' => 'string',  
        'deleted_by' => 'string' 
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getData()
    {
        $data = DB::select('SELECT * FROM ' . $this->table . " WHERE deleted_at IS NULL");
        return $data;
    }

    public function get_data_($search, $arr_pagination)
    {
        if (!empty($search)) {
            $arr_pagination['offset'] = 0;
        }

        $search = strtolower($search);

        $data = Users::whereRaw("
            (lower(email) LIKE '%$search%' 
            OR lower(name) LIKE '%$search%'
            OR lower(role) LIKE '%$search%')
            AND deleted_at IS NULL
        ")
            ->select('id', 'email', 'name', 'role', 'status')
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();

        return $data;
    }
}