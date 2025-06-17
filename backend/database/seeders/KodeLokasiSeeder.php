<?php

namespace Database\Seeders;

use App\Models\hr_kode_lokasi;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class KodeLokasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = (new hr_kode_lokasi) -> getTable();
        if (Schema::hasTable($tableName)){
            $rowCount = hr_kode_lokasi :: count ();
            if ($rowCount > 0) hr_kode_lokasi ::truncate();
            
            $sequence = $tableName . "_id_seq";
            DB :: statement("ALTER SEQUENCE $sequence RESTART WITH 1");
        };

        $scheme = [
            [
                'kode_cabang' => 'BDG1',
                'nama_cabang' => 'Bandung1',
                'kode_lokasi' => '006',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'BDG2',
                'nama_cabang' => 'Bandung2',
                'kode_lokasi' => '160',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'BDL',
                'nama_cabang' => 'Bandarlampung',
                'kode_lokasi' => '058',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'BGR',
                'nama_cabang' => 'Bogor',
                'kode_lokasi' => '017',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'BJM',
                'nama_cabang' => 'Banjarmasin',
                'kode_lokasi' => '009',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'BKL',
                'nama_cabang' => 'Bengkulu',
                'kode_lokasi' => '015',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'CRB',
                'nama_cabang' => 'Cirebon',
                'kode_lokasi' => '028',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'DPS',
                'nama_cabang' => 'Denpasar-Bali',
                'kode_lokasi' => '029',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'JBI',
                'nama_cabang' => 'Jambi',
                'kode_lokasi' => '042',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'JKTBRT',
                'nama_cabang' => 'Jakarta Barat',
                'kode_lokasi' => '041',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'JKTPST',
                'nama_cabang' => 'Jakarta Pusat',
                'kode_lokasi' => '037',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'JKTSLT',
                'nama_cabang' => 'Jakarta Selatan',
                'kode_lokasi' => '038',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'JKTTMR',
                'nama_cabang' => 'Jakarta Timur',
                'kode_lokasi' => '039',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'MDN',
                'nama_cabang' => 'Medan',
                'kode_lokasi' => '068',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'JMB',
                'nama_cabang' => 'Jember',
                'kode_lokasi' => '043',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'KDR',
                'nama_cabang' => 'Kediri',
                'kode_lokasi' => '049',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'KRW',
                'nama_cabang' => 'Karawang',
                'kode_lokasi' => '052',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'MKS',
                'nama_cabang' => 'Makassar',
                'kode_lokasi' => '065',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'MLG',
                'nama_cabang' => 'Malang',
                'kode_lokasi' => '066',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'PDG',
                'nama_cabang' => 'Padang',
                'kode_lokasi' => '076',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'PKB',
                'nama_cabang' => 'Pekanbaru',
                'kode_lokasi' => '087',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'PLB',
                'nama_cabang' => 'Palembang',
                'kode_lokasi' => '078',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'PTK',
                'nama_cabang' => 'Pontianak',
                'kode_lokasi' => '089',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'PWT',
                'nama_cabang' => 'Purwokerto',
                'kode_lokasi' => '095',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'SBY1',
                'nama_cabang' => 'Surabaya 1',
                'kode_lokasi' => '109',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'YGY',
                'nama_cabang' => 'Yogyakarta',
                'kode_lokasi' => '221',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'SMD',
                'nama_cabang' => 'Samarinda',
                'kode_lokasi' => '223',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'MND',
                'nama_cabang' => 'Manado',
                'kode_lokasi' => '444',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'SMG',
                'nama_cabang' => 'Semarang',
                'kode_lokasi' => '555',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'SRG',
                'nama_cabang' => 'Serang',
                'kode_lokasi' => '666',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'SLO',
                'nama_cabang' => 'Solo',
                'kode_lokasi' => '777',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'TGR',
                'nama_cabang' => 'Tanggerang',
                'kode_lokasi' => '888',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'TSK',
                'nama_cabang' => 'Tasikmalaya',
                'kode_lokasi' => '999',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
        ];

        hr_kode_lokasi :: insert($scheme);
    }
}