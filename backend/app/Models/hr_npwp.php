<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class hr_npwp extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'hr_npwp';
    protected $guarded = [];

    public function profile()
    {
        return $this->belongsTo(Profiles::class, 'profiles_id', 'profile_name');
    }

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

        return DB::table($this->table . ' as n')
            ->select(
                'n.id',
                'n.profiles_id',
                'n.npwp_no',
                'n.npwp_address'
            )
            ->whereNull('n.deleted_at')
            ->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(n.profiles_id) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(n.npwp_no) LIKE ?', ['%' . $search . '%']);
            })
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('n.id', 'ASC')
            ->get();
    }

    public function getDataByProfileName($profileName)
    {
        return DB::table($this->table)
            ->where('profiles_id', $profileName)
            ->whereNull('deleted_at')
            ->first();
    }

    public function get_total_data_($search)
    {
        $search = strtolower($search);

        return DB::table($this->table . ' as n')
            ->leftJoin('profiles as p', 'p.profile_name', '=', 'n.profiles_id')
            ->whereNull('n.deleted_at')
            ->whereNull('p.deleted_at')
            ->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(n.profiles_id) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(n.npwp_no) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(p.profile_name) LIKE ?', ['%' . $search . '%']);
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