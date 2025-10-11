<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TrainingApplication;
use App\Models\UserRegistration;

class TrainingApplicationSeeder extends Seeder
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

        // Create 30 test training applications using the factory
        TrainingApplication::factory(30)->create();

        // Create some specific training type applications for testing
        TrainingApplication::factory(5)->trainingType('tilapia_hito')->approved()->create();
        TrainingApplication::factory(3)->trainingType('hydroponics')->rejected()->create();
        TrainingApplication::factory(4)->trainingType('aquaponics')->underReview()->create();
        TrainingApplication::factory(2)->trainingType('mushrooms')->approved()->create();
        TrainingApplication::factory(3)->trainingType('livestock_poultry')->underReview()->create();
        TrainingApplication::factory(2)->trainingType('high_value_crops')->approved()->create();
        TrainingApplication::factory(1)->trainingType('sampaguita_propagation')->rejected()->create();
    }
}
