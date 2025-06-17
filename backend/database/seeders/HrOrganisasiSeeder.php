<?php

namespace Database\Seeders;

use App\Models\hr_organisasi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class HrOrganisasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = (new hr_organisasi()) -> getTable();
        if (Schema::hasTable($tableName)){
            $rowCount = hr_organisasi :: count ();
            if ($rowCount > 0) hr_organisasi ::truncate();
            
            $sequence = $tableName . "_id_seq";
            DB :: statement("ALTER SEQUENCE $sequence RESTART WITH 1");
        };
        
        $scheme = [
            [
                'supplier_name' => 'PT. Martina Berto',
                'supplier_address' => 'Jl. Pulo Kambing',
                'supplier_phone' => '02100821837',
                'supplier_city' => 'Jakarta',
                'supplier_postalcode' => '13930',
                'created_at' => Carbon :: now(),
                'updated_at' => Carbon :: now(),  
                'created_by' => 'testing',
            ],
            [
                'supplier_name' => 'PT. SAI INDONESIA',
                'supplier_address' => 'Jl. Pulo Ayang Kawasan Industri JIEP',
                'supplier_phone' => '02100821837',
                'supplier_city' => 'Jakarta',
                'supplier_postalcode' => '13930',
                'created_at' => Carbon :: now(),
                'updated_at' => Carbon :: now(),  
                'created_by' => 'testing',
            ],
            [
                'supplier_name' => 'TPSMTG',
                'supplier_address' => 'Jl. Pulo Kambing',
                'supplier_phone' => '000000',
                'supplier_city' => 'Jakarta',
                'supplier_postalcode' => '13930',
                'created_at' => Carbon :: now(),
                'updated_at' => Carbon :: now(),  
                'created_by' => 'testing',
            ],
            [
                'supplier_name' => 'PT. PENTA VALENT',
                'supplier_address' => 'Jl. Kedoya Raya No.33',
                'supplier_phone' => '000000',
                'supplier_city' => 'Jakarta',
                'supplier_postalcode' => '1150',
                'created_at' => Carbon :: now(),
                'updated_at' => Carbon :: now(),  
                'created_by' => 'testing',
            ],
        ];
        hr_organisasi :: insert($scheme);
        
    }
}