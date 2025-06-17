<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class profiles extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'users_id',
        'cabang_id',
        'kode_cabang',
        'kode_lokasi',
        'jabatan_id',
        'unit_id',
        'nik',
        'profile_name',
        'profile_gender',
        'profile_address',
        'postalcode',
        'email',
        'profile_phones',
        'profile_phones2',
        'identity_number',
        'birthplace',
        'birthdate',
        'marital_status',
        'nama_ibu',
        'nama_ayah',
        'nama_pasangan',
        'nama_anak',
        'blood_type',
        'join_date',
        'religion',
        'education_status',
        'is_active',
        'created_by',
        'updated_by'
    ];
    protected $dates = ['deleted_at', 'birthdate', 'join_date'];
    protected $table = 'profiles';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($profile) {
            // Mengambil ID terakhir + 1 untuk dijadikan users_id
            $lastId = self::withTrashed()->max('id');
            $nextId = $lastId ? $lastId + 1 : 1;
            $profile->users_id = (string)$nextId;
        });
    }

    protected $attributes = [
        'profile_phones2' => null,
        'nama_ayah' => null,
        'nama_pasangan' => null,
        'nama_anak' => null,
        'nik' => null
    ];

    protected $casts = [
        'birthdate' => 'date:Y-m-d',
        'join_date' => 'date:Y-m-d',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getIdentityNumberAttribute($value)
    {
        return is_numeric($value) ? (string) number_format($value, 0, '.', '') : $value;
    }

    // Tambahkan mutator untuk menghandle scientific notation
    public function setIdentityNumberAttribute($value)
    {
        $this->attributes['identity_number'] = is_numeric($value) ?
            (string) number_format($value, 0, '.', '') :
            $value;
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

        $data = profiles::leftJoin('hr_npwp', function ($join) {
            $join->on('profiles.users_id', '=', 'hr_npwp.profiles_id')
                ->whereNull('hr_npwp.deleted_at');
        })
            ->leftJoin('hr_bpjs', function ($join) {
                $join->on('profiles.users_id', '=', 'hr_bpjs.profiles_id')
                    ->whereNull('hr_bpjs.deleted_at');
            })
            ->whereRaw("
       (lower(profiles.users_id) LIKE '%$search%' 
       OR lower(profiles.cabang_id) LIKE '%$search%'
       OR lower(profiles.jabatan_id) LIKE '%$search%'
       OR lower(profiles.unit_id) LIKE '%$search%'
       OR lower(profiles.nik) LIKE '%$search%'
       OR lower(profiles.profile_name) LIKE '%$search%'
       OR lower(profiles.profile_gender) LIKE '%$search%'
       OR lower(profiles.profile_address) LIKE '%$search%'
       OR lower(profiles.postalcode) LIKE '%$search%'
       OR lower(profiles.email) LIKE '%$search%'
       OR lower(profiles.profile_phones) LIKE '%$search%'
       OR lower(profiles.profile_phones2) LIKE '%$search%'
       OR lower(profiles.identity_number) LIKE '%$search%'
       OR lower(profiles.birthplace) LIKE '%$search%'
       OR lower(profiles.birthdate) LIKE '%$search%'
       OR lower(profiles.marital_status) LIKE '%$search%'
       OR lower(profiles.nama_ayah) LIKE '%$search%'
       OR lower(profiles.nama_ibu) LIKE '%$search%'
       OR lower(profiles.nama_pasangan) LIKE '%$search%'
       OR lower(profiles.nama_anak) LIKE '%$search%'
       OR lower(profiles.blood_type) LIKE '%$search%'
       OR lower(profiles.join_date) LIKE '%$search%'
       OR lower(profiles.religion) LIKE '%$search%'
       OR lower(profiles.education_status) LIKE '%$search%'
       OR lower(profiles.is_active) LIKE '%$search%' )
       AND profiles.deleted_by IS NULL
   ")
            ->select(
                'profiles.id',
                'profiles.users_id',
                'profiles.cabang_id',
                'profiles.jabatan_id',
                'profiles.unit_id',
                'profiles.nik',
                'profiles.profile_name',
                'profiles.profile_gender',
                'profiles.profile_address',
                'profiles.postalcode',
                'profiles.email',
                'profiles.profile_phones',
                'profiles.profile_phones2',
                'profiles.identity_number',
                'profiles.birthplace',
                'profiles.birthdate',
                'profiles.marital_status',
                'profiles.nama_ayah',
                'profiles.nama_ibu',
                'profiles.nama_pasangan',
                'profiles.nama_anak',
                'profiles.blood_type',
                'profiles.join_date',
                'profiles.religion',
                'profiles.education_status',
                'profiles.is_active',
                'hr_npwp.npwp_no',
                'hr_bpjs.bpjs_no'
            )
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('profiles.id', 'ASC')
            ->get();

            return [
                'results' => $data,
                'offset' => $arr_pagination['offset'] 
            ];
    }

    public function npwp()
    {
        return $this->hasOne(hr_npwp::class, 'profiles_id', 'profile_name');
    }
}