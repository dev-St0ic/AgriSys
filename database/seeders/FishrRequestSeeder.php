<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FishrApplication;
use App\Models\UserRegistration;

class FishrRequestSeeder extends Seeder
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

        // Create 20 test FishR registrations using the factory
        FishrApplication::factory(20)->create();
    }
}
