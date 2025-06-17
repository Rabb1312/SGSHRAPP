<?php

namespace Database\Seeders;

use App\Models\hr_unit;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class HrUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = (new hr_unit) -> getTable();
        if (Schema::hasTable($tableName)){
            $rowCount = hr_unit :: count ();
            if ($rowCount > 0) hr_unit ::truncate();
            
            $sequence = $tableName . "_id_seq";
            DB :: statement("ALTER SEQUENCE $sequence RESTART WITH 1");
        };
        $scheme = [
            [
                'unit_number' => '06',
                'unit_code' => 'SGS',
                'unit_name' => 'SINERGI GLOBAL SERVIS',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'unit_number' => '07',
                'unit_code' => 'KBP',
                'unit_name' => 'KREASI BOGA PRIMA TAMA',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'unit_number' => '08',
                'unit_code' => 'CS',
                'unit_name' => 'CREATIVE STYLE',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'unit_number' => '09',
                'unit_code' => 'CPP',
                'unit_name' => 'CANTIKA PUSPA PESONA',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'unit_number' => '10',
                'unit_code' => 'TPS',
                'unit_name' => 'TARA PARAMA SEMESTA',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'unit_number' => '11',
                'unit_code' => 'CDF',
                'unit_name' => 'CEDEFINDO',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'unit_number' => '12',
                'unit_code' => 'DNS',
                'unit_name' => 'DOS NI ROHA',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'unit_number' => '13',
                'unit_code' => 'RMB',
                'unit_name' => 'ROHIS MARTINA BERTO',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
        ];
        hr_unit :: insert($scheme);
    }
}