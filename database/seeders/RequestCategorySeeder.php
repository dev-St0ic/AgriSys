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
                'display_order' => 1,
                'items' => [
                    [
                        'name' => 'Emerald Bitter Gourd Seeds',
                        'unit' => 'pack',
                        'display_order' => 1,
                        'current_supply' => 85,
                        'minimum_supply' => 20,
                        'maximum_supply' => 200,
                        'reorder_point' => 30,
                        'supply_alert_enabled' => true
                    ],
                    [
                        'name' => 'Golden Harvest Rice Seeds',
                        'unit' => 'kg',
                        'display_order' => 2,
                        'current_supply' => 150,
                        'minimum_supply' => 50,
                        'maximum_supply' => 500,
                        'reorder_point' => 75,
                        'supply_alert_enabled' => true
                    ],
                    [
                        'name' => 'Green Gem String Bean Seeds',
                        'unit' => 'pack',
                        'display_order' => 3,
                        'current_supply' => 45,
                        'minimum_supply' => 15,
                        'maximum_supply' => 150,
                        'reorder_point' => 25,
                        'supply_alert_enabled' => true
                    ],
                    [
                        'name' => 'Okra Seeds',
                        'unit' => 'pack',
                        'display_order' => 4,
                        'current_supply' => 65,
                        'minimum_supply' => 20,
                        'maximum_supply' => 180,
                        'reorder_point' => 30,
                        'supply_alert_enabled' => true
                    ],
                    [
                        'name' => 'Pioneer Hybrid Corn Seeds',
                        'unit' => 'kg',
                        'display_order' => 5,
                        'current_supply' => 120,
                        'minimum_supply' => 40,
                        'maximum_supply' => 300,
                        'reorder_point' => 60,
                        'supply_alert_enabled' => true
                    ],
                    [
                        'name' => 'Red Ruby Tomato Seeds',
                        'unit' => 'pack',
                        'display_order' => 6,
                        'current_supply' => 75,
                        'minimum_supply' => 25,
                        'maximum_supply' => 200,
                        'reorder_point' => 40,
                        'supply_alert_enabled' => true
                    ],
                    [
                        'name' => 'Sunshine Carrot Seeds',
                        'unit' => 'pack',
                        'display_order' => 7,
                        'current_supply' => 30,
                        'minimum_supply' => 15,
                        'maximum_supply' => 120,
                        'reorder_point' => 25,
                        'supply_alert_enabled' => true
                    ],
                    [
                        'name' => 'Yellow Pearl Squash Seeds',
                        'unit' => 'pack',
                        'display_order' => 8,
                        'current_supply' => 55,
                        'minimum_supply' => 20,
                        'maximum_supply' => 150,
                        'reorder_point' => 30,
                        'supply_alert_enabled' => true
                    ],
                ]
            ],
            [
                'name' => 'seedlings',
                'display_name' => 'Seedlings',
                'icon' => 'fa-leaf',
                'description' => 'Fruit tree seedlings',
                'display_order' => 2,
                'items' => [
                    [
                        'name' => 'Avocado Seedling',
                        'unit' => 'pcs',
                        'display_order' => 1,
                        'current_supply' => 42,
                        'minimum_supply' => 10,
                        'maximum_supply' => 100,
                        'reorder_point' => 20,
                        'supply_alert_enabled' => true
                    ],
                    [
                        'name' => 'Calamansi Seedling',
                        'unit' => 'pcs',
                        'display_order' => 2,
                        'current_supply' => 68,
                        'minimum_supply' => 15,
                        'maximum_supply' => 150,
                        'reorder_point' => 25,
                        'supply_alert_enabled' => true
                    ],
                    [
                        'name' => 'Guava Seedling',
                        'unit' => 'pcs',
                        'display_order' => 3,
                        'current_supply' => 35,
                        'minimum_supply' => 12,
                        'maximum_supply' => 80,
                        'reorder_point' => 20,
                        'supply_alert_enabled' => true
                    ],
                    [
                        'name' => 'Guyabano Seedling',
                        'unit' => 'pcs',
                        'display_order' => 4,
                        'current_supply' => 28,
                        'minimum_supply' => 8,
                        'maximum_supply' => 60,
                        'reorder_point' => 15,
                        'supply_alert_enabled' => true
                    ],
                    [
                        'name' => 'Mango Seedling',
                        'unit' => 'pcs',
                        'display_order' => 5,
                        'current_supply' => 45,
                        'minimum_supply' => 10,
                        'maximum_supply' => 100,
                        'reorder_point' => 20,
                        'supply_alert_enabled' => true
                    ],
                    [
                        'name' => 'Papaya Seedling',
                        'unit' => 'pcs',
                        'display_order' => 6,
                        'current_supply' => 52,
                        'minimum_supply' => 15,
                        'maximum_supply' => 120,
                        'reorder_point' => 25,
                        'supply_alert_enabled' => true
                    ],
                    [
                        'name' => 'Santol Seedling',
                        'unit' => 'pcs',
                        'display_order' => 7,
                        'current_supply' => 18,
                        'minimum_supply' => 5,
                        'maximum_supply' => 50,
                        'reorder_point' => 12,
                        'supply_alert_enabled' => true
                    ],
                ]
            ],
            [
                'name' => 'fruits',
                'display_name' => 'Fruit Trees',
                'icon' => 'fa-tree',
                'description' => 'Mature fruit trees',
                'display_order' => 3,
                'items' => [
                    [
                        'name' => 'Dwarf Coconut Tree',
                        'unit' => 'pcs',
                        'display_order' => 1,
                        'current_supply' => 15,
                        'minimum_supply' => 3,
                        'maximum_supply' => 40,
                        'reorder_point' => 8,
                        'supply_alert_enabled' => true
                    ],
                    [
                        'name' => 'Lakatan Banana Tree',
                        'unit' => 'pcs',
                        'display_order' => 2,
                        'current_supply' => 22,
                        'minimum_supply' => 5,
                        'maximum_supply' => 60,
                        'reorder_point' => 12,
                        'supply_alert_enabled' => true
                    ],
                    [
                        'name' => 'Rambutan Tree',
                        'unit' => 'pcs',
                        'display_order' => 3,
                        'current_supply' => 8,
                        'minimum_supply' => 2,
                        'maximum_supply' => 25,
                        'reorder_point' => 5,
                        'supply_alert_enabled' => true
                    ],
                    [
                        'name' => 'Star Apple Tree',
                        'unit' => 'pcs',
                        'display_order' => 4,
                        'current_supply' => 12,
                        'minimum_supply' => 3,
                        'maximum_supply' => 30,
                        'reorder_point' => 6,
                        'supply_alert_enabled' => true
                    ],
                ]
            ],
            [
                'name' => 'ornamentals',
                'display_name' => 'Ornamental Plants',
                'icon' => 'fa-spa',
                'description' => 'Decorative and ornamental plants',
                'display_order' => 4,
                'items' => [
                    [
                        'name' => 'Anthurium',
                        'unit' => 'pcs',
                        'display_order' => 1,
                        'current_supply' => 45,
                        'minimum_supply' => 10,
                        'maximum_supply' => 100,
                        'reorder_point' => 20,
                        'supply_alert_enabled' => true
                    ],
                    [
                        'name' => 'Bougainvillea',
                        'unit' => 'pcs',
                        'display_order' => 2,
                        'current_supply' => 38,
                        'minimum_supply' => 8,
                        'maximum_supply' => 80,
                        'reorder_point' => 15,
                        'supply_alert_enabled' => true
                    ],
                    [
                        'name' => 'Fortune Plant',
                        'unit' => 'pcs',
                        'display_order' => 3,
                        'current_supply' => 25,
                        'minimum_supply' => 5,
                        'maximum_supply' => 60,
                        'reorder_point' => 12,
                        'supply_alert_enabled' => true
                    ],
                    [
                        'name' => 'Gumamela (Hibiscus)',
                        'unit' => 'pcs',
                        'display_order' => 4,
                        'current_supply' => 55,
                        'minimum_supply' => 12,
                        'maximum_supply' => 120,
                        'reorder_point' => 25,
                        'supply_alert_enabled' => true
                    ],
                    [
                        'name' => 'Sansevieria (Snake Plant)',
                        'unit' => 'pcs',
                        'display_order' => 5,
                        'current_supply' => 32,
                        'minimum_supply' => 8,
                        'maximum_supply' => 70,
                        'reorder_point' => 15,
                        'supply_alert_enabled' => true
                    ],
                ]
            ],
            [
                'name' => 'fingerlings',
                'display_name' => 'Fingerlings',
                'icon' => 'fa-fish',
                'description' => 'Fish fingerlings for aquaculture',
                'display_order' => 5,
                'items' => [
                    [
                        'name' => 'Catfish Fingerling',
                        'unit' => 'pcs',
                        'min_quantity' => 50,
                        'display_order' => 1,
                        'current_supply' => 850,
                        'minimum_supply' => 200,
                        'maximum_supply' => 2000,
                        'reorder_point' => 400,
                        'supply_alert_enabled' => true
                    ],
                    [
                        'name' => 'Milkfish (Bangus) Fingerling',
                        'unit' => 'pcs',
                        'min_quantity' => 100,
                        'display_order' => 2,
                        'current_supply' => 1200,
                        'minimum_supply' => 300,
                        'maximum_supply' => 3000,
                        'reorder_point' => 600,
                        'supply_alert_enabled' => true
                    ],
                    [
                        'name' => 'Tilapia Fingerlings',
                        'unit' => 'pcs',
                        'min_quantity' => 100,
                        'display_order' => 3,
                        'current_supply' => 950,
                        'minimum_supply' => 250,
                        'maximum_supply' => 2500,
                        'reorder_point' => 500,
                        'supply_alert_enabled' => true
                    ],
                ]
            ],
            [
                'name' => 'fertilizers',
                'display_name' => 'Fertilizers',
                'icon' => 'fa-flask',
                'description' => 'Organic and inorganic fertilizers',
                'display_order' => 6,
                'items' => [
                    [
                        'name' => 'Ammonium Sulfate (21-0-0)',
                        'unit' => 'kg',
                        'display_order' => 1,
                        'current_supply' => 180,
                        'minimum_supply' => 50,
                        'maximum_supply' => 500,
                        'reorder_point' => 100,
                        'supply_alert_enabled' => true
                    ],
                    [
                        'name' => 'Humic Acid',
                        'unit' => 'liter',
                        'display_order' => 2,
                        'current_supply' => 65,
                        'minimum_supply' => 20,
                        'maximum_supply' => 150,
                        'reorder_point' => 35,
                        'supply_alert_enabled' => true
                    ],
                    [
                        'name' => 'Pre-processed Chicken Manure',
                        'unit' => 'kg',
                        'display_order' => 3,
                        'current_supply' => 220,
                        'minimum_supply' => 80,
                        'maximum_supply' => 600,
                        'reorder_point' => 150,
                        'supply_alert_enabled' => true
                    ],
                    [
                        'name' => 'Urea (46-0-0)',
                        'unit' => 'kg',
                        'display_order' => 4,
                        'current_supply' => 95,
                        'minimum_supply' => 30,
                        'maximum_supply' => 300,
                        'reorder_point' => 60,
                        'supply_alert_enabled' => true
                    ],
                    [
                        'name' => 'Vermicast Fertilizer',
                        'unit' => 'kg',
                        'display_order' => 5,
                        'current_supply' => 140,
                        'minimum_supply' => 40,
                        'maximum_supply' => 400,
                        'reorder_point' => 80,
                        'supply_alert_enabled' => true
                    ],
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
