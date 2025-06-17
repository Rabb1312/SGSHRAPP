<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class hr_bpjs extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'hr_bpjs';
    protected $guarded = [];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function get_data_($search, $arr_pagination)
    {
        if (!empty($search)) {
            $arr_pagination['offset'] = 0;
        }

        $search = strtolower($search);

        return DB::table($this->table . ' as b')
            ->select(
                'b.id',
                'b.profiles_id',
                'b.bpjs_no',
                'b.bpjs_ket'
            )
            ->whereNull('b.deleted_at')
            ->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(b.profiles_id) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(b.bpjs_no) LIKE ?', ['%' . $search . '%']);
            })
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('b.id', 'ASC')
            ->get();
    }

    public function get_total_data_($search)
    {
        $search = strtolower($search);

        return DB::table($this->table . ' as n')
            ->whereNull('n.deleted_at')
            ->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(n.profiles_id) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(n.bpjs_no) LIKE ?', ['%' . $search . '%']);
            })
            ->count();
    }

    public function getDataById($id)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();
    }
}