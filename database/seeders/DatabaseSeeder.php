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
            // BarangaySeeder::class,
            // FishrRequestSeeder::class,
            FisherfolkRegisteredSeeder::class,
            SuppliesSeeder::class,
            // SupplyManagementSeeder::class,  // Run before SeedlingRequestSeeder to ensure supplies are available
            // SeedlingRequestSeeder::class,
            VegetableSeedlingsDispersalSeeder::class,
            // BoatrRequestSeeder::class,
            BoatrRegisteredSeeder::class,
            // RsbsaApplicationSeeder::class,
            RsbsaSpecificDataSeeder::class, // Run after RsbsaApplicationSeeder to link specific data to applications
            TrainingApplicationSeeder::class,
            UserRegistrationSeeder::class,
            
            EventSeeder::class,
            SlideshowImagesSeeder::class
        ]);
    }
}
