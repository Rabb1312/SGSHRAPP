<?php

namespace Database\Seeders;

use App\Models\hr_cabang;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class HrCabangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */


    public function run(): void
    {
        $tableName = (new hr_cabang)->getTable();
        if (Schema::hasTable($tableName)) {
            $rowCount = hr_cabang::count();
            if ($rowCount > 0) hr_cabang::truncate();

            $sequence = $tableName . "_id_seq";
            DB::statement("ALTER SEQUENCE $sequence RESTART WITH 1");
        };

        $scheme = [
            [
                'kode_cabang' => 'ACH',
                'nama_cabang' => 'Aceh',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'BDG',
                'nama_cabang' => 'Bandung',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'BDL',
                'nama_cabang' => 'Bandarlampung',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'BGR',
                'nama_cabang' => 'Bogor',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'BJM',
                'nama_cabang' => 'Banjarmasin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'BKL',
                'nama_cabang' => 'Bengkulu',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'CRB',
                'nama_cabang' => 'Cirebon',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'DPS',
                'nama_cabang' => 'Denpasar',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'JBI',
                'nama_cabang' => 'Jambi',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'JKT',
                'nama_cabang' => 'Jakarta',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'MDN',
                'nama_cabang' => 'Medan',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'JMB',
                'nama_cabang' => 'Jember',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'KDR',
                'nama_cabang' => 'Kediri',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'KRW',
                'nama_cabang' => 'Karawang',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'MKS',
                'nama_cabang' => 'Makassar',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'MLG',
                'nama_cabang' => 'Malang',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'PDG',
                'nama_cabang' => 'Padang',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'PKB',
                'nama_cabang' => 'Pekanbaru',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'PLB',
                'nama_cabang' => 'Palembang',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'PNK',
                'nama_cabang' => 'Pontianak',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'PWT',
                'nama_cabang' => 'Purwokerto',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'SBY',
                'nama_cabang' => 'Surabaya',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'MND',
                'nama_cabang' => 'Manado',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'MTR',
                'nama_cabang' => 'Mataram',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'PLU',
                'nama_cabang' => 'Palu',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'SLO',
                'nama_cabang' => 'Solo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'SMD',
                'nama_cabang' => 'Sumedang',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'SMG',
                'nama_cabang' => 'Semarang',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'SRG',
                'nama_cabang' => 'Serang',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'TSK',
                'nama_cabang' => 'Tasikmalaya',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'YGY',
                'nama_cabang' => 'Yogyakarta',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
        ];

        $scheme = array_map(function ($item) {
            $item['nama_cabang'] = strtoupper($item['nama_cabang']);
            return $item;
        }, $scheme);

        hr_cabang::insert($scheme);
    }
}