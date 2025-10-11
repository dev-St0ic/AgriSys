<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RsbsaApplication;
use App\Models\UserRegistration;

class RsbsaApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have users in the user_registration table
        // Check if there are existing users, if not create some
        if (UserRegistration::count() < 10) {
            UserRegistration::factory(20)->create();
        }

        // Create a mix of different types of applications

        // 10 pending applications
        RsbsaApplication::factory(10)->pending()->create();

        // 8 under review applications
        RsbsaApplication::factory(8)->underReview()->create();

        // 15 approved applications
        RsbsaApplication::factory(15)->approved()->create();

        // 5 rejected applications
        RsbsaApplication::factory(5)->rejected()->create();

        // Create some applications with different livelihoods
        RsbsaApplication::factory(5)->create(['main_livelihood' => 'Farmer']);
        RsbsaApplication::factory(4)->create(['main_livelihood' => 'Fisherfolk']);
        RsbsaApplication::factory(3)->create(['main_livelihood' => 'Farmworker/Laborer']);
        RsbsaApplication::factory(3)->create(['main_livelihood' => 'Agri-youth']);

        // Create some applications from specific barangays
        $popularBarangays = ['Poblacion', 'San Antonio', 'Bagong Silang', 'Magsaysay'];
        foreach ($popularBarangays as $barangay) {
            RsbsaApplication::factory(3)->create(['barangay' => $barangay]);
        }
    }
}
