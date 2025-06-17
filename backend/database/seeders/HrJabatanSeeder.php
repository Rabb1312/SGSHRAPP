<?php

namespace Database\Seeders;

use App\Models\hr_jabatan;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class HrJabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = (new hr_jabatan)->getTable();
        if (Schema::hasTable($tableName)) {
            $rowCount = hr_jabatan::count();
            if ($rowCount > 0) hr_jabatan::truncate();

            $sequence = $tableName . "_id_seq";
            DB::statement("ALTER SEQUENCE $sequence RESTART WITH 1");
        };

        $scheme = [
            [
                'jabatan' => 'BEAUTY ADVISOR', //BA/BC NIK BERAWALAN = 12
                'level_atas' => 'ACCOUNT EKSEKUTIF',
                'level_atas1' => 'ACCOUNT SUPERVISOR',
                'level_atas2' => 'ACCOUNT AREA MANAGER',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'ADMIN',
            ],
            [
                'jabatan' => 'BEAUTY CONSULTANT',
                'level_atas' => 'ACCOUNT EKSEKUTIF',
                'level_atas1' => 'ACCOUNT SUPERVISOR',
                'level_atas2' => 'ACCOUNT AREA MANAGER',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'ADMIN',
            ],
            [
                'jabatan' => 'ACCOUNT SUPERVISOR',
                'level_atas' => 'ACCOUNT AREA MANAGER',
                'level_atas1' => 'NATIONAL SALES OPERATIONAL MANAGER',
                'level_atas2' => 'GENERAL MANAGER',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'ADMIN',
            ],
            [
                'jabatan' => 'ACCOUNT AREA MANAGER',
                'level_atas' => 'NATIONAL SALES OPERATIONAL MANAGER',
                'level_atas1' => 'GENERAL MANAGER',
                'level_atas2' => 'DIRECTOR',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'ADMIN',
            ],
            [
                'jabatan' => 'FINANCE STAFF',
                'level_atas' => 'FINANCE MANAGER',
                'level_atas1' => 'CHIEF FINANCIAL OFFICER',
                'level_atas2' => 'DIRECTOR',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'ADMIN',
            ],
            [
                'jabatan' => 'MERCHANDISER',
                'level_atas' => 'ACCOUNT EKSEKUTIF',
                'level_atas1' => 'ACCOUNT SUPERVISOR',
                'level_atas2' => 'ACCOUNT AREA MANAGER',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'ADMIN',
            ],
            [
                'jabatan' => 'SALESMAN',
                'level_atas' => 'ACCOUNT EKSEKUTIF',
                'level_atas1' => 'ACCOUNT SUPERVISOR',
                'level_atas2' => 'ACCOUNT AREA MANAGER',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'ADMIN',
            ],
            [
                'jabatan' => 'DRIVER', //LOGISTIK NIK BERAWALAN = 14
                'level_atas' => 'SPV LOGISTIK',
                'level_atas1' => 'MANAGER LOGISTIK',
                'level_atas2' => 'GENERAL MANAGER',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'ADMIN',
            ],
            [
                'jabatan' => 'PICKER', //LOGISTIK NIK BERAWALAN = 14
                'level_atas' => 'SPV LOGISTIK',
                'level_atas1' => 'MANAGER LOGISTIK',
                'level_atas2' => 'GENERAL MANAGER',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'ADMIN',
            ],
            [
                'jabatan' => 'CHECKER', //LOGISTIK NIK BERAWALAN = 14
                'level_atas' => 'SPV LOGISTIK',
                'level_atas1' => 'MANAGER LOGISTIK',
                'level_atas2' => 'GENERAL MANAGER',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'ADMIN',
            ],
        ];
        hr_jabatan::insert($scheme);
    }
}