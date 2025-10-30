<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user or create one
        $adminUser = User::where('role', 'admin')->first();
        if (!$adminUser) {
            $adminUser = User::first();
        }

        // Create sample events
        $events = [
            [
                'title' => 'Community Garden Initiative',
                'description' => 'Urban gardening program bringing together residents to cultivate fresh produce while learning sustainable farming techniques including organic gardening, composting, and water conservation.',
                'category' => 'ongoing',
                'date' => 'Every Saturday | 8:00 AM - 12:00 PM',
                'location' => 'San Pedro Community Garden, Brgy. Riverside',
                'details' => [
                    'participants' => 'All residents welcome, families encouraged',
                    'cost' => 'Free for all participants',
                    'icon' => '👥',
                    'requirement' => 'No prior experience needed'
                ],
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'title' => 'Green Corridor Project',
                'description' => 'City-wide landscaping initiative that transformed urban spaces into vibrant green zones. Over 500 native trees planted and pocket gardens created throughout the city to improve air quality and aesthetics.',
                'category' => 'past',
                'date' => '✅ Completed: September 2024',
                'location' => '12 Barangays across San Pedro City',
                'details' => [
                    'achievement' => '500+ native trees planted',
                    'impact' => 'Improved air quality and urban aesthetics',
                    'icon' => '🌳'
                ],
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'title' => 'Tree Planting Drive',
                'description' => 'Annual tree planting event with a goal to plant 1,000 indigenous trees across the city. Volunteers receive free seedlings, refreshments, and certificates of participation.',
                'category' => 'upcoming',
                'date' => '🌱 November 15, 2025 | 6:00 AM - 10:00 AM',
                'location' => 'Various locations citywide',
                'details' => [
                    'freebies' => 'Free seedlings, refreshments, certificate',
                    'registration' => 'Contact City Agriculture Office',
                    'icon' => '🎁'
                ],
                'is_active' => true,
                'display_order' => 3,
            ],
            [
                'title' => 'Vegetable Farming Workshop',
                'description' => 'Expert-led workshops covering advanced vegetable cultivation methods, pest management, and market strategies to help farmers maximize yields while minimizing environmental impact.',
                'category' => 'upcoming',
                'date' => '📚 October 28, 2025 | 2:00 PM - 5:00 PM',
                'location' => 'Agriculture Office Training Center',
                'details' => [
                    'for' => 'Local farmers and aspiring growers',
                    'freebies' => 'Free seeds, tools, and training materials',
                    'icon' => '👨‍🌾'
                ],
                'is_active' => true,
                'display_order' => 4,
            ],
            [
                'title' => 'Organic Rice Cultivation',
                'description' => 'Year-long program supporting farmers transitioning to organic rice farming methods that eliminate harmful pesticides. Includes training, organic fertilizers, and access to premium markets.',
                'category' => 'ongoing',
                'date' => '🌾 January - December 2025',
                'location' => 'All rice farming areas in San Pedro',
                'details' => [
                    'support' => 'Training, organic fertilizers, market access',
                    'certification' => 'Organic farming certification assistance',
                    'icon' => '🌾'
                ],
                'is_active' => true,
                'display_order' => 5,
            ],
            [
                'title' => 'Park Maintenance Program',
                'description' => 'Daily maintenance of city parks through regular mowing, trimming, and landscaping ensuring safe, clean, and beautiful spaces for families and communities.',
                'category' => 'ongoing',
                'date' => '🔄 Ongoing | Daily Operations',
                'location' => 'All public parks across San Pedro',
                'details' => [
                    'services' => 'Mowing, trimming, landscaping, sanitation',
                    'report' => 'Contact City Agriculture Office',
                    'icon' => '🛠️'
                ],
                'is_active' => true,
                'display_order' => 6,
            ],
            [
                'title' => 'Sports Field Renovation',
                'description' => 'Complete overhaul of community sports facilities including new turf installation, modern drainage systems, and efficient irrigation creating world-class venues for youth sports and tournaments.',
                'category' => 'ongoing',
                'date' => '⚙️ In Progress | Target: December 2025',
                'location' => 'San Pedro Sports Complex',
                'details' => [
                    'facilities' => 'Soccer fields, basketball courts, running tracks',
                    'upgrades' => 'New turf, drainage, irrigation, lighting',
                    'icon' => '⚽'
                ],
                'is_active' => true,
                'display_order' => 7,
            ],
            [
                'title' => 'Urban Farming Training',
                'description' => 'Monthly training on innovative techniques for growing vegetables in small spaces using containers, vertical gardens, and hydroponics. Perfect for apartment dwellers and homeowners.',
                'category' => 'announcement',
                'date' => '🏙️ First Sunday of Every Month | 9:00 AM - 12:00 PM',
                'location' => 'Agriculture Office Training Center',
                'details' => [
                    'techniques' => 'Container gardening, vertical gardens, hydroponics',
                    'materials' => 'All training materials and starter kits provided',
                    'icon' => '🌱'
                ],
                'is_active' => true,
                'display_order' => 8,
            ],
        ];

        foreach ($events as $eventData) {
            Event::create(array_merge($eventData, [
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ]));
        }

        // Create additional random events using factory
        Event::factory()->count(4)
            ->state(['created_by' => $adminUser->id, 'updated_by' => $adminUser->id])
            ->create();

        $this->command->info('Events seeded successfully!');
    }
}