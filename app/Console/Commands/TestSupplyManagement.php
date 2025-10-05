<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SeedlingRequest;
use App\Models\SeedlingRequestItem;
use App\Models\CategoryItem;
use App\Models\RequestCategory;

class TestSupplyManagement extends Command
{
    protected $signature = 'test:supply-management';
    protected $description = 'Test supply management integration with seedling requests';

    public function handle()
    {
        $this->info('🧪 Testing Supply Management Integration');
        $this->newLine();

        // Step 1: Show current supply status
        $this->info('📦 Current Supply Status:');
        $itemsWithSupply = CategoryItem::where('current_supply', '>', 0)
            ->select('id', 'name', 'current_supply', 'unit')
            ->get();

        foreach ($itemsWithSupply as $item) {
            $this->line("   ID {$item->id}: {$item->name} - {$item->current_supply} {$item->unit}");
        }
        $this->newLine();

        if ($itemsWithSupply->isEmpty()) {
            $this->error('No items have supply! Adding some test supply...');
            CategoryItem::where('id', 1)->update(['current_supply' => 50]);
            CategoryItem::where('id', 2)->update(['current_supply' => 30]);
            $this->info('Added 50 units to item 1 and 30 units to item 2');
            $itemsWithSupply = CategoryItem::whereIn('id', [1, 2])->get();
        }

        // Step 2: Create test request
        $this->info('📝 Creating Test Seedling Request...');
        $request = SeedlingRequest::create([
            'request_number' => 'TEST-' . time(),
            'first_name' => 'Test',
            'last_name' => 'User',
            'contact_number' => '09123456789',
            'email' => 'test@example.com',
            'address' => '123 Test Street',
            'barangay' => 'Test Barangay',
            'status' => 'pending'
        ]);

        $this->info("   ✅ Created request: {$request->request_number} (ID: {$request->id})");

        // Step 3: Add items to request
        $this->info('📋 Adding Items to Request...');
        $testItem = $itemsWithSupply->first();
        $requestQuantity = 5; // Request 5 units

        // Get the category for this item - refresh from database to ensure we have category_id
        $testItem = CategoryItem::find($testItem->id);
        $categoryId = $testItem->category_id;

        $requestItem = SeedlingRequestItem::create([
            'seedling_request_id' => $request->id,
            'category_id' => $categoryId,
            'category_item_id' => $testItem->id,
            'item_name' => $testItem->name,
            'requested_quantity' => $requestQuantity,
            'status' => 'pending'
        ]);

        $this->info("   ✅ Added item: {$testItem->name} (Requested: {$requestQuantity} {$testItem->unit})");
        $this->newLine();

        // Step 4: Show supply before approval
        $beforeSupply = $testItem->fresh()->current_supply;
        $this->info("📊 Supply Before Approval: {$beforeSupply} {$testItem->unit}");

        // Step 5: Approve the request (simulate admin approval)
        $this->info('✅ Simulating Admin Approval...');

        // Update the request item status to approved
        $requestItem->update([
            'status' => 'approved',
            'approved_quantity' => $requestQuantity
        ]);

        // The automatic supply distribution should happen
        $success = $testItem->distributeSupply(
            $requestQuantity,
            1, // Admin user ID
            "Distributed for approved request #{$request->request_number} - Test User",
            'SeedlingRequest',
            $request->id
        );

        if ($success) {
            $this->info("   ✅ Supply distribution successful!");
        } else {
            $this->error("   ❌ Supply distribution failed!");
        }

        // Step 6: Check supply after approval
        $afterSupply = $testItem->fresh()->current_supply;
        $this->info("📊 Supply After Approval: {$afterSupply} {$testItem->unit}");

        $difference = $beforeSupply - $afterSupply;
        $this->info("📉 Supply Reduced By: {$difference} {$testItem->unit}");
        $this->newLine();

        // Step 7: Check supply logs
        $this->info('📜 Checking Supply Logs...');
        $logs = \App\Models\ItemSupplyLog::where('category_item_id', $testItem->id)
            ->latest()
            ->take(3)
            ->get(['transaction_type', 'quantity', 'old_supply', 'new_supply', 'notes']);

        if ($logs->count() > 0) {
            foreach ($logs as $log) {
                $this->line("   📝 {$log->transaction_type}: {$log->quantity} units ({$log->old_supply} → {$log->new_supply})");
                $this->line("      Note: {$log->notes}");
            }
        } else {
            $this->warn('   No supply logs found');
        }

        $this->newLine();
        $this->info('🎉 Supply Management Test Complete!');

        return 0;
    }
}
