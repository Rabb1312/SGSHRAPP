<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\hr_sallary;

class SalarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        hr_sallary::create(
            // [
            //     'cabang_id' => 'ACEH',
            //     'sallary' => '3460672',
            //     'tahun' => '2024',
            //     'created_by' => 'SYSTEM',
            //     'updated_by' => 'SYSTEM',
            // ],
            // [
            //     'cabang_id' => 'BANDUNG',
            //     'sallary' => '4209309',
            //     'tahun' => '2024',
            //     'created_by' => 'SYSTEM',
            //     'updated_by' => 'SYSTEM',
            // ],
            // [
            //     'cabang_id' => 'BOGOR',
            //     'sallary' => '4813988',
            //     'tahun' => '2024',
            //     'created_by' => 'SYSTEM',
            //     'updated_by' => 'SYSTEM',
            // ],
            // [
            //     'cabang_id' => 'BANJARMASIN',
            //     'sallary' => '3379513',
            //     'tahun' => '2024',
            //     'created_by' => 'SYSTEM',
            //     'updated_by' => 'SYSTEM',
            // ],
            // [
            //     'cabang_id' => 'CIREBON',
            //     'sallary' => '2533038',
            //     'tahun' => '2024',
            //     'created_by' => 'SYSTEM',
            //     'updated_by' => 'SYSTEM',
            // ],
            // [
            //     'cabang_id' => 'DENPASAR',
            //     'sallary' => '3096823',
            //     'tahun' => '2024',
            //     'created_by' => 'SYSTEM',
            //     'updated_by' => 'SYSTEM',
            // ],
            // [
            //     'cabang_id' => 'JAMBI',
            //     'sallary' => '3037121',
            //     'tahun' => '2024',
            //     'created_by' => 'SYSTEM',
            //     'updated_by' => 'SYSTEM',
            // ],
            [
                'cabang_id' => 'JAKARTA',
                'sallary' => '5067381',
                'tahun' => '2024',
                'created_by' => 'SYSTEM',
                'updated_by' => 'SYSTEM',
            ],
            // [
            //     'cabang_id' => 'JAMBI',
            //     'sallary' => '3037121',
            //     'tahun' => '2024',
            //     'created_by' => 'SYSTEM',
            //     'updated_by' => 'SYSTEM',
            // ],
            // [
            //     'cabang_id' => 'KEDIRI',
            //     'sallary' => '2415362',
            //     'tahun' => '2024',
            //     'created_by' => 'SYSTEM',
            //     'updated_by' => 'SYSTEM',
            // ],
            // [
            //     'cabang_id' => 'BANDARLAMPUNG',
            //     'sallary' => '31036311',
            //     'tahun' => '2024',
            //     'created_by' => 'SYSTEM',
            //     'updated_by' => 'SYSTEM',
            // ],
            // [
            //     'cabang_id' => 'MEDAN',
            //     'sallary' => '3769082',
            //     'tahun' => '2024',
            //     'created_by' => 'SYSTEM',
            //     'updated_by' => 'SYSTEM',
            // ],
            // [
            //     'cabang_id' => 'MAKASSAR',
            //     'sallary' => '3643321',
            //     'tahun' => '2024',
            //     'created_by' => 'SYSTEM',
            //     'updated_by' => 'SYSTEM',
            // ],
            // [
            //     'cabang_id' => 'MANADO',
            //     'sallary' => '3590000',
            //     'tahun' => '2024',
            //     'created_by' => 'SYSTEM',
            //     'updated_by' => 'SYSTEM',
            // ],
            // [
            //     'cabang_id' => 'MALANG',
            //     'sallary' => '3309144',
            //     'tahun' => '2024',
            //     'created_by' => 'SYSTEM',
            //     'updated_by' => 'SYSTEM',
            // ],
            // [
            //     'cabang_id' => 'PADANG',
            //     'sallary' => '3319023',
            //     'tahun' => '2024',
            //     'created_by' => 'SYSTEM',
            //     'updated_by' => 'SYSTEM',
            // ],
            // [
            //     'cabang_id' => 'PALU',
            //     'sallary' => '3179859',
            //     'tahun' => '2024',
            //     'created_by' => 'SYSTEM',
            //     'updated_by' => 'SYSTEM',
            // ],
            // [
            //     'cabang_id' => 'PALEMBANG',
            //     'sallary' => '3179859',
            //     'tahun' => '2024',
            //     'created_by' => 'SYSTEM',
            //     'updated_by' => 'SYSTEM',
            // ],
            // [
            //     'cabang_id' => 'PONTIANAK',
            //     'sallary' => '3188983',
            //     'tahun' => '2024',
            //     'created_by' => 'SYSTEM',
            //     'updated_by' => 'SYSTEM',
            // ],
            // [
            //     'cabang_id' => 'PURWOKERTO',
            //     'sallary' => '2195960',
            //     'tahun' => '2024',
            //     'created_by' => 'SYSTEM',
            //     'updated_by' => 'SYSTEM',
            // ],
            // [
            //     'cabang_id' => 'SURABAYA',
            //     'sallary' => '4725479',
            //     'tahun' => '2024',
            //     'created_by' => 'SYSTEM',
            //     'updated_by' => 'SYSTEM',
            // ],
            // [
            //     'cabang_id' => 'SOLO',
            //     'sallary' => '2269070',
            //     'tahun' => '2024',
            //     'created_by' => 'SYSTEM',
            //     'updated_by' => 'SYSTEM',
            // ],
            // [
            //     'cabang_id' => 'SUMEDANG',
            //     'sallary' => '3732088',
            //     'tahun' => '2024',
            //     'created_by' => 'SYSTEM',
            //     'updated_by' => 'SYSTEM',
            // ],
            // [
            //     'cabang_id' => 'TANGGERANG',
            //     'sallary' => '4601988',
            //     'tahun' => '2024',
            //     'created_by' => 'SYSTEM',
            //     'updated_by' => 'SYSTEM',
            // ],
            // [
            //     'cabang_id' => 'TASIKMALAYA',
            //     'sallary' => '2630951',
            //     'tahun' => '2024',
            //     'created_by' => 'SYSTEM',
            //     'updated_by' => 'SYSTEM',
            // ],
            // [
            //     'cabang_id' => 'YOGYAKARTA',
            //     'sallary' => '2125897',
            //     'tahun' => '2024',
            //     'created_by' => 'SYSTEM',
            //     'updated_by' => 'SYSTEM',
            // ],
        );
    }
}