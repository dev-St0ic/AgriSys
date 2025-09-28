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
     * Creates comprehensive test data for all 6 seedling categories across 2024-2025
     */
    public function run(): void
    {
        $this->command->info('Creating seedling requests with 6 categories...');

        // === 2024 HISTORICAL DATA ===
        $this->create2024Data();

        // === 2025 CURRENT YEAR DATA ===
        $this->create2025Data();

        // === CATEGORY-SPECIFIC POPULAR ITEMS ===
        $this->createPopularItemsData();

        // === SEASONAL VARIATIONS ===
        $this->createSeasonalData();

        // === LARGE ORDERS AND SPECIAL CASES ===
        $this->createSpecialCases();

        $this->command->info('Seedling requests seeding completed successfully!');
    }

    private function create2024Data(): void
    {
        $this->command->info('Creating 2024 historical data...');

        // Q1 2024 (January - March) - Mixed requests
        SeedlingRequest::factory()->count(15)->approved()->create([
            'created_at' => Carbon::parse('2024-01-01')->addDays(rand(0, 89)),
        ]);
        SeedlingRequest::factory()->count(8)->seedsOnly()->approved()->create([
            'created_at' => Carbon::parse('2024-01-01')->addDays(rand(0, 89)),
        ]);
        SeedlingRequest::factory()->count(5)->ornamentalsOnly()->approved()->create([
            'created_at' => Carbon::parse('2024-01-01')->addDays(rand(0, 89)),
        ]);

        // Q2 2024 (April - June) - Peak planting season
        SeedlingRequest::factory()->count(20)->approved()->create([
            'created_at' => Carbon::parse('2024-04-01')->addDays(rand(0, 91)),
        ]);
        SeedlingRequest::factory()->count(10)->seedsOnly()->approved()->create([
            'created_at' => Carbon::parse('2024-04-01')->addDays(rand(0, 91)),
        ]);
        SeedlingRequest::factory()->count(8)->fingerlingsOnly()->approved()->create([
            'created_at' => Carbon::parse('2024-04-01')->addDays(rand(0, 91)),
        ]);

        // Q3 2024 (July - September) - Rainy season
        SeedlingRequest::factory()->count(18)->approved()->create([
            'created_at' => Carbon::parse('2024-07-01')->addDays(rand(0, 92)),
        ]);
        SeedlingRequest::factory()->count(12)->ornamentalsOnly()->approved()->create([
            'created_at' => Carbon::parse('2024-07-01')->addDays(rand(0, 92)),
        ]);

        // Q4 2024 (October - December) - Year-end requests
        SeedlingRequest::factory()->count(16)->approved()->create([
            'created_at' => Carbon::parse('2024-10-01')->addDays(rand(0, 92)),
        ]);
        SeedlingRequest::factory()->count(6)->fingerlingsOnly()->approved()->create([
            'created_at' => Carbon::parse('2024-10-01')->addDays(rand(0, 92)),
        ]);
        SeedlingRequest::factory()->count(8)->seedsOnly()->approved()->create([
            'created_at' => Carbon::parse('2024-10-01')->addDays(rand(0, 92)),
        ]);
    }

    private function create2025Data(): void
    {
        $this->command->info('Creating 2025 current year data...');

        // Q1 2025 (January - March) - New year initiatives
        SeedlingRequest::factory()->count(20)->approved()->create([
            'created_at' => Carbon::parse('2025-01-01')->addDays(rand(0, 89)),
        ]);
        SeedlingRequest::factory()->count(10)->ornamentalsOnly()->approved()->create([
            'created_at' => Carbon::parse('2025-01-01')->addDays(rand(0, 89)),
        ]);

        // Q2 2025 (April - June) - Peak season with all categories
        SeedlingRequest::factory()->count(25)->approved()->create([
            'created_at' => Carbon::parse('2025-04-01')->addDays(rand(0, 91)),
        ]);
        SeedlingRequest::factory()->count(15)->seedsOnly()->approved()->create([
            'created_at' => Carbon::parse('2025-04-01')->addDays(rand(0, 91)),
        ]);
        SeedlingRequest::factory()->count(10)->fingerlingsOnly()->approved()->create([
            'created_at' => Carbon::parse('2025-04-01')->addDays(rand(0, 91)),
        ]);

        // Q3 2025 (July - August, current period)
        SeedlingRequest::factory()->count(15)->approved()->create([
            'created_at' => Carbon::parse('2025-07-01')->addDays(rand(0, 58)),
        ]);
        SeedlingRequest::factory()->count(8)->ornamentalsOnly()->approved()->create([
            'created_at' => Carbon::parse('2025-07-01')->addDays(rand(0, 58)),
        ]);

        // Recent pending requests (August 2025) - Mixed categories
        SeedlingRequest::factory()->count(8)->pending()->create([
            'created_at' => Carbon::now()->subDays(rand(1, 14))
        ]);
        SeedlingRequest::factory()->count(4)->seedsOnly()->pending()->create([
            'created_at' => Carbon::now()->subDays(rand(1, 14))
        ]);
        SeedlingRequest::factory()->count(3)->fingerlingsOnly()->pending()->create([
            'created_at' => Carbon::now()->subDays(rand(1, 14))
        ]);

        // Recent rejected requests (mixed dates 2025)
        SeedlingRequest::factory()->count(8)->rejected()->create([
            'created_at' => Carbon::parse('2025-01-01')->addDays(rand(0, 240))
        ]);
        SeedlingRequest::factory()->count(4)->ornamentalsOnly()->rejected()->create([
            'created_at' => Carbon::parse('2025-01-01')->addDays(rand(0, 240))
        ]);

        // Partially approved requests (2025) - Mix of categories
        SeedlingRequest::factory()->count(12)->partiallyApproved()->create([
            'created_at' => Carbon::parse('2025-01-01')->addDays(rand(0, 240))
        ]);
        SeedlingRequest::factory()->count(6)->partiallyApproved()->create([
            'created_at' => Carbon::parse('2025-01-01')->addDays(rand(0, 240))
        ]);
    }

    private function createPopularItemsData(): void
    {
        $this->command->info('Creating popular items across categories...');

        // Seeds - Popular throughout the year
        for ($month = 1; $month <= 20; $month++) {
            $year = $month <= 12 ? 2024 : 2025;
            $actualMonth = $month <= 12 ? $month : $month - 12;

            if ($year == 2025 && $actualMonth > 8) break;

            SeedlingRequest::factory()->count(rand(2, 4))->create([
                'status' => 'approved',
                'seeds' => [
                    ['name' => 'Red Ruby Tomato Seeds', 'quantity' => rand(10, 15)],
                    ['name' => 'Green Gem String Bean Seeds', 'quantity' => rand(8, 12)]
                ],
                'seedlings' => [],
                'fruits' => [],
                'ornamentals' => [],
                'fingerlings' => [],
                'fertilizers' => [
                    ['name' => 'Vermicast Fertilizer', 'quantity' => rand(3, 8)]
                ],
                'vegetables' => [], // Legacy field
                'seeds_status' => 'approved',
                'fertilizers_status' => 'approved',
                'created_at' => Carbon::create($year, $actualMonth, rand(1, 28))
            ]);
        }

        // Seedlings - High demand in planting seasons
        for ($quarter = 1; $quarter <= 6; $quarter++) {
            $year = $quarter <= 4 ? 2024 : 2025;
            $month = $quarter <= 4 ? ($quarter * 3) : (($quarter - 4) * 3);

            if ($year == 2025 && $month > 8) break;

            SeedlingRequest::factory()->count(rand(3, 6))->create([
                'status' => 'approved',
                'seeds' => [],
                'seedlings' => [
                    ['name' => 'Calamansi Seedling', 'quantity' => rand(5, 8)],
                    ['name' => 'Papaya Seedling', 'quantity' => rand(3, 5)]
                ],
                'fruits' => [],
                'ornamentals' => [],
                'fingerlings' => [],
                'fertilizers' => [],
                'vegetables' => [ // Legacy mapping
                    ['name' => 'Calamansi Seedling', 'quantity' => rand(5, 8)],
                    ['name' => 'Papaya Seedling', 'quantity' => rand(3, 5)]
                ],
                'seedlings_status' => 'approved',
                'vegetables_status' => 'approved', // Legacy
                'created_at' => Carbon::create($year, $month, rand(1, 28))
            ]);
        }

        // Fruit-bearing Trees - Popular for long-term projects
        for ($month = 1; $month <= 20; $month++) {
            $year = $month <= 12 ? 2024 : 2025;
            $actualMonth = $month <= 12 ? $month : $month - 12;

            if ($year == 2025 && $actualMonth > 8) break;

            if ($month % 3 == 0) { // Every 3rd month
                SeedlingRequest::factory()->count(rand(2, 3))->create([
                    'status' => 'approved',
                    'seeds' => [],
                    'seedlings' => [],
                    'fruits' => [
                        ['name' => 'Lakatan Banana Tree', 'quantity' => rand(2, 4)],
                        ['name' => 'Star Apple Tree', 'quantity' => rand(1, 3)]
                    ],
                    'ornamentals' => [],
                    'fingerlings' => [],
                    'fertilizers' => [],
                    'vegetables' => [], // Legacy
                    'fruits_status' => 'approved',
                    'created_at' => Carbon::create($year, $actualMonth, rand(1, 28))
                ]);
            }
        }

        // Ornamentals - Steady demand for beautification
        for ($month = 1; $month <= 20; $month++) {
            $year = $month <= 12 ? 2024 : 2025;
            $actualMonth = $month <= 12 ? $month : $month - 12;

            if ($year == 2025 && $actualMonth > 8) break;

            if ($month % 2 == 0) { // Every 2nd month
                SeedlingRequest::factory()->count(rand(2, 4))->create([
                    'status' => 'approved',
                    'seeds' => [],
                    'seedlings' => [],
                    'fruits' => [],
                    'ornamentals' => [
                        ['name' => 'Bougainvillea', 'quantity' => rand(5, 8)],
                        ['name' => 'Anthurium', 'quantity' => rand(8, 10)]
                    ],
                    'fingerlings' => [],
                    'fertilizers' => [],
                    'vegetables' => [], // Legacy
                    'ornamentals_status' => 'approved',
                    'created_at' => Carbon::create($year, $actualMonth, rand(1, 28))
                ]);
            }
        }

        // Fingerlings - Seasonal aquaculture projects
        for ($quarter = 1; $quarter <= 6; $quarter++) {
            $year = $quarter <= 4 ? 2024 : 2025;
            $month = $quarter <= 4 ? ($quarter * 3) : (($quarter - 4) * 3);

            if ($year == 2025 && $month > 8) break;

            SeedlingRequest::factory()->count(rand(1, 3))->create([
                'status' => 'approved',
                'seeds' => [],
                'seedlings' => [],
                'fruits' => [],
                'ornamentals' => [],
                'fingerlings' => [
                    ['name' => 'Tilapia Fingerlings', 'quantity' => rand(200, 500)],
                    ['name' => 'Milkfish (Bangus) Fingerling', 'quantity' => rand(100, 300)]
                ],
                'fertilizers' => [],
                'vegetables' => [], // Legacy
                'fingerlings_status' => 'approved',
                'purpose' => 'Fish farming livelihood project',
                'created_at' => Carbon::create($year, $month, rand(1, 28))
            ]);
        }

        // Fertilizers - Consistent demand throughout
        for ($month = 1; $month <= 20; $month++) {
            $year = $month <= 12 ? 2024 : 2025;
            $actualMonth = $month <= 12 ? $month : $month - 12;

            if ($year == 2025 && $actualMonth > 8) break;

            SeedlingRequest::factory()->count(rand(2, 4))->create([
                'status' => 'approved',
                'seeds' => [],
                'seedlings' => [],
                'fruits' => [],
                'ornamentals' => [],
                'fingerlings' => [],
                'fertilizers' => [
                    ['name' => 'Vermicast Fertilizer', 'quantity' => rand(8, 12)],
                    ['name' => 'Pre-processed Chicken Manure', 'quantity' => rand(5, 8)],
                    ['name' => 'Humic Acid', 'quantity' => rand(3, 6)]
                ],
                'vegetables' => [], // Legacy
                'fertilizers_status' => 'approved',
                'created_at' => Carbon::create($year, $actualMonth, rand(1, 28))
            ]);
        }
    }

    private function createSeasonalData(): void
    {
        $this->command->info('Creating seasonal variations...');

        // Summer planting requests (March-May 2024) - Seeds and ornamentals
        SeedlingRequest::factory()->count(6)->create([
            'status' => 'approved',
            'seeds' => [
                ['name' => 'Okra Seeds', 'quantity' => rand(8, 12)],
                ['name' => 'Yellow Pearl Squash Seeds', 'quantity' => rand(5, 8)]
            ],
            'ornamentals' => [
                ['name' => 'Sansevieria (Snake Plant)', 'quantity' => rand(10, 15)]
            ],
            'seedlings' => [],
            'fruits' => [],
            'fingerlings' => [],
            'fertilizers' => [],
            'vegetables' => [], // Legacy
            'seeds_status' => 'approved',
            'ornamentals_status' => 'approved',
            'created_at' => Carbon::create(2024, rand(3, 5), rand(1, 28))
        ]);

        // Rainy season requests (June-August 2024 & 2025) - All categories active
        foreach ([2024, 2025] as $year) {
            $maxMonth = $year == 2025 ? 8 : 8;

            SeedlingRequest::factory()->count(8)->create([
                'status' => 'approved',
                'seeds' => [
                    ['name' => 'Green Gem String Bean Seeds', 'quantity' => rand(10, 15)]
                ],
                'seedlings' => [
                    ['name' => 'Guava Seedling', 'quantity' => rand(4, 6)]
                ],
                'ornamentals' => [
                    ['name' => 'Gumamela (Hibiscus)', 'quantity' => rand(4, 6)]
                ],
                'fingerlings' => [
                    ['name' => 'Catfish Fingerling', 'quantity' => rand(100, 200)]
                ],
                'fruits' => [],
                'fertilizers' => [
                    ['name' => 'Urea (46-0-0)', 'quantity' => rand(3, 5)]
                ],
                'vegetables' => [ // Legacy mapping
                    ['name' => 'Guava Seedling', 'quantity' => rand(4, 6)]
                ],
                'seeds_status' => 'approved',
                'seedlings_status' => 'approved',
                'ornamentals_status' => 'approved',
                'fingerlings_status' => 'approved',
                'fertilizers_status' => 'approved',
                'vegetables_status' => 'approved', // Legacy
                'created_at' => Carbon::create($year, rand(6, $maxMonth), rand(1, 28))
            ]);
        }
    }

    private function createSpecialCases(): void
    {
        $this->command->info('Creating special cases and large orders...');

        // Large orders with multiple categories (scattered throughout timeline)
        SeedlingRequest::factory()->count(5)->largeOrder()->approved()->create([
            'seeds' => [
                ['name' => 'Golden Harvest Rice Seeds', 'quantity' => 50],
                ['name' => 'Pioneer Hybrid Corn Seeds', 'quantity' => 30]
            ],
            'fertilizers' => [
                ['name' => 'Ammonium Sulfate (21-0-0)', 'quantity' => 20]
            ],
            'seedlings' => [],
            'fruits' => [],
            'ornamentals' => [],
            'fingerlings' => [],
            'vegetables' => [], // Legacy
            'seeds_status' => 'approved',
            'fertilizers_status' => 'approved',
            'created_at' => Carbon::parse('2024-01-01')->addDays(rand(0, 600)) // Spread across 2024-2025
        ]);

        // Mixed category requests with different statuses
        SeedlingRequest::factory()->count(10)->create([
            'seeds' => [
                ['name' => 'Sunshine Carrot Seeds', 'quantity' => rand(15, 20)]
            ],
            'ornamentals' => [
                ['name' => 'Fortune Plant', 'quantity' => rand(8, 12)]
            ],
            'fertilizers' => [
                ['name' => 'Vermicast Fertilizer', 'quantity' => rand(5, 10)]
            ],
            'seedlings' => [],
            'fruits' => [],
            'fingerlings' => [],
            'vegetables' => [], // Legacy
            'status' => 'partially_approved',
            'seeds_status' => 'approved',
            'ornamentals_status' => 'rejected',
            'fertilizers_status' => 'approved',
            'created_at' => Carbon::parse('2025-01-01')->addDays(rand(0, 240))
        ]);

        // Document-required large orders
        SeedlingRequest::factory()->count(3)->withDocuments()->largeOrder()->create([
            'fingerlings' => [
                ['name' => 'Tilapia Fingerlings', 'quantity' => 1000]
            ],
            'purpose' => 'Large scale aquaculture project',
            'status' => 'under_review',
            'fingerlings_status' => null,
            'created_at' => Carbon::now()->subDays(rand(5, 15))
        ]);
    }
}