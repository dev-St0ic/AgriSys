<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SeedlingRequest;
use App\Models\User;
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

        // Ensure at least one user exists for foreign key relationships
        if (User::count() === 0) {
            $this->command->info('Creating default users...');
            User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@agrisys.com',
                'password' => bcrypt('password'),
            ]);
            
            // Create a few more users for variety
            User::factory(3)->create();
        }

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
        SeedlingRequest::factory()->count(15)->approved()->state([
            'created_at' => Carbon::parse('2024-01-01')->addDays(rand(0, 89)),
            'updated_at' => Carbon::parse('2024-01-01')->addDays(rand(0, 89)),
        ])->create();

        SeedlingRequest::factory()->count(8)->seedsOnly()->approved()->state([
            'created_at' => Carbon::parse('2024-01-01')->addDays(rand(0, 89)),
            'updated_at' => Carbon::parse('2024-01-01')->addDays(rand(0, 89)),
        ])->create();

        SeedlingRequest::factory()->count(5)->ornamentalsOnly()->approved()->state([
            'created_at' => Carbon::parse('2024-01-01')->addDays(rand(0, 89)),
            'updated_at' => Carbon::parse('2024-01-01')->addDays(rand(0, 89)),
        ])->create();

        // Q2 2024 (April - June) - Peak planting season
        SeedlingRequest::factory()->count(20)->approved()->state([
            'created_at' => Carbon::parse('2024-04-01')->addDays(rand(0, 91)),
            'updated_at' => Carbon::parse('2024-04-01')->addDays(rand(0, 91)),
        ])->create();

        SeedlingRequest::factory()->count(10)->seedsOnly()->approved()->state([
            'created_at' => Carbon::parse('2024-04-01')->addDays(rand(0, 91)),
            'updated_at' => Carbon::parse('2024-04-01')->addDays(rand(0, 91)),
        ])->create();

        SeedlingRequest::factory()->count(8)->fingerlingsOnly()->approved()->state([
            'created_at' => Carbon::parse('2024-04-01')->addDays(rand(0, 91)),
            'updated_at' => Carbon::parse('2024-04-01')->addDays(rand(0, 91)),
        ])->create();

        // Q3 2024 (July - September) - Rainy season
        SeedlingRequest::factory()->count(18)->approved()->state([
            'created_at' => Carbon::parse('2024-07-01')->addDays(rand(0, 92)),
            'updated_at' => Carbon::parse('2024-07-01')->addDays(rand(0, 92)),
        ])->create();

        SeedlingRequest::factory()->count(12)->ornamentalsOnly()->approved()->state([
            'created_at' => Carbon::parse('2024-07-01')->addDays(rand(0, 92)),
            'updated_at' => Carbon::parse('2024-07-01')->addDays(rand(0, 92)),
        ])->create();

        // Q4 2024 (October - December) - Year-end requests
        SeedlingRequest::factory()->count(16)->approved()->state([
            'created_at' => Carbon::parse('2024-10-01')->addDays(rand(0, 92)),
            'updated_at' => Carbon::parse('2024-10-01')->addDays(rand(0, 92)),
        ])->create();

        SeedlingRequest::factory()->count(6)->fingerlingsOnly()->approved()->state([
            'created_at' => Carbon::parse('2024-10-01')->addDays(rand(0, 92)),
            'updated_at' => Carbon::parse('2024-10-01')->addDays(rand(0, 92)),
        ])->create();

        SeedlingRequest::factory()->count(8)->seedsOnly()->approved()->state([
            'created_at' => Carbon::parse('2024-10-01')->addDays(rand(0, 92)),
            'updated_at' => Carbon::parse('2024-10-01')->addDays(rand(0, 92)),
        ])->create();
    }

    private function create2025Data(): void
    {
        $this->command->info('Creating 2025 current year data...');

        // Q1 2025 (January - March) - New year initiatives
        SeedlingRequest::factory()->count(20)->approved()->state([
            'created_at' => Carbon::parse('2025-01-01')->addDays(rand(0, 89)),
            'updated_at' => Carbon::parse('2025-01-01')->addDays(rand(0, 89)),
        ])->create();

        SeedlingRequest::factory()->count(10)->ornamentalsOnly()->approved()->state([
            'created_at' => Carbon::parse('2025-01-01')->addDays(rand(0, 89)),
            'updated_at' => Carbon::parse('2025-01-01')->addDays(rand(0, 89)),
        ])->create();

        // Q2 2025 (April - June) - Peak season with all categories
        SeedlingRequest::factory()->count(25)->approved()->state([
            'created_at' => Carbon::parse('2025-04-01')->addDays(rand(0, 91)),
            'updated_at' => Carbon::parse('2025-04-01')->addDays(rand(0, 91)),
        ])->create();

        SeedlingRequest::factory()->count(15)->seedsOnly()->approved()->state([
            'created_at' => Carbon::parse('2025-04-01')->addDays(rand(0, 91)),
            'updated_at' => Carbon::parse('2025-04-01')->addDays(rand(0, 91)),
        ])->create();

        SeedlingRequest::factory()->count(10)->fingerlingsOnly()->approved()->state([
            'created_at' => Carbon::parse('2025-04-01')->addDays(rand(0, 91)),
            'updated_at' => Carbon::parse('2025-04-01')->addDays(rand(0, 91)),
        ])->create();

        // Q3 2025 (July - August, current period)
        SeedlingRequest::factory()->count(15)->approved()->state([
            'created_at' => Carbon::parse('2025-07-01')->addDays(rand(0, 58)),
            'updated_at' => Carbon::parse('2025-07-01')->addDays(rand(0, 58)),
        ])->create();

        SeedlingRequest::factory()->count(8)->ornamentalsOnly()->approved()->state([
            'created_at' => Carbon::parse('2025-07-01')->addDays(rand(0, 58)),
            'updated_at' => Carbon::parse('2025-07-01')->addDays(rand(0, 58)),
        ])->create();

        // Recent pending requests (August 2025) - Mixed categories
        SeedlingRequest::factory()->count(8)->pending()->state([
            'created_at' => Carbon::now()->subDays(rand(1, 14)),
            'updated_at' => Carbon::now()->subDays(rand(1, 14)),
        ])->create();

        SeedlingRequest::factory()->count(4)->seedsOnly()->pending()->state([
            'created_at' => Carbon::now()->subDays(rand(1, 14)),
            'updated_at' => Carbon::now()->subDays(rand(1, 14)),
        ])->create();

        SeedlingRequest::factory()->count(3)->fingerlingsOnly()->pending()->state([
            'created_at' => Carbon::now()->subDays(rand(1, 14)),
            'updated_at' => Carbon::now()->subDays(rand(1, 14)),
        ])->create();

        // Recent rejected requests (mixed dates 2025)
        SeedlingRequest::factory()->count(8)->rejected()->state([
            'created_at' => Carbon::parse('2025-01-01')->addDays(rand(0, 240)),
            'updated_at' => Carbon::parse('2025-01-01')->addDays(rand(0, 240)),
        ])->create();

        SeedlingRequest::factory()->count(4)->ornamentalsOnly()->rejected()->state([
            'created_at' => Carbon::parse('2025-01-01')->addDays(rand(0, 240)),
            'updated_at' => Carbon::parse('2025-01-01')->addDays(rand(0, 240)),
        ])->create();

        // Partially approved requests (2025) - Mix of categories
        SeedlingRequest::factory()->count(12)->partiallyApproved()->state([
            'created_at' => Carbon::parse('2025-01-01')->addDays(rand(0, 240)),
            'updated_at' => Carbon::parse('2025-01-01')->addDays(rand(0, 240)),
        ])->create();

        SeedlingRequest::factory()->count(6)->partiallyApproved()->state([
            'created_at' => Carbon::parse('2025-01-01')->addDays(rand(0, 240)),
            'updated_at' => Carbon::parse('2025-01-01')->addDays(rand(0, 240)),
        ])->create();
    }

    private function createPopularItemsData(): void
    {
        $this->command->info('Creating popular items across categories...');

        // Seeds - Popular throughout the year
        for ($month = 1; $month <= 20; $month++) {
            $year = $month <= 12 ? 2024 : 2025;
            $actualMonth = $month <= 12 ? $month : $month - 12;

            if ($year == 2025 && $actualMonth > 8) break;

            $createdDate = Carbon::create($year, $actualMonth, rand(1, 28));
            
            SeedlingRequest::factory()
                ->count(rand(2, 4))
                ->state([
                    'created_at' => $createdDate,
                    'updated_at' => $createdDate,
                    'status' => 'approved',
                    'seeds' => json_encode([
                        ['name' => 'Red Ruby Tomato Seeds', 'quantity' => rand(10, 15)],
                        ['name' => 'Green Gem String Bean Seeds', 'quantity' => rand(8, 12)]
                    ]),
                    'fertilizers' => json_encode([
                        ['name' => 'Vermicast Fertilizer', 'quantity' => rand(3, 8)]
                    ]),
                    'seeds_status' => 'approved',
                    'fertilizers_status' => 'approved',
                    'seedlings' => null,
                    'fruits' => null,
                    'ornamentals' => null,
                    'fingerlings' => null,
                ])
                ->create();
        }

        // Seedlings - High demand in planting seasons
        for ($quarter = 1; $quarter <= 6; $quarter++) {
            $year = $quarter <= 4 ? 2024 : 2025;
            $month = $quarter <= 4 ? ($quarter * 3) : (($quarter - 4) * 3);

            if ($year == 2025 && $month > 8) break;

            $createdDate = Carbon::create($year, $month, rand(1, 28));

            SeedlingRequest::factory()
                ->count(rand(3, 6))
                ->state([
                    'created_at' => $createdDate,
                    'updated_at' => $createdDate,
                    'status' => 'approved',
                    'seedlings' => json_encode([
                        ['name' => 'Calamansi Seedling', 'quantity' => rand(5, 8)],
                        ['name' => 'Papaya Seedling', 'quantity' => rand(3, 5)]
                    ]),
                    'seedlings_status' => 'approved',
                    'seeds' => null,
                    'fruits' => null,
                    'ornamentals' => null,
                    'fingerlings' => null,
                    'fertilizers' => null,
                ])
                ->create();
        }
    }

    private function createSeasonalData(): void
    {
        $this->command->info('Creating seasonal variations...');

        // Summer planting requests (March-May 2024) - Seeds and ornamentals
        SeedlingRequest::factory()
            ->count(6)
            ->state([
                'created_at' => Carbon::create(2024, rand(3, 5), rand(1, 28)),
                'updated_at' => Carbon::create(2024, rand(3, 5), rand(1, 28)),
                'status' => 'approved',
                'seeds' => json_encode([
                    ['name' => 'Okra Seeds', 'quantity' => rand(8, 12)],
                    ['name' => 'Yellow Pearl Squash Seeds', 'quantity' => rand(5, 8)]
                ]),
                'ornamentals' => json_encode([
                    ['name' => 'Sansevieria (Snake Plant)', 'quantity' => rand(10, 15)]
                ]),
                'seeds_status' => 'approved',
                'ornamentals_status' => 'approved',
                'seedlings' => null,
                'fruits' => null,
                'fingerlings' => null,
                'fertilizers' => null,
            ])
            ->create();

        // Rainy season requests (June-August 2024 & 2025) - All categories active
        foreach ([2024, 2025] as $year) {
            $maxMonth = $year == 2025 ? 8 : 8;

            $createdDate = Carbon::create($year, rand(6, $maxMonth), rand(1, 28));

            SeedlingRequest::factory()
                ->count(8)
                ->state([
                    'created_at' => $createdDate,
                    'updated_at' => $createdDate,
                    'status' => 'approved',
                    'seeds' => json_encode([
                        ['name' => 'Green Gem String Bean Seeds', 'quantity' => rand(10, 15)]
                    ]),
                    'seedlings' => json_encode([
                        ['name' => 'Guava Seedling', 'quantity' => rand(4, 6)]
                    ]),
                    'ornamentals' => json_encode([
                        ['name' => 'Gumamela (Hibiscus)', 'quantity' => rand(4, 6)]
                    ]),
                    'fingerlings' => json_encode([
                        ['name' => 'Catfish Fingerling', 'quantity' => rand(100, 200)]
                    ]),
                    'fertilizers' => json_encode([
                        ['name' => 'Urea (46-0-0)', 'quantity' => rand(3, 5)]
                    ]),
                    'seeds_status' => 'approved',
                    'seedlings_status' => 'approved',
                    'ornamentals_status' => 'approved',
                    'fingerlings_status' => 'approved',
                    'fertilizers_status' => 'approved',
                    'fruits' => null,
                ])
                ->create();
        }
    }

    private function createSpecialCases(): void
    {
        $this->command->info('Creating special cases and large orders...');

        // Large orders with multiple categories (scattered throughout timeline)
        SeedlingRequest::factory()
            ->count(5)
            ->largeOrder()
            ->approved()
            ->state([
                'created_at' => Carbon::parse('2024-01-01')->addDays(rand(0, 600)),
                'updated_at' => Carbon::parse('2024-01-01')->addDays(rand(0, 600)),
                'seeds' => json_encode([
                    ['name' => 'Golden Harvest Rice Seeds', 'quantity' => 50],
                    ['name' => 'Pioneer Hybrid Corn Seeds', 'quantity' => 30]
                ]),
                'fertilizers' => json_encode([
                    ['name' => 'Ammonium Sulfate (21-0-0)', 'quantity' => 20]
                ]),
                'seeds_status' => 'approved',
                'fertilizers_status' => 'approved',
                'seedlings' => null,
                'fruits' => null,
                'ornamentals' => null,
                'fingerlings' => null,
            ])
            ->create();

        // Mixed category requests with different statuses
        SeedlingRequest::factory()
            ->count(10)
            ->state([
                'created_at' => Carbon::parse('2025-01-01')->addDays(rand(0, 240)),
                'updated_at' => Carbon::parse('2025-01-01')->addDays(rand(0, 240)),
                'seeds' => json_encode([
                    ['name' => 'Sunshine Carrot Seeds', 'quantity' => rand(15, 20)]
                ]),
                'ornamentals' => json_encode([
                    ['name' => 'Fortune Plant', 'quantity' => rand(8, 12)]
                ]),
                'fertilizers' => json_encode([
                    ['name' => 'Vermicast Fertilizer', 'quantity' => rand(5, 10)]
                ]),
                'status' => 'partially_approved',
                'seeds_status' => 'approved',
                'ornamentals_status' => 'rejected',
                'fertilizers_status' => 'approved',
                'seedlings' => null,
                'fruits' => null,
                'fingerlings' => null,
            ])
            ->create();

        // Document-required large orders
        SeedlingRequest::factory()
            ->count(3)
            ->withDocuments()
            ->largeOrder()
            ->state([
                'created_at' => Carbon::now()->subDays(rand(5, 15)),
                'updated_at' => Carbon::now()->subDays(rand(5, 15)),
                'fingerlings' => json_encode([
                    ['name' => 'Tilapia Fingerlings', 'quantity' => 1000]
                ]),
                'purpose' => 'Large scale aquaculture project',
                'status' => 'under_review',
                'fingerlings_status' => null,
                'seeds' => null,
                'seedlings' => null,
                'fruits' => null,
                'ornamentals' => null,
                'fertilizers' => null,
            ])
            ->create();
    }
}