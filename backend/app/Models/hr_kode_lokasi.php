<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class hr_kode_lokasi extends Model
{
    use SoftDeletes;
    protected $dates  = ['deleted_at'];
    protected $table = 'hr_kode_lokasi';
    protected $guarded = [];
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getData()
    {
        $data = hr_kode_lokasi::whereNull('deleted_at')
            ->get();
        return $data;
    }


    public function get_data_($search, $arr_pagination)
    {
        $data = hr_kode_lokasi::where(function ($query) use ($search) {
            $query->where(DB::raw('lower(kode_cabang)'), 'LIKE', "%$search%")
                ->orWhere(DB::raw('lower(nama_cabang)'), 'LIKE', "%$search%")
                ->orWhere(DB::raw('lower(kode_lokasi)'), 'LIKE', "%$search%");
        })
            ->whereNull('deleted_by')
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();

        return $data;
    }


    public function cabang()
    {
        return $this->belongsTo(hr_cabang::class, 'kode_cabang', 'kode_cabang');
    }
}