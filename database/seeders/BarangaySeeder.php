<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangays = [
            'Bagong Silang',
            'Cuyab',
            'Estrella',
            'G.S.I.S.',
            'Landayan',
            'Langgam',
            'Laram',
            'Magsaysay',
            'Nueva',
            'Poblacion',
            'Riverside',
            'San Antonio',
            'San Roque',
            'San Vicente',
            'Santo Niño',
            'United Bayanihan',
            'United Better Living',
            'Sampaguita Village',
            'Calendola',
            'Narra',
            'Chrysanthemum',
            'Fatima',
            'Maharlika',
            'Pacita 1',
            'Pacita 2',
            'Rosario',
            'San Lorenzo Ruiz',
        ];

        foreach ($barangays as $barangay) {
            DB::table('barangays')->insert([
                'name' => $barangay,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Successfully seeded ' . count($barangays) . ' barangays.');
    }
}
