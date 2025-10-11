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

        // Q1 2024 (January - March)
        $this->createRequests(15, '2024-01-01', 89, 'approved', $categories);

        // Q2 2024 (April - June) - Peak season
        $this->createRequests(20, '2024-04-01', 91, 'approved', $categories);

        // Q3 2024 (July - September)
        $this->createRequests(18, '2024-07-01', 92, 'approved', $categories);

        // Q4 2024 (October - December)
        $this->createRequests(16, '2024-10-01', 92, 'approved', $categories);
    }

    private function create2025Data($categories): void
    {
        $this->command->info('Creating 2025 current year data...');

        // Q1 2025
        $this->createRequests(20, '2025-01-01', 89, 'approved', $categories);

        // Q2 2025
        $this->createRequests(25, '2025-04-01', 91, 'approved', $categories);

        // Q3 2025 (current)
        $this->createRequests(15, '2025-07-01', 58, 'approved', $categories);

        // Recent pending requests
        $this->createRequests(8, Carbon::now()->subDays(14)->format('Y-m-d'), 14, 'pending', $categories);

        // Rejected requests
        $this->createRequests(8, '2025-01-01', 240, 'rejected', $categories);

        // Partially approved requests
        $this->createRequests(12, '2025-01-01', 240, 'partially_approved', $categories);
    }

    private function createRequests(int $count, string $startDate, int $dayRange, string $status, $categories): void
    {
        $users = User::all();
        $userRegistrations = UserRegistration::all();

        for ($i = 0; $i < $count; $i++) {
            $createdDate = Carbon::parse($startDate)->addDays(rand(0, $dayRange));

            // Create main request
            $request = SeedlingRequest::create([
                'user_id' => $userRegistrations->random()->id,
                'request_number' => SeedlingRequest::generateRequestNumber(),
                'first_name' => fake()->firstName(),
                'middle_name' => rand(0, 1) ? fake()->firstName() : null,
                'last_name' => fake()->lastName(),
                'extension_name' => rand(0, 10) > 7 ? ['Jr.', 'Sr.', 'III'][rand(0, 2)] : null,
                'contact_number' => fake()->phoneNumber(),
                'email' => fake()->email(),
                'address' => fake()->streetAddress(),
                'barangay' => fake()->city(),
                'planting_location' => fake()->address(),
                'purpose' => ['Community garden project', 'Backyard farming', 'School project', 'Livelihood program', 'Food security initiative'][rand(0, 4)],
                'total_quantity' => 0, // Will be calculated
                'approved_quantity' => null,
                'preferred_delivery_date' => $createdDate->copy()->addDays(rand(7, 30)),
                'status' => $status,
                'reviewed_by' => $status !== 'pending' ? $users->random()->id : null,
                'reviewed_at' => $status !== 'pending' ? $createdDate->copy()->addHours(rand(1, 48)) : null,
                'remarks' => $status === 'approved' ? 'Request approved and ready for pickup.' : ($status === 'rejected' ? 'Cannot fulfill request at this time.' : null),
                'approved_at' => $status === 'approved' ? $createdDate->copy()->addHours(rand(1, 48)) : null,
                'rejected_at' => $status === 'rejected' ? $createdDate->copy()->addHours(rand(1, 48)) : null,
                'created_at' => $createdDate,
                'updated_at' => $createdDate,
            ]);

            // Add items to the request
            $this->addItemsToRequest($request, $categories, $status);

            // Update total quantity
            $request->update([
                'total_quantity' => $request->items()->sum('requested_quantity'),
                'approved_quantity' => $status === 'approved' ? $request->items()->sum('approved_quantity') : null,
            ]);
        }
    }

    private function addItemsToRequest($request, $categories, $requestStatus): void
    {
        // Randomly select 1-3 categories
        $selectedCategories = $categories->random(rand(1, 3));

        foreach ($selectedCategories as $category) {
            $categoryItems = $category->items;
            if ($categoryItems->isEmpty()) continue;

            // Select 1-3 items from this category
            $selectedItems = $categoryItems->random(min(rand(1, 3), $categoryItems->count()));

            foreach ($selectedItems as $categoryItem) {
                $requestedQty = rand(5, 20);

                // Determine item status based on request status
                $itemStatus = match($requestStatus) {
                    'approved' => 'approved',
                    'rejected' => 'rejected',
                    'partially_approved' => ['approved', 'rejected'][rand(0, 1)],
                    default => 'pending'
                };

                SeedlingRequestItem::create([
                    'seedling_request_id' => $request->id,
                    'category_id' => $category->id,
                    'category_item_id' => $categoryItem->id,
                    'item_name' => $categoryItem->name,
                    'requested_quantity' => $requestedQty,
                    'approved_quantity' => $itemStatus === 'approved' ? $requestedQty : null,
                    'status' => $itemStatus,
                    'rejection_reason' => $itemStatus === 'rejected' ? 'Insufficient stock' : null,
                ]);
            }
        }
    }
}
