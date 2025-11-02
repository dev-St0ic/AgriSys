<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoryItem;
use App\Models\ItemSupplyLog;
use App\Models\User;
use Carbon\Carbon;

class SupplyManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating supply management data...');

        // Ensure we have admin users for supply operations
        $adminUsers = User::whereIn('role', ['admin', 'superadmin'])->get();
        if ($adminUsers->isEmpty()) {
            $this->command->error('No admin users found! Please run SuperAdminSeeder first.');
            return;
        }

        // Get all category items
        $categoryItems = CategoryItem::with('category')->get();
        if ($categoryItems->isEmpty()) {
            $this->command->error('No category items found! Please run RequestCategorySeeder first.');
            return;
        }

        // Create supply activities throughout the year
        $this->createInitialSupplies($categoryItems, $adminUsers);
        $this->createYearlySupplyActivities($categoryItems, $adminUsers);

        $this->command->info('Supply management seeding completed successfully!');
    }

    /**
     * Create initial supply entries (like starting inventory)
     */
    private function createInitialSupplies($categoryItems, $adminUsers): void
    {
        $this->command->info('Creating initial supply entries...');

        foreach ($categoryItems as $item) {
            // Create initial supply log entry for current stock
            if ($item->current_supply > 0) {
                ItemSupplyLog::create([
                    'category_item_id' => $item->id,
                    'transaction_type' => 'initial_supply',
                    'quantity' => $item->current_supply,
                    'old_supply' => 0,
                    'new_supply' => $item->current_supply,
                    'performed_by' => $adminUsers->random()->id,
                    'notes' => "Initial supply setup for {$item->name}",
                    'source' => 'System Setup',
                    'created_at' => Carbon::parse('2025-01-01'),
                    'updated_at' => Carbon::parse('2025-01-01')
                ]);
            }
        }
    }

    /**
     * Create supply activities throughout the year
     */
    private function createYearlySupplyActivities($categoryItems, $adminUsers): void
    {
        $this->command->info('Creating yearly supply activities...');

        // Generate monthly supply replenishments
        for ($month = 1; $month <= 11; $month++) {
            $this->createMonthlySupplyActivities($categoryItems, $adminUsers, 2025, $month);
        }

        // Some random adjustments and losses throughout the year
        $this->createRandomSupplyAdjustments($categoryItems, $adminUsers);
    }

    /**
     * Create supply activities for a specific month
     */
    private function createMonthlySupplyActivities($categoryItems, $adminUsers, int $year, int $month): void
    {
        $monthName = Carbon::create($year, $month, 1)->format('F Y');

        // Determine number of supply deliveries based on seasonal patterns
        $deliveryCount = match($month) {
            1, 2 => rand(2, 4),    // Post-holiday restocking
            3, 4 => rand(4, 6),    // Pre-peak season preparation
            5, 6 => rand(3, 5),    // Regular deliveries
            7, 8 => rand(2, 3),    // Rainy season (fewer deliveries)
            9, 10 => rand(4, 6),   // Post-rainy season restocking
            11 => rand(1, 2),      // Current month (only 2 days)
            default => rand(3, 5)
        };

        for ($delivery = 0; $delivery < $deliveryCount; $delivery++) {
            // Random date in month (limited for current month)
            $maxDay = ($month === 11) ? 2 : 28;
            $day = rand(1, $maxDay);
            $deliveryDate = Carbon::create($year, $month, $day);

            // Skip weekends for realistic delivery dates
            while ($deliveryDate->isWeekend()) {
                $deliveryDate->addDay();
                if ($deliveryDate->month !== $month) {
                    $deliveryDate = Carbon::create($year, $month, rand(1, $maxDay));
                }
            }

            // Select random items to restock (not all items every delivery)
            $itemsToRestock = $categoryItems->random(rand(3, min(8, $categoryItems->count())));

            foreach ($itemsToRestock as $item) {
                // Only restock if current supply is below maximum or needs replenishment
                if ($item->current_supply < ($item->maximum_supply * 0.8)) {
                    $this->createSupplyReplenishment($item, $adminUsers->random(), $deliveryDate);
                }
            }
        }

        $this->command->info("Created {$deliveryCount} supply deliveries for {$monthName}");
    }

    /**
     * Create a supply replenishment for an item
     */
    private function createSupplyReplenishment($item, $admin, $deliveryDate): void
    {
        // Calculate realistic replenishment quantity
        $targetStock = $item->maximum_supply ?? ($item->current_supply * 2);
        $availableSpace = $targetStock - $item->current_supply;

        if ($availableSpace <= 0) return;

        // Random quantity within reasonable bounds
        $quantity = rand(
            min(10, $availableSpace),
            min($availableSpace, $targetStock * 0.4)
        );

        if ($quantity <= 0) return;

        // Add supply to the item
        $oldSupply = $item->current_supply;
        $newSupply = $oldSupply + $quantity;

        // Ensure we don't exceed maximum
        if ($item->maximum_supply && $newSupply > $item->maximum_supply) {
            $newSupply = $item->maximum_supply;
            $quantity = $newSupply - $oldSupply;
        }

        if ($quantity <= 0) return;

        // Update the item
        $item->update([
            'current_supply' => $newSupply,
            'last_supplied_at' => $deliveryDate,
            'last_supplied_by' => $admin->id
        ]);

        // Create supply log
        ItemSupplyLog::create([
            'category_item_id' => $item->id,
            'transaction_type' => 'received',
            'quantity' => $quantity,
            'old_supply' => $oldSupply,
            'new_supply' => $newSupply,
            'performed_by' => $admin->id,
            'notes' => $this->getSupplyNotes($item, $quantity),
            'source' => $this->getRandomSupplier(),
            'created_at' => $deliveryDate,
            'updated_at' => $deliveryDate
        ]);
    }

    /**
     * Create random supply adjustments and losses
     */
    private function createRandomSupplyAdjustments($categoryItems, $adminUsers): void
    {
        $this->command->info('Creating random supply adjustments and losses...');

        // Create some random adjustments throughout the year
        for ($i = 0; $i < rand(15, 25); $i++) {
            $item = $categoryItems->random();
            $admin = $adminUsers->random();
            $adjustmentDate = Carbon::create(2025, rand(1, 11), rand(1, 28));

            // Random adjustment type
            $adjustmentType = collect(['loss', 'adjustment', 'returned'])->random();

            switch ($adjustmentType) {
                case 'loss':
                    $this->createSupplyLoss($item, $admin, $adjustmentDate);
                    break;
                case 'adjustment':
                    $this->createSupplyAdjustment($item, $admin, $adjustmentDate);
                    break;
                case 'returned':
                    $this->createSupplyReturn($item, $admin, $adjustmentDate);
                    break;
            }
        }
    }

    /**
     * Create a supply loss (expired, damaged, etc.)
     */
    private function createSupplyLoss($item, $admin, $date): void
    {
        if ($item->current_supply <= 0) return;

        $lossQuantity = rand(1, min(5, $item->current_supply));
        $oldSupply = $item->current_supply;
        $newSupply = $oldSupply - $lossQuantity;

        $item->update(['current_supply' => $newSupply]);

        ItemSupplyLog::create([
            'category_item_id' => $item->id,
            'transaction_type' => 'loss',
            'quantity' => $lossQuantity,
            'old_supply' => $oldSupply,
            'new_supply' => $newSupply,
            'performed_by' => $admin->id,
            'notes' => $this->getLossReason() . " - {$lossQuantity} {$item->unit}",
            'source' => null,
            'created_at' => $date,
            'updated_at' => $date
        ]);
    }

    /**
     * Create a supply adjustment (inventory count correction)
     */
    private function createSupplyAdjustment($item, $admin, $date): void
    {
        $oldSupply = $item->current_supply;
        $adjustment = rand(-3, 8); // More likely to be positive
        $newSupply = max(0, $oldSupply + $adjustment);

        if ($oldSupply === $newSupply) return;

        $item->update(['current_supply' => $newSupply]);

        ItemSupplyLog::create([
            'category_item_id' => $item->id,
            'transaction_type' => 'adjustment',
            'quantity' => abs($adjustment),
            'old_supply' => $oldSupply,
            'new_supply' => $newSupply,
            'performed_by' => $admin->id,
            'notes' => "Inventory count correction: " . ($adjustment > 0 ? "+{$adjustment}" : $adjustment) . " {$item->unit}",
            'source' => null,
            'created_at' => $date,
            'updated_at' => $date
        ]);
    }

    /**
     * Create a supply return (from cancelled/rejected requests)
     */
    private function createSupplyReturn($item, $admin, $date): void
    {
        $returnQuantity = rand(1, 10);
        $oldSupply = $item->current_supply;
        $newSupply = $oldSupply + $returnQuantity;

        // Respect maximum limits
        if ($item->maximum_supply && $newSupply > $item->maximum_supply) {
            $newSupply = $item->maximum_supply;
            $returnQuantity = $newSupply - $oldSupply;
        }

        if ($returnQuantity <= 0) return;

        $item->update(['current_supply' => $newSupply]);

        ItemSupplyLog::create([
            'category_item_id' => $item->id,
            'transaction_type' => 'returned',
            'quantity' => $returnQuantity,
            'old_supply' => $oldSupply,
            'new_supply' => $newSupply,
            'performed_by' => $admin->id,
            'notes' => "Returned from cancelled/rejected request - {$returnQuantity} {$item->unit}",
            'source' => null,
            'created_at' => $date,
            'updated_at' => $date
        ]);
    }

    /**
     * Get random supply notes
     */
    private function getSupplyNotes($item, $quantity): string
    {
        $notes = [
            "Received delivery of {$quantity} {$item->unit} of {$item->name}",
            "New stock arrival: {$quantity} {$item->unit}",
            "Supply replenishment: {$quantity} {$item->unit}",
            "Monthly delivery: {$quantity} {$item->unit} of {$item->name}",
            "Restocking {$item->name}: {$quantity} {$item->unit}",
        ];

        return collect($notes)->random();
    }

    /**
     * Get random supplier names
     */
    private function getRandomSupplier(): string
    {
        $suppliers = [
            'Metro Agriculture Supply',
            'Green Fields Agricultural Corp',
            'PhilAgriculture Trading',
            'Sunshine Farm Supply Co.',
            'Agricultural Development Center',
            'Farm to Market Supplies',
            'Tropical Agriculture Inc.',
            'Seedling Specialist Corp',
            'Agri-Growth Solutions',
            'Local Farmers Cooperative',
            'Regional Agriculture Center',
            'Organic Farm Supplies',
            'ProGrow Agricultural',
            'Natural Farming Solutions',
            'AgriTech Philippines'
        ];

        return collect($suppliers)->random();
    }

    /**
     * Get random loss reasons
     */
    private function getLossReason(): string
    {
        $reasons = [
            'Expired stock disposal',
            'Damaged during transport',
            'Quality control rejection',
            'Pest damage discovered',
            'Weather damage',
            'Storage contamination',
            'Natural spoilage',
            'Mechanical damage',
            'Failed quality inspection'
        ];

        return collect($reasons)->random();
    }
}
