<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class hr_kontrak extends Model
{
    use SoftDeletes;
    protected $dates  = ['deleted_at'];
    protected $table = 'hr_kontrak';
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

    public function get_data_($search, $arr_pagination, $cabang_id = null) // Tambahkan parameter cabang_id
{
    if (!empty($search)) {
        $arr_pagination['offset'] = 0;
    }

    $search = strtolower($search);

    $query = hr_kontrak::where(function ($query) use ($search) {
        $query->whereRaw("LOWER(profiles_id) LIKE ?", ['%' . $search . '%'])
            ->orWhereRaw("LOWER(cabang_id) LIKE ?", ['%' . $search . '%'])
            ->orWhereRaw("LOWER(no_kontrak) LIKE ?", ['%' . $search . '%'])
            ->orWhereRaw("LOWER(tipe) LIKE ?", ['%' . $search . '%'])
            ->orWhereRaw("CAST(tgl_masuk AS TEXT) LIKE ?", ['%' . $search . '%'])
            ->orWhereRaw("CAST(tgl_keluar AS TEXT) LIKE ?", ['%' . $search . '%'])
            ->orWhere('tahun', 'LIKE', "%$search%")  
            ->orWhere('bulan', 'LIKE', "%$search%")  
            ->orWhere('hari', 'LIKE', "%$search%")   
            ->orWhereRaw("CAST(tgl_take_out AS TEXT) LIKE ?", ['%' . $search . '%'])
            ->orWhereRaw("CAST(tgl_make_kontrak AS TEXT) LIKE ?", ['%' . $search . '%'])
            ->orWhereRaw("LOWER(ket) LIKE ?", ['%' . $search . '%'])
            ->orWhereRaw("LOWER(ket_take_out) LIKE ?", ['%' . $search . '%']);
    })
        ->whereNull('deleted_at');

    // Tambahkan filter cabang_id jika ada
    if ($cabang_id) {
        $query->where('cabang_id', $cabang_id);
    }

    $data = $query->select('id', 'profiles_id', 'cabang_id', 'no_kontrak', 'tipe', 'tgl_masuk', 'tgl_keluar', 'tahun', 'bulan', 'hari', 'tgl_take_out','tgl_make_kontrak', 'ket','ket_take_out')
        ->offset($arr_pagination['offset'])
        ->limit($arr_pagination['limit'])
        ->orderBy('id', 'DESC')
        ->get();

    return $data;
}

    public function profiles() {
        return $this->hasOne(profiles::class,'id', 'profiles_id');
    }
}