<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FishrApplication;
use App\Models\UserRegistration;

class FishrApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have users in the user_registration table
        if (UserRegistration::count() < 10) {
            UserRegistration::factory(20)->create();
        }

        // Create test FishR registrations with varied statuses
        FishrApplication::factory(5)->pending()->create();
        FishrApplication::factory(5)->underReview()->create();
        FishrApplication::factory(8)->approved()->create();
        FishrApplication::factory(2)->rejected()->create();

        // Create some with secondary livelihood
        FishrApplication::factory(10)->withSecondaryLivelihood()->approved()->create();
        FishrApplication::factory(5)->withSecondaryLivelihood()->pending()->create();

        // Create some without secondary livelihood
        FishrApplication::factory(5)->withoutSecondaryLivelihood()->pending()->create();

        $this->command->info('FishrApplication seeder executed successfully!');
        $this->command->info('Total records created: ' . FishrApplication::count());
    }
}