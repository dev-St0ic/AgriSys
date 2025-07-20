<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FishrApplication;

class FishrRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 20 test FishR registrations using the factory
        FishrApplication::factory(20)->create();
    }
}