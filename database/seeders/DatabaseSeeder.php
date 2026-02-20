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
            BarangaySeeder::class,
            FishrRequestSeeder::class,
            RequestCategorySeeder::class,
            SupplyManagementSeeder::class,  // Run before SeedlingRequestSeeder to ensure supplies are available
            SeedlingRequestSeeder::class,
            BoatrRequestSeeder::class,
            RsbsaApplicationSeeder::class,
            TrainingApplicationSeeder::class,
            UserRegistrationSeeder::class,
            EventSeeder::class,
            SlideshowImagesSeeder::class,
            RetentionScheduleSeeder::class,
        ]);
    }
}
