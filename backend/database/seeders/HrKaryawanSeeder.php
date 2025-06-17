<?php

namespace Database\Seeders;

use App\Models\profiles;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class HrKaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */


    public function run(): void
    {
        $tableName = (new profiles)->getTable();
        if (Schema::hasTable($tableName)) {
            $rowCount = profiles::count();
            if ($rowCount > 0) profiles::truncate();

            $sequence = $tableName . "_id_seq";
            DB::statement("ALTER SEQUENCE $sequence RESTART WITH 1");
        };

        $scheme = [
            [
                'users_id' => '1',
                'cabang_id' => 'BANDUNG',
                'jabatan_id' => 'BEAUTY ADVISOR',
                'unit_id' => 'CREATIVE STYLE',
                'nik' => '31750413',
                'profile_name' => 'AMAR',
                'profile_gender' => 'Laki-laki',
                'profile_address' => 'Jalan Raya',
                'postalcode' => '13540',
                'profile_phones' => '0812345678',
                'profile_phones2' => '0987766544',
                'identity_number' => '1234567',
                'birthplace' => 'JAKARTA',
                'birthdate' => '2000-12-13',
                'marital_status' => 'MENIKAH',
                'nama_ayah' => 'AYAH',
                'nama_ibu' => 'IBU',
                'nama_pasangan' => 'PASANGAN',
                'nama_anak' => 'ANAK',
                'blood_type' => 'O',
                'join_date' => '2000-12-13',
                'religion' => 'ISLAM',
                'education_status' => 'SD',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
        ];
        profiles::insert($scheme);
    }
}