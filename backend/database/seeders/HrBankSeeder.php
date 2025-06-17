<?php

namespace Database\Seeders;

use App\Models\hr_bank;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class HrBankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = (new hr_bank) -> getTable();
        if (Schema::hasTable($tableName)){
            $rowCount = hr_bank :: count ();
            if ($rowCount > 0) hr_bank ::truncate();
            
            $sequence = $tableName . "_id_seq";
            DB :: statement("ALTER SEQUENCE $sequence RESTART WITH 1");
        };
        $scheme = [
            [
               
                'bank_code' => 'CIMB',
                'bank_name' => 'CIMB NIAGA',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                
                'bank_code' => 'BCA',
                'bank_name' => 'BANK CENTRAL ASIA',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                
                'bank_code' => 'BNI',
                'bank_name' => 'BANK NASIONAL INDONESIA',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                
                'bank_code' => 'BRI',
                'bank_name' => 'BANK RAKYAT INDONESIA',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                
                'bank_code' => 'NBC',
                'bank_name' => 'NEO BANK COMMERCE',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                
                'bank_code' => 'MDR',
                'bank_name' => 'MANDIRI',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                
                'bank_code' => 'BSI',
                'bank_name' => 'BANK SYARIAH INDONESIA',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
            
                'bank_code' => 'BTN',
                'bank_name' => 'BANK TABUNGAN NEGARA',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
            
                'bank_code' => 'HBANK',
                'bank_name' => 'HANA BANK',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
        ];
        hr_bank :: insert($scheme);
    }
}