<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SuperAdminSeeder::class,
            FisherfolkRegisteredSeeder::class,
            SuppliesSeeder::class, // Run before VegetableSeedlingsSeeder to ensure supplies are available
            VegetableSeedlingsDispersalSeeder::class,
            BoatrRegisteredSeeder::class,
            RsbsaSpecificDataSeeder::class,
            TrainingSeeder::class,
            // UserRegistrationSeeder::class,
            
            EventsSeeder::class,
            SlideshowImagesSeeder::class
        ]);
    }
}
