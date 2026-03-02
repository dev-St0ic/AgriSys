<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RequestCategory;
use App\Models\CategoryItem;
use Illuminate\Support\Facades\File;     
use Illuminate\Support\Facades\Storage;    

class SuppliesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding request categories and items...');

        $categories = [
            [
                'name' => 'seeds',
                'display_name' => 'Seeds',
                'icon' => 'fa-seedling',
                'description' => 'Various vegetable and crop seeds',
                'display_order' => 1,
                'items' => [
                    ['name' => 'Kamatis', 'filename' => 'kamatis.jpg'],
                    ['name' => 'Pechay', 'filename' => 'pechay.jpg'],
                    ['name' => 'Kangkong', 'filename' => 'kangkong.jpg'],
                    ['name' => 'Upo', 'filename' => 'upo.jpg'],
                    ['name' => 'Mustasa', 'filename' => 'mustasa.jpg'],
                ]
            ],
            [
                'name' => 'seedlings',
                'display_name' => 'Seedlings',
                'icon' => 'fa-leaf',
                'description' => 'Seedlings ready for transplanting',
                'display_order' => 2,
                'items' => [
                    // Original items
                    ['name' => 'Talong', 'filename' => 'talong.jpg'],
                    ['name' => 'Kamatis', 'filename' => 'kamatis.jpg'],
                    ['name' => 'Papaya', 'filename' => 'papaya.jpg'],
                    ['name' => 'Okra', 'filename' => 'okra.jpg'],
                    ['name' => 'Pipino', 'filename' => 'pipino.jpg'],
                    ['name' => 'Upo', 'filename' => 'upo.jpg'],
                    ['name' => 'Sili Panigang', 'filename' => 'sili_panigang.jpg'],
                    ['name' => 'Aloe Vera', 'filename' => 'aloe_vera.jpg'],
                    
                    // ALL items from seedling data
                    ['name' => 'Sili Labuyo', 'filename' => 'sili_labuyo.jpg'],
                    ['name' => 'Ampalaya', 'filename' => 'ampalaya.jpg'],
                    ['name' => 'Cacao', 'filename' => 'cacao.jpg'],
                    ['name' => 'Guyabano', 'filename' => 'guyabano.jpg'],
                    ['name' => 'Kalamansi', 'filename' => 'kalamansi.jpg'],
                    ['name' => 'Sampaguita', 'filename' => 'sampaguita.jpg'],
                    ['name' => 'Basil', 'filename' => 'basil.jpg']
                ]
            ]
        ];

        foreach ($categories as $categoryData) {
            // Create category first
            $category = RequestCategory::updateOrCreate(
                ['name' => $categoryData['name']],
                [
                    'display_name' => $categoryData['display_name'],
                    'icon' => $categoryData['icon'],
                    'description' => $categoryData['description'],
                    'display_order' => $categoryData['display_order'],
                    'is_active' => true
                ]
            );

            foreach ($categoryData['items'] as $index => $itemInfo) {
                $filename = $itemInfo['filename'];
                
                // Define paths 
                $sourcePath = public_path('images/supplies/' . $categoryData['name'] . '/' . $filename);
                $destinationPath = 'category-items/supplies/' . $categoryData['name'] . '/' . $filename;

                // Check if source file exists 
                if (!File::exists($sourcePath)) {
                    $this->command->warn("Source file not found: {$sourcePath}");
                    continue;
                }

                try {
                    // Copy the file to storage 
                    Storage::disk('public')->put($destinationPath, File::get($sourcePath));

                    // Define supply values based on item
                    $supplyValues = $this->getSupplyValues($categoryData['name'], $itemInfo['name']);

                    // Create the item
                    CategoryItem::updateOrCreate(
                        [
                            'category_id' => $category->id,
                            'name' => $itemInfo['name'],
                        ],
                        [
                            'unit' => 'pcs',
                            'display_order' => $index + 1,
                            'current_supply' => $supplyValues['current'],
                            'minimum_supply' => $supplyValues['minimum'],
                            'maximum_supply' => $supplyValues['maximum'],
                            'reorder_point' => $supplyValues['reorder'],
                            'supply_alert_enabled' => true,
                            'image_path' => $destinationPath,
                            'is_active' => true
                        ]
                    );

                    // Success message 
                    $this->command->info("Successfully imported: {$filename} for {$itemInfo['name']}");

                } catch (\Exception $e) {
                    // Error message 
                    $this->command->error("Failed to import {$filename}: " . $e->getMessage());
                }
            }

            $this->command->info("Completed category: {$category->display_name}");
        }

        $this->command->info('Supplies seeding completed!');
    }

    /**
     * Helper method to define supply values
     */
    private function getSupplyValues($category, $itemName)
    {
        // Seeds category defaults
        if ($category === 'seeds') {
            return [
                'current' => 100,
                'minimum' => 20,
                'maximum' => 200,
                'reorder' => 30
            ];
        }

        // Seedlings category with specific values
        $seedlingsValues = [
            // Original items with their current stock
            'Talong' => ['current' => 56, 'minimum' => 10, 'maximum' => 200, 'reorder' => 20],
            'Kamatis' => ['current' => 25, 'minimum' => 10, 'maximum' => 200, 'reorder' => 20],
            'Papaya' => ['current' => 10, 'minimum' => 5, 'maximum' => 100, 'reorder' => 10],
            'Okra' => ['current' => 60, 'minimum' => 15, 'maximum' => 200, 'reorder' => 25],
            'Pipino' => ['current' => 84, 'minimum' => 20, 'maximum' => 200, 'reorder' => 30],
            'Upo' => ['current' => 78, 'minimum' => 20, 'maximum' => 200, 'reorder' => 30],
            'Sili Panigang' => ['current' => 15, 'minimum' => 5, 'maximum' => 100, 'reorder' => 10],
            'Aloe Vera' => ['current' => 5, 'minimum' => 2, 'maximum' => 50, 'reorder' => 5],
            
            // ALL items from seedling data with 0 stock
            'Sili Labuyo' => ['current' => 0, 'minimum' => 8, 'maximum' => 120, 'reorder' => 12],
            'Ampalaya' => ['current' => 0, 'minimum' => 5, 'maximum' => 100, 'reorder' => 10],
            'Cacao' => ['current' => 0, 'minimum' => 2, 'maximum' => 50, 'reorder' => 5],
            'Guyabano' => ['current' => 0, 'minimum' => 2, 'maximum' => 40, 'reorder' => 4],
            'Kalamansi' => ['current' => 0, 'minimum' => 3, 'maximum' => 60, 'reorder' => 6],
            'Sampaguita' => ['current' => 0, 'minimum' => 3, 'maximum' => 50, 'reorder' => 5],
            'Basil' => ['current' => 0, 'minimum' => 5, 'maximum' => 80, 'reorder' => 8],
        ];

        return $seedlingsValues[$itemName] ?? [
            'current' => 0,
            'minimum' => 10,
            'maximum' => 150,
            'reorder' => 20
        ];
    }
}