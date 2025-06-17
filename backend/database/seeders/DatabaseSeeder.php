<?php

namespace Database\Seeders;

use App\Models\SignatureMaster;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call('UsersTableSeeder');
        $this->call(HrCabangSeeder :: class);
        $this->call(HrJabatanSeeder :: class);
        $this->call(HrUnitSeeder :: class);
        $this->call(HrOrganisasiSeeder :: class);
        // $this->call(HrKaryawanSeeder :: class);
        $this->call(HrBankSeeder :: class);
        $this->call(KodeLokasiSeeder :: class);
        $this->call(UserSeeder :: class);
        $this->call(SignatureMasterSeeder :: class);
        $this->call(SalarySeeder :: class);
        $this->call(MasterKriteriaSeeder :: class);

    }
}