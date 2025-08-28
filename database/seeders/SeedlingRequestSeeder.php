<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SeedlingRequest;
use Carbon\Carbon;

class SeedlingRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create historical data spanning from 2024 to 2025

        // === 2024 DATA ===

        // Q1 2024 (January - March)
        SeedlingRequest::factory()->count(25)->create([
            'status' => 'approved',
            'created_at' => Carbon::parse('2024-01-01')->addDays(rand(0, 89)), // Jan-Mar 2024
        ]);

        // Q2 2024 (April - June) - Peak planting season
        SeedlingRequest::factory()->count(35)->create([
            'status' => 'approved',
            'created_at' => Carbon::parse('2024-04-01')->addDays(rand(0, 91)), // Apr-Jun 2024
        ]);

        // Q3 2024 (July - September)
        SeedlingRequest::factory()->count(30)->create([
            'status' => 'approved',
            'created_at' => Carbon::parse('2024-07-01')->addDays(rand(0, 92)), // Jul-Sep 2024
        ]);

        // Q4 2024 (October - December)
        SeedlingRequest::factory()->count(28)->create([
            'status' => 'approved',
            'created_at' => Carbon::parse('2024-10-01')->addDays(rand(0, 92)), // Oct-Dec 2024
        ]);

        // === 2025 DATA ===

        // Q1 2025 (January - March)
        SeedlingRequest::factory()->count(30)->create([
            'status' => 'approved',
            'created_at' => Carbon::parse('2025-01-01')->addDays(rand(0, 89)), // Jan-Mar 2025
        ]);

        // Q2 2025 (April - June) - Peak season
        SeedlingRequest::factory()->count(40)->create([
            'status' => 'approved',
            'created_at' => Carbon::parse('2025-04-01')->addDays(rand(0, 91)), // Apr-Jun 2025
        ]);

        // Q3 2025 (July - August, current period)
        SeedlingRequest::factory()->count(20)->create([
            'status' => 'approved',
            'created_at' => Carbon::parse('2025-07-01')->addDays(rand(0, 58)), // Jul-Aug 2025
        ]);

        // Recent pending requests (August 2025)
        SeedlingRequest::factory()->count(15)->create([
            'status' => 'under_review',
            'created_at' => Carbon::now()->subDays(rand(1, 14))
        ]);

        // Recent rejected requests (mixed dates 2025)
        SeedlingRequest::factory()->count(12)->create([
            'status' => 'rejected',
            'created_at' => Carbon::parse('2025-01-01')->addDays(rand(0, 240))
        ]);

        // Partially approved requests (2025)
        SeedlingRequest::factory()->count(18)->create([
            'status' => 'partially_approved',
            'created_at' => Carbon::parse('2025-01-01')->addDays(rand(0, 240))
        ]);

        // === SPECIFIC POPULAR ITEMS ACROSS TIME PERIODS ===

        // High-demand vegetables throughout 2024-2025
        for ($month = 1; $month <= 8; $month++) {
            $year = $month <= 12 ? 2024 : 2025;
            $actualMonth = $month <= 12 ? $month : $month - 12;

            if ($year == 2025 && $actualMonth > 8) break; // Don't go beyond August 2025

            SeedlingRequest::factory()->count(rand(3, 6))->create([
                'status' => 'approved',
                'vegetables' => [
                    ['name' => 'Kamatis', 'quantity' => rand(15, 25)],
                    ['name' => 'Eggplant', 'quantity' => rand(10, 18)]
                ],
                'created_at' => Carbon::create($year, $actualMonth, rand(1, 28))
            ]);
        }

        // Fruit requests spread across seasons
        for ($quarter = 1; $quarter <= 6; $quarter++) {
            $year = $quarter <= 4 ? 2024 : 2025;
            $month = $quarter <= 4 ? ($quarter * 3) : (($quarter - 4) * 3);

            if ($year == 2025 && $month > 8) break;

            SeedlingRequest::factory()->count(rand(2, 4))->create([
                'status' => 'approved',
                'fruits' => [
                    ['name' => 'Kalamansi', 'quantity' => rand(5, 8)],
                    ['name' => 'Mangga', 'quantity' => rand(2, 6)]
                ],
                'created_at' => Carbon::create($year, $month, rand(1, 28))
            ]);
        }

        // Fertilizer requests (steady throughout)
        for ($month = 1; $month <= 20; $month++) {
            $year = $month <= 12 ? 2024 : 2025;
            $actualMonth = $month <= 12 ? $month : $month - 12;

            if ($year == 2025 && $actualMonth > 8) break;

            SeedlingRequest::factory()->count(rand(1, 3))->create([
                'status' => 'approved',
                'fertilizers' => [
                    ['name' => 'Vermicast', 'quantity' => rand(8, 12)],
                    ['name' => 'Pre-processed Chicken Manure', 'quantity' => rand(3, 8)]
                ],
                'created_at' => Carbon::create($year, $actualMonth, rand(1, 28))
            ]);
        }

        // Add some seasonal variations

        // Summer planting requests (March-May 2024 & 2025)
        foreach ([2024, 2025] as $year) {
            if ($year == 2025) continue; // Skip 2025 summer as we're in August

            SeedlingRequest::factory()->count(8)->create([
                'status' => 'approved',
                'vegetables' => [
                    ['name' => 'Okra', 'quantity' => rand(10, 15)],
                    ['name' => 'Pipino', 'quantity' => rand(8, 16)]
                ],
                'created_at' => Carbon::create($year, rand(3, 5), rand(1, 28))
            ]);
        }

        // Rainy season requests (June-August 2024 & 2025)
        foreach ([2024, 2025] as $year) {
            $maxMonth = $year == 2025 ? 8 : 8; // Current limit for 2025

            SeedlingRequest::factory()->count(6)->create([
                'status' => 'approved',
                'vegetables' => [
                    ['name' => 'Kangkong', 'quantity' => rand(20, 30)],
                    ['name' => 'Kalabasa', 'quantity' => rand(8, 12)]
                ],
                'created_at' => Carbon::create($year, rand(6, $maxMonth), rand(1, 28))
            ]);
        }
    }
}
