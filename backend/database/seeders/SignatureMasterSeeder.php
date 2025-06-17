<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SignatureMaster;

class SignatureMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SignatureMaster::create([
            'name' => 'Fransiska Margi Tri Rahayu',
            'position' => 'Jr. HRGA Manager',
            'is_active' => true,
            'created_by' => 'SYSTEM',
            'updated_by' => 'SYSTEM',
        ]);
    }
}