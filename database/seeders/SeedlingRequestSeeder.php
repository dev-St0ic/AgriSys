<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SeedlingRequest;
use App\Models\SeedlingRequestItem;
use App\Models\RequestCategory;
use App\Models\CategoryItem;
use App\Models\ItemSupplyLog;
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

        // Ensure we have users
        if (UserRegistration::count() < 15) {
            UserRegistration::factory(25)->create();
        }

        if (User::count() === 0) {
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

        // Create current year data - January 2025 to November 2025 (current date)
        $this->createCurrentYearData($categories);

        $this->command->info('Seedling requests seeding completed successfully!');
    }

    private function createCurrentYearData($categories): void
    {
        $this->command->info('Creating current year data with seasonal patterns...');

        // January 2025 - New year slow start
        $this->createMonthlyRequests('2025-01', 8, $categories, ['approved' => 80, 'rejected' => 15, 'partially_approved' => 5]);

        // February 2025 - Picking up
        $this->createMonthlyRequests('2025-02', 12, $categories, ['approved' => 75, 'rejected' => 15, 'partially_approved' => 10]);

        // March 2025 - Pre-peak season
        $this->createMonthlyRequests('2025-03', 16, $categories, ['approved' => 70, 'rejected' => 20, 'partially_approved' => 10]);

        // April 2025 - Peak planting season
        $this->createMonthlyRequests('2025-04', 20, $categories, ['approved' => 65, 'rejected' => 20, 'partially_approved' => 15]);

        // May 2025 - Continued peak
        $this->createMonthlyRequests('2025-05', 18, $categories, ['approved' => 70, 'rejected' => 15, 'partially_approved' => 15]);

        // June 2025 - Rainy season starts
        $this->createMonthlyRequests('2025-06', 14, $categories, ['approved' => 75, 'rejected' => 15, 'partially_approved' => 10]);

        // July 2025 - Rainy season
        $this->createMonthlyRequests('2025-07', 12, $categories, ['approved' => 70, 'rejected' => 20, 'partially_approved' => 10]);

        // August 2025 - Heavy rains
        $this->createMonthlyRequests('2025-08', 10, $categories, ['approved' => 75, 'rejected' => 15, 'partially_approved' => 10]);

        // September 2025 - End of rainy season
        $this->createMonthlyRequests('2025-09', 14, $categories, ['approved' => 80, 'rejected' => 10, 'partially_approved' => 10]);

        // October 2025 - Current dry season
        $this->createMonthlyRequests('2025-10', 16, $categories, ['approved' => 60, 'rejected' => 15, 'partially_approved' => 15, 'pending' => 10]);

        // November 2025 - Current month (only 2 days)
        $this->createMonthlyRequests('2025-11', 2, $categories, ['approved' => 40, 'rejected' => 20, 'partially_approved' => 20, 'pending' => 20], 2);
    }

    private function createMonthlyRequests(string $month, int $count, $categories, array $statusDistribution, int $maxDay = 28): void
    {
        $users = User::all();
        $userRegistrations = UserRegistration::all();

        // Check if we have the necessary data
        if ($users->isEmpty()) {
            $this->command->error("No users found! Please ensure users exist in the database.");
            return;
        }

        if ($userRegistrations->isEmpty()) {
            $this->command->error("No user registrations found! Please ensure user registrations exist in the database.");
            return;
        }

        for ($i = 0; $i < $count; $i++) {
            // Random day in the month (limited by maxDay for current month)
            $day = rand(1, $maxDay);
            $createdDate = Carbon::parse("{$month}-{$day}");

            // Skip weekends for realistic data
            while ($createdDate->isWeekend()) {
                $createdDate->addDay();
            }

            // Determine status based on distribution
            $status = $this->getRandomStatus($statusDistribution);
            $selectedUser = $userRegistrations->random();

            // Calculate processing time (in hours for more realistic times)
            $processingHours = match($status) {
                'approved' => rand(24, 72),      // 1-3 days
                'rejected' => rand(12, 48),      // 0.5-2 days
                'partially_approved' => rand(48, 96), // 2-4 days
                default => 0
            };

            // Create request
            $request = SeedlingRequest::create([
                'user_id' => $selectedUser->id,
                'request_number' => SeedlingRequest::generateRequestNumber(),
                'first_name' => fake()->firstName(),
                'middle_name' => rand(0, 1) ? fake()->firstName() : null,
                'last_name' => fake()->lastName(),
                'extension_name' => rand(0, 10) > 8 ? ['Jr.', 'Sr.', 'III'][rand(0, 2)] : null,
                'contact_number' => '09' . rand(100000000, 999999999),
                'email' => fake()->email(),
                'address' => fake()->streetAddress(),
                'barangay' => $this->getRandomBarangay(),
                'planting_location' => fake()->address(),
                'purpose' => $this->getRandomPurpose(),
                'total_quantity' => 0,
                'approved_quantity' => null,
                'preferred_delivery_date' => $createdDate->copy()->addDays(rand(7, 21)),
                'status' => $status,
                'reviewed_by' => $status !== 'pending' ? $users->random()->id : null,
                'reviewed_at' => $status !== 'pending' ? $createdDate->copy()->addHours($processingHours) : null,
                'remarks' => $this->getStatusRemarks($status),
                'approved_at' => $status === 'approved' ? $createdDate->copy()->addHours($processingHours) : null,
                'rejected_at' => $status === 'rejected' ? $createdDate->copy()->addHours($processingHours) : null,
                'created_at' => $createdDate,
                'updated_at' => $status !== 'pending' ? $createdDate->copy()->addHours($processingHours) : $createdDate,
            ]);

            // Add items to request
            $this->addItemsToRequest($request, $categories, $status, $selectedUser->id);

            // Update totals
            $totalQty = $request->items()->sum('requested_quantity');
            $approvedQty = $request->items()->sum('approved_quantity');

            $request->update([
                'total_quantity' => $totalQty,
                'approved_quantity' => $status === 'approved' ? $approvedQty : ($status === 'partially_approved' ? $approvedQty : null),
            ]);

            // Deduct supplies for approved requests
            if (in_array($status, ['approved', 'partially_approved'])) {
                $this->deductSuppliesFromRequest($request, $users->random()->id);
            }
        }

        $this->command->info("Created {$count} requests for {$month}");
    }

    private function addItemsToRequest($request, $categories, $status, $userId): void
    {
        // Check if categories exist
        if ($categories->isEmpty()) {
            $this->command->error("No categories found! Cannot create request items.");
            return;
        }

        // Select 1-3 categories randomly
        $numCategories = rand(1, 3);
        $selectedCategories = $categories->random(min($numCategories, $categories->count()));

        foreach ($selectedCategories as $category) {
            $categoryItems = $category->items;
            if ($categoryItems->isEmpty()) continue;

            // Select 1-2 items per category
            $itemCount = rand(1, 2);
            $selectedItems = $categoryItems->random(min($itemCount, $categoryItems->count()));

            foreach ($selectedItems as $categoryItem) {
                // Realistic quantities based on category
                $requestedQty = match($category->name) {
                    'fingerlings' => rand(50, 300),
                    'fertilizers' => rand(5, 25),
                    'seeds' => rand(2, 10),
                    'seedlings' => rand(3, 15),
                    'fruits' => rand(1, 8),
                    'ornamentals' => rand(3, 12),
                    default => rand(2, 10)
                };

                // Determine item status
                $itemStatus = match($status) {
                    'approved' => 'approved',
                    'rejected' => 'rejected',
                    'partially_approved' => (rand(1, 100) <= 70) ? 'approved' : 'rejected',
                    default => 'pending'
                };

                SeedlingRequestItem::create([
                    'seedling_request_id' => $request->id,
                    'user_id' => $userId,
                    'category_id' => $category->id,
                    'category_item_id' => $categoryItem->id,
                    'item_name' => $categoryItem->name,
                    'requested_quantity' => $requestedQty,
                    'approved_quantity' => $itemStatus === 'approved' ? $this->getApprovedQuantity($categoryItem, $requestedQty) : null,
                    'status' => $itemStatus,
                    'rejection_reason' => $itemStatus === 'rejected' ? $this->getRejectionReason() : null,
                ]);
            }
        }
    }

    // Helper methods
    private function getRandomStatus(array $distribution): string
    {
        $total = array_sum($distribution);
        $random = rand(1, $total);
        $sum = 0;

        foreach ($distribution as $status => $weight) {
            $sum += $weight;
            if ($random <= $sum) {
                return $status;
            }
        }

        return 'pending';
    }

    private function getRandomBarangay(): string
    {
        $barangays = [
            'Poblacion', 'San Roque', 'San Antonio', 'Magsaysay', 'Bagong Silang',
            'Landayan', 'Pacita 1', 'Pacita 2', 'Riverside', 'San Vicente'
        ];
        return $barangays[array_rand($barangays)];
    }

    private function getRandomPurpose(): string
    {
        $purposes = [
            'Backyard farming', 'Community garden project', 'Livelihood program',
            'Food security initiative', 'School project', 'Urban farming',
            'Family consumption', 'Local market selling'
        ];
        return $purposes[array_rand($purposes)];
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
                'Unable to fulfill request at this time.',
            ])->random(),
            'partially_approved' => collect([
                'Some items approved. Others out of stock.',
                'Partial approval due to limited availability.',
                'Available items approved. Others pending restock.',
            ])->random(),
            default => null,
        };
    }

    /**
     * Get approved quantity based on available supply
     */
    private function getApprovedQuantity($categoryItem, int $requestedQty): int
    {
        // Refresh the item to get current supply
        $categoryItem->refresh();

        // For realistic simulation, sometimes approve full amount even if stock is lower
        // (representing supplies that arrive between request and approval)
        if (rand(1, 100) <= 20) {
            return $requestedQty; // 20% chance of full approval regardless of current stock
        }

        // Otherwise, approve based on available supply
        return min($requestedQty, max(0, $categoryItem->current_supply));
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

    /**
     * Deduct supplies for approved request items
     */
    private function deductSuppliesFromRequest($request, int $adminUserId): void
    {
        foreach ($request->items()->where('status', 'approved')->get() as $item) {
            $categoryItem = CategoryItem::find($item->category_item_id);
            if ($categoryItem && $item->approved_quantity > 0) {
                $categoryItem->distributeSupply(
                    $item->approved_quantity,
                    $adminUserId,
                    "Distributed for request #{$request->request_number} - {$item->item_name}",
                    'SeedlingRequest',
                    $request->id
                );
            }
        }
    }
}
