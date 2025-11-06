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
     * 
     * Seeds the database with sample events for the Agricultural Events Management System
     * Creates 8 predefined events + 4 randomly generated events
     * 
     * Total: 12 sample events perfectly suited for 3 cards + 1 featured layout
     * 
     * Display Order:
     * - Order 1-3: Show as 3 cards + featured (event 1 is featured)
     * - Order 4-8: Additional events in grid (if you extend the layout)
     * - Order 9-12: Random events for variety
     */
    public function run(): void
    {
        // Get admin user or use first user as fallback
        $adminUser = User::where('role', 'admin')->first();
        if (!$adminUser) {
            $adminUser = User::first();
            if (!$adminUser) {
                $this->command->error('❌ No users found. Please run UserSeeder first.');
                return;
            }
        }

        $this->command->info('📝 Starting event seeding...');

        // ========================================
        // PREDEFINED SAMPLE EVENTS (8 Events)
        // ========================================
        // Carefully crafted agricultural events
        // showcasing different categories and use cases
        
        $events = [
            // ===== EVENT 1: ONGOING (Featured & Card 1) =====
            [
                'title' => 'Community Garden Initiative',
                'short_description' => 'Weekly urban gardening program for residents and families',
                'description' => 'Urban gardening program bringing together residents to cultivate fresh produce while learning sustainable farming techniques. Participants receive free seedlings, tools, and expert guidance throughout the growing season. This initiative promotes food security, environmental awareness, and community building among neighbors.',
                'category' => 'ongoing',
                'category_label' => 'Ongoing',
                'date' => 'Every Saturday | 8:00 AM - 12:00 PM',
                'location' => 'San Pedro Community Garden, Brgy. Riverside',
                'image_path' => 'events/placeholder-1.jpg',
                'image_alt_text' => 'Community members gardening together in raised beds',
                'details' => [
                    'participants' => 'All residents welcome, families encouraged',
                    'cost' => 'Free for all participants',
                    'requirement' => 'No prior experience needed',
                    'contact' => '(049) 123-4567',
                    'benefits' => 'Free seedlings, tools, and training materials'
                ],
                'is_active' => true,
                'is_featured' => true,
                'display_order' => 1,
            ],

            // ===== EVENT 2: PAST (Card 2) =====
            [
                'title' => 'Green Corridor Project',
                'short_description' => 'City-wide landscaping initiative completed successfully',
                'description' => 'City-wide landscaping initiative that transformed urban spaces into vibrant green zones. Over 500 native trees planted and pocket gardens created throughout the city to improve air quality, reduce urban heat, and enhance the aesthetic appeal of our community. This major project contributed to environmental sustainability goals.',
                'category' => 'past',
                'category_label' => 'Past',
                'date' => '✅ Completed: September 2024',
                'location' => '12 Barangays across San Pedro City',
                'image_path' => 'events/placeholder-2.jpg',
                'image_alt_text' => 'Newly planted green corridor with trees lining the street',
                'details' => [
                    'achievement' => '500+ native trees planted across city',
                    'impact' => 'Improved air quality and urban aesthetics',
                    'scope' => '12 barangays participated',
                    'budget' => 'Funded by City Agriculture Office'
                ],
                'is_active' => true,
                'is_featured' => false,
                'display_order' => 2,
            ],

            // ===== EVENT 3: UPCOMING (Card 3) =====
            [
                'title' => 'Tree Planting Drive 2025',
                'short_description' => 'Annual tree planting event - goal of 1,000 indigenous trees',
                'description' => 'Annual tree planting event with an ambitious goal to plant 1,000 indigenous trees across the city. Volunteers receive free seedlings, refreshments, and certificates of participation. This event promotes environmental conservation and community engagement in fighting climate change.',
                'category' => 'upcoming',
                'category_label' => 'Upcoming',
                'date' => '🌱 November 15, 2025 | 6:00 AM - 10:00 AM',
                'location' => 'Various locations citywide',
                'image_path' => 'events/placeholder-3.jpg',
                'image_alt_text' => 'Volunteers holding saplings ready for planting',
                'details' => [
                    'target' => '1,000 indigenous trees',
                    'freebies' => 'Free seedlings, refreshments, certificate of participation',
                    'registration' => 'Contact City Agriculture Office or register online',
                    'tools_provided' => 'Yes, all tools and materials provided'
                ],
                'is_active' => true,
                'is_featured' => false,
                'display_order' => 3,
            ],

            // ===== EVENT 4: UPCOMING =====
            [
                'title' => 'Vegetable Farming Workshop',
                'short_description' => 'Expert-led workshop on advanced vegetable cultivation methods',
                'description' => 'Expert-led workshops covering advanced vegetable cultivation methods, integrated pest management, soil health, and market strategies to help farmers maximize yields while minimizing environmental impact and production costs.',
                'category' => 'upcoming',
                'category_label' => 'Upcoming',
                'date' => '📚 October 28, 2025 | 2:00 PM - 5:00 PM',
                'location' => 'Agriculture Office Training Center, City Hall Grounds',
                'image_path' => 'events/placeholder-4.jpg',
                'image_alt_text' => 'Farmers learning about vegetable cultivation in classroom',
                'details' => [
                    'target_audience' => 'Local farmers and aspiring growers',
                    'freebies' => 'Free seeds, tools, and comprehensive training materials',
                    'speakers' => 'Agriculture experts from DA',
                    'certificates' => 'Certificates provided upon completion'
                ],
                'is_active' => true,
                'is_featured' => false,
                'display_order' => 4,
            ],

            // ===== EVENT 5: ONGOING =====
            [
                'title' => 'Organic Rice Cultivation Program',
                'short_description' => 'Year-long support program for organic farming transition',
                'description' => 'Year-long program supporting farmers transitioning to organic rice farming methods that eliminate harmful pesticides and chemical inputs. Includes comprehensive training, provision of organic fertilizers, and exclusive access to premium markets with better price points.',
                'category' => 'ongoing',
                'category_label' => 'Ongoing',
                'date' => '🌾 January - December 2025 (Year-round)',
                'location' => 'All rice farming areas in San Pedro',
                'image_path' => 'events/placeholder-5.jpg',
                'image_alt_text' => 'Rice farmers examining organic fertilizer in the field',
                'details' => [
                    'support_provided' => 'Training, organic fertilizers, market linkage',
                    'certification' => 'Assistance with organic farming certification',
                    'duration' => '12 months',
                    'participants' => 'Rice farmers transitioning to organic methods'
                ],
                'is_active' => true,
                'is_featured' => false,
                'display_order' => 5,
            ],

            // ===== EVENT 6: ONGOING =====
            [
                'title' => 'Park Maintenance Program',
                'short_description' => 'Daily maintenance ensuring safe and beautiful city parks',
                'description' => 'Daily maintenance of city parks through regular mowing, trimming, landscaping, and sanitation ensuring safe, clean, and beautiful recreational spaces for families and communities throughout San Pedro City.',
                'category' => 'ongoing',
                'category_label' => 'Ongoing',
                'date' => '🔄 Daily Operations | 6:00 AM - 3:00 PM',
                'location' => 'All public parks across San Pedro',
                'image_path' => 'events/placeholder-6.jpg',
                'image_alt_text' => 'Park maintenance team mowing grass and trimming hedges',
                'details' => [
                    'services' => 'Mowing, trimming, landscaping, sanitation',
                    'parks_covered' => 'All public parks',
                    'report_issues' => 'Contact City Agriculture Office at (049) 123-4567',
                    'frequency' => 'Daily maintenance schedule'
                ],
                'is_active' => true,
                'is_featured' => false,
                'display_order' => 6,
            ],

            // ===== EVENT 7: ONGOING =====
            [
                'title' => 'Sports Field Renovation Project',
                'short_description' => 'Modern sports facility upgrade with professional-grade turf',
                'description' => 'Complete overhaul of community sports facilities including new professional-grade turf installation, modern drainage systems, and efficient irrigation creating world-class venues for youth sports, tournaments, and community events.',
                'category' => 'ongoing',
                'category_label' => 'Ongoing',
                'date' => '⚙️ In Progress | Target Completion: December 2025',
                'location' => 'San Pedro Sports Complex, City Hall Grounds',
                'image_path' => 'events/placeholder-7.jpg',
                'image_alt_text' => 'Sports field under renovation with new turf installation',
                'details' => [
                    'facilities' => 'Soccer fields, basketball courts, running tracks',
                    'upgrades' => 'New professional turf, drainage system, irrigation, LED lighting',
                    'status' => '75% complete',
                    'completion_date' => 'December 2025'
                ],
                'is_active' => true,
                'is_featured' => false,
                'display_order' => 7,
            ],

            // ===== EVENT 8: ANNOUNCEMENT =====
            [
                'title' => 'Urban Farming Training Series',
                'short_description' => 'Monthly workshops on innovative small-space farming techniques',
                'description' => 'Monthly training series on innovative techniques for growing vegetables in small spaces using containers, vertical gardens, hydroponics, and other space-efficient methods. Perfect for apartment dwellers, homeowners, and urban gardening enthusiasts who want to grow their own food.',
                'category' => 'announcement',
                'category_label' => 'Announcement',
                'date' => '🏙️ First Sunday of Every Month | 9:00 AM - 12:00 PM',
                'location' => 'Agriculture Office Training Center',
                'image_path' => 'events/placeholder-8.jpg',
                'image_alt_text' => 'Urban farmer displaying vertical garden setup',
                'details' => [
                    'techniques' => 'Container gardening, vertical gardens, hydroponics, aquaponics',
                    'materials_provided' => 'All training materials and starter kits provided',
                    'target' => 'Urban residents, apartment dwellers, homeowners',
                    'next_session' => 'First Sunday of upcoming month'
                ],
                'is_active' => true,
                'is_featured' => false,
                'display_order' => 8,
            ],
        ];

        // ========================================
        // CREATE PREDEFINED EVENTS
        // ========================================
        foreach ($events as $eventData) {
            Event::create(array_merge($eventData, [
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
                'published_at' => now(),
            ]));
            
            $this->command->line("✅ Created: {$eventData['title']}");
        }

        $this->command->info('📚 Creating 4 random additional events...');

        // ========================================
        // CREATE RANDOM EVENTS (using factory)
        // ========================================
        Event::factory()
            ->count(4)
            ->state([
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
                'published_at' => now(),
            ])
            ->create();

        $this->command->newLine();
        $this->command->info('✅ ✅ ✅ EVENT SEEDING COMPLETED! ✅ ✅ ✅');
        $this->command->line('');
        $this->command->info('📊 Statistics:');
        $this->command->line('   • 8 Predefined events created');
        $this->command->line('   • 4 Random events created');
        $this->command->line('   • Total: 12 events');
        $this->command->line('');
        $this->command->info('🎯 Frontend Display:');
        $this->command->line('   • Cards 1-3: Events with display_order 1, 2, 3');
        $this->command->line('   • Featured: Event with display_order 1 (Community Garden)');
        $this->command->line('   • Additional: Events 4-12 available if you extend the layout');
        $this->command->line('');
        $this->command->info('🔗 Access at: http://localhost/api/events');
        $this->command->line('');
    }
}