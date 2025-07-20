<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BoatrApplication;

class BoatrRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 25 test BoatR applications with different statuses
        
        // 8 pending applications
        BoatrApplication::factory(8)->pending()->create();
        
        // 6 approved applications (with completed inspections)
        BoatrApplication::factory(6)->approved()->create();
        
        // 4 inspection required applications
        BoatrApplication::factory(4)->inspectionRequired()->create();
        
        // 3 rejected applications
        BoatrApplication::factory(3)->rejected()->create();
        
        // 4 random status applications
        BoatrApplication::factory(4)->create();
        
        $this->command->info('Created 25 BoatR application records with various statuses.');
    }
}