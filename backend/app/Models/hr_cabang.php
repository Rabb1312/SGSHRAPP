<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class hr_cabang extends Model
{
    use SoftDeletes;
    protected $dates  = ['deleted_at'];
    protected $table = 'hr_cabang';
    protected $guarded = [];
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getData()
    {
        $data = hr_cabang::with('kodeLokasi') // Menggunakan relasi yang didefinisikan
            ->whereNull('deleted_at')
            ->get();
        return $data;
    }



    public function get_data_($search, $arr_pagination)
    {
        $data = hr_cabang::with('kodeLokasi')
            ->where(function ($query) use ($search) {
                $query->where(DB::raw('lower(kode_cabang)'), 'LIKE', "%$search%")
                    ->orWhere(DB::raw('lower(nama_cabang)'), 'LIKE', "%$search%");
            })
            ->whereNull('deleted_by')
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();

        return $data;
    }



    public function kodeLokasi()
    {
        return $this->hasOne(hr_kode_lokasi::class, 'kode_cabang', 'kode_cabang');
    }
}