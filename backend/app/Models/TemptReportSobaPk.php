<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class TemptReportSobaPk extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cabang_id',
        'profiles_id',
        'mop',
        'yop',
        'nama_toko',
        'channel',
        'tb',
        'bb',
        'bmi',
        'klasifikasi',
        'pk',
        'target_siba',
        'target_soba',
        'siba',
        'soba',
        'achv_siba',
        'achv_soba',
        'created_by',
        'updated_by'
    ];

    protected $dates = ['deleted_at'];
    protected $table = 'tempt_report_soba_pk';
    protected $guarded = [];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function profile()
    {
        return $this->belongsTo(profiles::class, 'profiles_id', 'profile_name');
    }

    public function cabang()
    {
        return $this->belongsTo(hr_cabang::class, 'cabang_id', 'nama_cabang');
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

        $data = TemptReportSobaPk::with(['profile', 'cabang'])
            ->leftJoin('profiles', 'tempt_report_soba_pk.profiles_id', '=', 'profiles.profile_name') // Join dengan profile_name
            ->leftJoin('hr_cabang', 'tempt_report_soba_pk.cabang_id', '=', 'hr_cabang.nama_cabang') // Join dengan nama_cabang
            ->select(
                'tempt_report_soba_pk.*',
                'profiles.join_date',
                'profiles.profile_name',
                'hr_cabang.nama_cabang'
            )
            ->whereRaw("
            (lower(tempt_report_soba_pk.cabang_id) LIKE '%$search%'
            OR lower(tempt_report_soba_pk.profiles_id) LIKE '%$search%' 
            OR lower(tempt_report_soba_pk.mop) LIKE '%$search%' 
            OR lower(tempt_report_soba_pk.yop) LIKE '%$search%' 
            OR lower(tempt_report_soba_pk.nama_toko) LIKE '%$search%'
            OR lower(tempt_report_soba_pk.channel) LIKE '%$search%'
            OR lower(tempt_report_soba_pk.tb) LIKE '%$search%'
            OR lower(tempt_report_soba_pk.bb) LIKE '%$search%'
            OR lower(tempt_report_soba_pk.bmi) LIKE '%$search%'
            OR lower(tempt_report_soba_pk.klasifikasi) LIKE '%$search%'
            OR lower(tempt_report_soba_pk.pk) LIKE '%$search%'
            OR lower(tempt_report_soba_pk.target_siba) LIKE '%$search%'
            OR lower(tempt_report_soba_pk.target_soba) LIKE '%$search%'
            OR lower(tempt_report_soba_pk.siba) LIKE '%$search%' 
            OR lower(tempt_report_soba_pk.soba) LIKE '%$search%'
            OR lower(tempt_report_soba_pk.achv_siba) LIKE '%$search%'
            OR lower(tempt_report_soba_pk.achv_soba) LIKE '%$search%')
            AND tempt_report_soba_pk.deleted_by IS NULL
        ")
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('tempt_report_soba_pk.id', 'ASC')
            ->get();

        return $data;
    }
}
