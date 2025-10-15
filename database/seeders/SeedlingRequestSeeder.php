<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SeedlingRequest;
use App\Models\SeedlingRequestItem;
use App\Models\RequestCategory;
use App\Models\CategoryItem;
use App\Models\User;
use App\Models\UserRegistration;
use Carbon\Carbon;

class SeedlingRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating seedling requests with items...');

        // Ensure we have users in the user_registration table
        if (UserRegistration::count() < 10) {
            UserRegistration::factory(20)->create();
        }

        // Ensure at least one user exists
        if (User::count() === 0) {
            $this->command->info('Creating default users...');
            User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@agrisys.com',
                'password' => bcrypt('password'),
            ]);
            User::factory(3)->create();
        }

        // Get categories and items
        $categories = RequestCategory::with('items')->get();
        if ($categories->isEmpty()) {
            $this->command->error('Please run RequestCategorySeeder first!');
            return;
        }

        // === 2024 HISTORICAL DATA ===
        $this->create2024Data($categories);

        // === 2025 CURRENT YEAR DATA ===
        $this->create2025Data($categories);

        $this->command->info('Seedling requests seeding completed successfully!');
    }

    private function create2024Data($categories): void
    {
        $this->command->info('Creating 2024 historical data...');

        // Q1 2024 (January - March) - Post-holiday slow period
        $this->createRequests(12, '2024-01-01', 89, 'approved', $categories, 85);
        $this->createRequests(3, '2024-01-01', 89, 'rejected', $categories, 15);

        // Q2 2024 (April - June) - Peak planting season
        $this->createRequests(25, '2024-04-01', 91, 'approved', $categories, 80);
        $this->createRequests(5, '2024-04-01', 91, 'rejected', $categories, 10);
        $this->createRequests(5, '2024-04-01', 91, 'partially_approved', $categories, 60);

        // Q3 2024 (July - September) - Rainy season
        $this->createRequests(15, '2024-07-01', 92, 'approved', $categories, 75);
        $this->createRequests(5, '2024-07-01', 92, 'rejected', $categories, 20);
        $this->createRequests(3, '2024-07-01', 92, 'partially_approved', $categories, 55);

        // Q4 2024 (October - December) - Harvest/preparation season
        $this->createRequests(18, '2024-10-01', 92, 'approved', $categories, 82);
        $this->createRequests(4, '2024-10-01', 92, 'rejected', $categories, 12);
        $this->createRequests(2, '2024-10-01', 92, 'partially_approved', $categories, 65);
    }

    private function create2025Data($categories): void
    {
        $this->command->info('Creating 2025 current year data...');

        // Q1 2025 - Growing demand
        $this->createRequests(18, '2025-01-01', 89, 'approved', $categories, 80);
        $this->createRequests(4, '2025-01-01', 89, 'rejected', $categories, 15);
        $this->createRequests(3, '2025-01-01', 89, 'partially_approved', $categories, 58);

        // Q2 2025 - Peak season continues
        $this->createRequests(28, '2025-04-01', 91, 'approved', $categories, 78);
        $this->createRequests(6, '2025-04-01', 91, 'rejected', $categories, 12);
        $this->createRequests(6, '2025-04-01', 91, 'partially_approved', $categories, 62);

        // Q3 2025 (current) - Mix of statuses
        $this->createRequests(12, '2025-07-01', 58, 'approved', $categories, 70);
        $this->createRequests(3, '2025-07-01', 58, 'rejected', $categories, 15);
        $this->createRequests(2, '2025-07-01', 58, 'partially_approved', $categories, 50);
        
        // Recent pending requests - show active pipeline
        $this->createRequests(10, Carbon::now()->subDays(14)->format('Y-m-d'), 14, 'pending', $categories);
    }

    private function createRequests(
        int $count, 
        string $startDate, 
        int $dayRange, 
        string $status, 
        $categories,
        int $approvalRate = 100
    ): void
    {
        $users = User::all();
        $userRegistrations = UserRegistration::all();

        for ($i = 0; $i < $count; $i++) {
            // More realistic date distribution within the range
            $daysOffset = $this->getWeightedRandomDay($dayRange);
            $createdDate = Carbon::parse($startDate)->addDays($daysOffset);
            
            // Skip weekends for more realistic data
            while ($createdDate->isWeekend()) {
                $createdDate->addDay();
            }

            $selectedUserRegistration = $userRegistrations->random();

            // Variable processing time based on status
            $processingHours = match($status) {
                'approved' => rand(24, 72),
                'rejected' => rand(12, 48),
                'partially_approved' => rand(36, 96),
                default => 0
            };

            // Create main request
            $request = SeedlingRequest::create([
                'user_id' => $selectedUserRegistration->id,
                'request_number' => SeedlingRequest::generateRequestNumber(),
                'first_name' => fake()->firstName(),
                'middle_name' => rand(0, 1) ? fake()->firstName() : null,
                'last_name' => fake()->lastName(),
                'extension_name' => rand(0, 10) > 7 ? ['Jr.', 'Sr.', 'III'][rand(0, 2)] : null,
                'contact_number' => fake()->phoneNumber(),
                'email' => fake()->email(),
                'address' => fake()->streetAddress(),
                'barangay' => $this->getWeightedBarangay(),
                'planting_location' => fake()->address(),
                'purpose' => $this->getRealisticPurpose(),
                'total_quantity' => 0,
                'approved_quantity' => null,
                'preferred_delivery_date' => $createdDate->copy()->addDays(rand(7, 30)),
                'status' => $status,
                'reviewed_by' => $status !== 'pending' ? $users->random()->id : null,
                'reviewed_at' => $status !== 'pending' ? $createdDate->copy()->addHours($processingHours) : null,
                'remarks' => $this->getStatusRemarks($status),
                'approved_at' => $status === 'approved' ? $createdDate->copy()->addHours($processingHours) : null,
                'rejected_at' => $status === 'rejected' ? $createdDate->copy()->addHours($processingHours) : null,
                'created_at' => $createdDate,
                'updated_at' => $status !== 'pending' ? $createdDate->copy()->addHours($processingHours) : $createdDate,
            ]);

            // Add items to the request
            $this->addItemsToRequest($request, $categories, $status, $selectedUserRegistration->id, $approvalRate);

            // Update total quantity
            $request->update([
                'total_quantity' => $request->items()->sum('requested_quantity'),
                'approved_quantity' => $status === 'approved' ? $request->items()->sum('approved_quantity') : null,
            ]);
        }
    }

    private function addItemsToRequest($request, $categories, $requestStatus, $userId, $approvalRate = 100): void
    {
        // More realistic item selection
        $numCategories = $this->getWeightedCategoryCount();
        $selectedCategories = $categories->random(min($numCategories, $categories->count()));

        foreach ($selectedCategories as $category) {
            $categoryItems = $category->items;
            if ($categoryItems->isEmpty()) continue;

            // Realistic item counts per category
            $itemCount = match($category->name) {
                'seeds' => rand(2, 4),
                'seedlings' => rand(2, 5),
                'fertilizers' => rand(1, 3),
                'fingerlings' => rand(1, 2),
                'ornamentals' => rand(2, 4),
                'fruits' => rand(1, 3),
                default => rand(1, 3)
            };

            $selectedItems = $categoryItems->random(min($itemCount, $categoryItems->count()));

            foreach ($selectedItems as $categoryItem) {
                // Realistic quantity ranges per category
                $requestedQty = match($category->name) {
                    'fingerlings' => rand(100, 500),
                    'fertilizers' => rand(5, 50),
                    'seeds' => rand(5, 25),
                    'seedlings' => rand(5, 20),
                    'fruits' => rand(3, 15),
                    'ornamentals' => rand(5, 30),
                    default => rand(5, 20)
                };

                // Determine item status with weighted logic
                $itemStatus = match($requestStatus) {
                    'approved' => 'approved',
                    'rejected' => 'rejected',
                    'partially_approved' => (rand(1, 100) <= $approvalRate) ? 'approved' : 'rejected',
                    default => 'pending'
                };

                SeedlingRequestItem::create([
                    'seedling_request_id' => $request->id,
                    'user_id' => $userId,
                    'category_id' => $category->id,
                    'category_item_id' => $categoryItem->id,
                    'item_name' => $categoryItem->name,
                    'requested_quantity' => $requestedQty,
                    'approved_quantity' => $itemStatus === 'approved' ? $requestedQty : null,
                    'status' => $itemStatus,
                    'rejection_reason' => $itemStatus === 'rejected' ? $this->getRejectionReason() : null,
                ]);
            }
        }
    }

    // Helper methods
    private function getWeightedRandomDay(int $maxDays): int
    {
        // Creates more activity at the beginning and end of periods
        $random = rand(0, 100);
        if ($random < 30) return rand(0, (int)($maxDays * 0.25));
        if ($random < 60) return rand((int)($maxDays * 0.25), (int)($maxDays * 0.75));
        return rand((int)($maxDays * 0.75), $maxDays);
    }

    private function getWeightedBarangay(): string
    {
        $barangays = [
            'Poblacion' => 20,
            'San Roque' => 15,
            'San Antonio' => 15,
            'Magsaysay' => 12,
            'Bagong Silang' => 10,
            'Landayan' => 8,
            'Pacita 1' => 8,
            'Pacita 2' => 5,
            'Riverside' => 4,
            'San Vicente' => 3,
        ];
        
        $random = rand(1, array_sum($barangays));
        $sum = 0;
        foreach ($barangays as $barangay => $weight) {
            $sum += $weight;
            if ($random <= $sum) return $barangay;
        }
        return 'Poblacion';
    }

    private function getRealisticPurpose(): string
    {
        $purposes = [
            'Backyard farming' => 30,
            'Community garden project' => 20,
            'Livelihood program' => 18,
            'Food security initiative' => 15,
            'School project' => 10,
            'Urban farming' => 7,
        ];
        
        $random = rand(1, array_sum($purposes));
        $sum = 0;
        foreach ($purposes as $purpose => $weight) {
            $sum += $weight;
            if ($random <= $sum) return $purpose;
        }
        return 'Backyard farming';
    }

    private function getStatusRemarks(string $status): ?string
    {
        return match($status) {
            'approved' => collect([
                'Request approved and ready for pickup.',
                'All items available. Please collect within 7 days.',
                'Approved. Schedule pickup at your convenience.',
                'Request processed successfully.',
            ])->random(),
            'rejected' => collect([
                'Insufficient stock available.',
                'Request exceeds allocation limit.',
                'Incomplete documentation provided.',
                'Outside service coverage area.',
                'Unable to fulfill request at this time.',
            ])->random(),
            'partially_approved' => collect([
                'Some items approved. Others out of stock.',
                'Partial approval due to limited availability.',
                'Available items approved. Others pending restock.',
                'Request partially fulfilled based on current inventory.',
            ])->random(),
            default => null,
        };
    }

    private function getWeightedCategoryCount(): int
    {
        $random = rand(1, 100);
        if ($random <= 50) return 1; // 50% - single category
        if ($random <= 80) return 2; // 30% - two categories
        return 3; // 20% - three categories
    }

    private function getRejectionReason(): string
    {
        return collect([
            'Insufficient stock',
            'Out of season',
            'Limited availability',
            'Exceeds individual allocation',
            'Currently unavailable',
        ])->random();
    }
}