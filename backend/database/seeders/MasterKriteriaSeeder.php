<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MasterKriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'kriteria' => 'Grooming',
                'is_active' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'seeder',
                'updated_by' => 'seeder'
            ],
            [
                'kriteria' => 'Pengetahuan Produk',
                'is_active' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'seeder',
                'updated_by' => 'seeder'
            ],
            [
                'kriteria' => 'Pencapaian Target Sales',
                'is_active' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'seeder',
                'updated_by' => 'seeder'
            ],
            [
                'kriteria' => 'Kedisiplinan dan Kehadiran',
                'is_active' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'seeder',
                'updated_by' => 'seeder'
            ],
            [
                'kriteria' => 'Administrasi',
                'is_active' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'seeder',
                'updated_by' => 'seeder'
            ]
        ];

        DB::table('mst_kriteria_rapor')->insert($data);
    }
}