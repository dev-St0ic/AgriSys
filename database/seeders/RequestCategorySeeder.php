<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RequestCategory;
use App\Models\CategoryItem;

class RequestCategorySeeder extends Seeder
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
                'sort_order' => 1,
                'items' => [
                    ['name' => 'Emerald Bitter Gourd Seeds', 'unit' => 'pack', 'sort_order' => 1],
                    ['name' => 'Golden Harvest Rice Seeds', 'unit' => 'kg', 'sort_order' => 2],
                    ['name' => 'Green Gem String Bean Seeds', 'unit' => 'pack', 'sort_order' => 3],
                    ['name' => 'Okra Seeds', 'unit' => 'pack', 'sort_order' => 4],
                    ['name' => 'Pioneer Hybrid Corn Seeds', 'unit' => 'kg', 'sort_order' => 5],
                    ['name' => 'Red Ruby Tomato Seeds', 'unit' => 'pack', 'sort_order' => 6],
                    ['name' => 'Sunshine Carrot Seeds', 'unit' => 'pack', 'sort_order' => 7],
                    ['name' => 'Yellow Pearl Squash Seeds', 'unit' => 'pack', 'sort_order' => 8],
                ]
            ],
            [
                'name' => 'seedlings',
                'display_name' => 'Seedlings',
                'icon' => 'fa-leaf',
                'description' => 'Fruit tree seedlings',
                'sort_order' => 2,
                'items' => [
                    ['name' => 'Avocado Seedling', 'unit' => 'pcs', 'sort_order' => 1],
                    ['name' => 'Calamansi Seedling', 'unit' => 'pcs', 'sort_order' => 2],
                    ['name' => 'Guava Seedling', 'unit' => 'pcs', 'sort_order' => 3],
                    ['name' => 'Guyabano Seedling', 'unit' => 'pcs', 'sort_order' => 4],
                    ['name' => 'Mango Seedling', 'unit' => 'pcs', 'sort_order' => 5],
                    ['name' => 'Papaya Seedling', 'unit' => 'pcs', 'sort_order' => 6],
                    ['name' => 'Santol Seedling', 'unit' => 'pcs', 'sort_order' => 7],
                ]
            ],
            [
                'name' => 'fruits',
                'display_name' => 'Fruit Trees',
                'icon' => 'fa-tree',
                'description' => 'Mature fruit trees',
                'sort_order' => 3,
                'items' => [
                    ['name' => 'Dwarf Coconut Tree', 'unit' => 'pcs', 'sort_order' => 1],
                    ['name' => 'Lakatan Banana Tree', 'unit' => 'pcs', 'sort_order' => 2],
                    ['name' => 'Rambutan Tree', 'unit' => 'pcs', 'sort_order' => 3],
                    ['name' => 'Star Apple Tree', 'unit' => 'pcs', 'sort_order' => 4],
                ]
            ],
            [
                'name' => 'ornamentals',
                'display_name' => 'Ornamental Plants',
                'icon' => 'fa-spa',
                'description' => 'Decorative and ornamental plants',
                'sort_order' => 4,
                'items' => [
                    ['name' => 'Anthurium', 'unit' => 'pcs', 'sort_order' => 1],
                    ['name' => 'Bougainvillea', 'unit' => 'pcs', 'sort_order' => 2],
                    ['name' => 'Fortune Plant', 'unit' => 'pcs', 'sort_order' => 3],
                    ['name' => 'Gumamela (Hibiscus)', 'unit' => 'pcs', 'sort_order' => 4],
                    ['name' => 'Sansevieria (Snake Plant)', 'unit' => 'pcs', 'sort_order' => 5],
                ]
            ],
            [
                'name' => 'fingerlings',
                'display_name' => 'Fingerlings',
                'icon' => 'fa-fish',
                'description' => 'Fish fingerlings for aquaculture',
                'sort_order' => 5,
                'items' => [
                    ['name' => 'Catfish Fingerling', 'unit' => 'pcs', 'min_quantity' => 50, 'sort_order' => 1],
                    ['name' => 'Milkfish (Bangus) Fingerling', 'unit' => 'pcs', 'min_quantity' => 100, 'sort_order' => 2],
                    ['name' => 'Tilapia Fingerlings', 'unit' => 'pcs', 'min_quantity' => 100, 'sort_order' => 3],
                ]
            ],
            [
                'name' => 'fertilizers',
                'display_name' => 'Fertilizers',
                'icon' => 'fa-flask',
                'description' => 'Organic and inorganic fertilizers',
                'sort_order' => 6,
                'items' => [
                    ['name' => 'Ammonium Sulfate (21-0-0)', 'unit' => 'kg', 'sort_order' => 1],
                    ['name' => 'Humic Acid', 'unit' => 'liter', 'sort_order' => 2],
                    ['name' => 'Pre-processed Chicken Manure', 'unit' => 'kg', 'sort_order' => 3],
                    ['name' => 'Urea (46-0-0)', 'unit' => 'kg', 'sort_order' => 4],
                    ['name' => 'Vermicast Fertilizer', 'unit' => 'kg', 'sort_order' => 5],
                ]
            ],
        ];

        foreach ($categories as $categoryData) {
            $items = $categoryData['items'];
            unset($categoryData['items']);

            $category = RequestCategory::create($categoryData);

            foreach ($items as $itemData) {
                $itemData['category_id'] = $category->id;
                CategoryItem::create($itemData);
            }

            $this->command->info("Created category: {$category->display_name} with " . count($items) . " items");
        }

        $this->command->info('Request categories seeding completed!');
    }
}