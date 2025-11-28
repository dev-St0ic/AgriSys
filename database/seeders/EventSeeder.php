<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * ACTIVE: 4 events (1 announcement + 3 non-announcement)
     * INACTIVE: 8 events
     * 
     * Total: 12 events
     * 
     * Images are copied from: public/images/events/
     * To: storage/app/public/events/
     */
    public function run(): void
    {
        $adminUser = User::where('role', 'admin')->first();
        if (!$adminUser) {
            $adminUser = User::first();
            if (!$adminUser) {
                $this->command->error('❌ No users found. Please run UserSeeder first.');
                return;
            }
        }

        $this->command->info('📝 Starting event seeding...');
        
        // Copy event images first
        $this->command->info('🎨 Copying event images...');
        $this->copyEventImages();
        $this->command->info('✅ Images copied successfully!');

        $events = [
            // ===== EVENT 1: ACTIVE (Grid Card 1 - Ongoing) =====
            [
                'title' => 'Community Garden Initiative',
                'short_description' => 'Weekly urban gardening program for residents and families',
                'description' => 'Urban gardening program bringing together residents to cultivate fresh produce while learning sustainable farming techniques. Participants receive free seedlings, tools, and expert guidance.',
                'category' => 'ongoing',
                'category_label' => 'Ongoing',
                'date' => 'Every Saturday | 8:00 AM - 12:00 PM',
                'location' => 'San Pedro Community Garden, Brgy. Riverside',
                'image_path' => 'events/placeholder-1.jpg',
                'is_active' => true,
                'display_order' => 1,
            ],

            // ===== EVENT 2: ACTIVE (Grid Card 2 - Past) =====
            [
                'title' => 'Green Corridor Project',
                'short_description' => 'City-wide landscaping initiative completed successfully',
                'description' => 'City-wide landscaping initiative that transformed urban spaces into vibrant green zones. Over 500 native trees planted across the city.',
                'category' => 'past',
                'category_label' => 'Past',
                'date' => '✅ Completed: September 2024',
                'location' => '12 Barangays across San Pedro City',
                'image_path' => 'events/placeholder-2.jpg',
                'is_active' => true,
                'display_order' => 2,
            ],

            // ===== EVENT 3: ACTIVE (Grid Card 3 - Upcoming) =====
            [
                'title' => 'Tree Planting Drive 2025',
                'short_description' => 'Annual tree planting event - goal of 1,000 indigenous trees',
                'description' => 'Annual tree planting event with an ambitious goal to plant 1,000 indigenous trees across the city. Volunteers receive free seedlings and certificates.',
                'category' => 'upcoming',
                'category_label' => 'Upcoming',
                'date' => 'November 15, 2025 | 6:00 AM - 10:00 AM',
                'location' => 'Various locations citywide',
                'image_path' => 'events/placeholder-3.jpg',
                'is_active' => true,
                'display_order' => 3,
            ],

            // ===== EVENT 4: ACTIVE (Featured Announcement) =====
            [
                'title' => 'Urban Farming Training Series',
                'short_description' => 'Monthly workshops on innovative small-space farming techniques',
                'description' => 'Monthly training series on innovative techniques for growing vegetables in small spaces using containers, vertical gardens, hydroponics, and other space-efficient methods.',
                'category' => 'announcement',
                'category_label' => 'Announcement',
                'date' => null,
                'location' => 'Agriculture Office Training Center',
                'image_path' => 'events/placeholder-8.jpg',
                'is_active' => true,
                'display_order' => 4,
            ],

            // ===== EVENT 5: INACTIVE =====
            [
                'title' => 'Vegetable Farming Workshop',
                'short_description' => 'Expert-led workshop on advanced vegetable cultivation methods',
                'description' => 'Expert-led workshops covering advanced vegetable cultivation methods and pest management.',
                'category' => 'upcoming',
                'category_label' => 'Upcoming',
                'date' => 'October 28, 2025 | 2:00 PM - 5:00 PM',
                'location' => 'Agriculture Office Training Center',
                'image_path' => 'events/placeholder-4.jpg',
                'is_active' => false,
                'display_order' => 5,
            ],

            // ===== EVENT 6: INACTIVE =====
            [
                'title' => 'Organic Rice Cultivation Program',
                'short_description' => 'Year-long support program for organic farming transition',
                'description' => 'Year-long program supporting farmers transitioning to organic rice farming methods.',
                'category' => 'ongoing',
                'category_label' => 'Ongoing',
                'date' => 'January - December 2025 (Year-round)',
                'location' => 'All rice farming areas in San Pedro',
                'image_path' => 'events/placeholder-5.jpg',
                'is_active' => false,
                'display_order' => 6,
            ],

            // ===== EVENT 7: INACTIVE =====
            [
                'title' => 'Park Maintenance Program',
                'short_description' => 'Daily maintenance ensuring safe and beautiful city parks',
                'description' => 'Daily maintenance of city parks through regular mowing, trimming, and sanitation.',
                'category' => 'ongoing',
                'category_label' => 'Ongoing',
                'date' => 'Daily Operations | 6:00 AM - 3:00 PM',
                'location' => 'All public parks across San Pedro',
                'image_path' => 'events/placeholder-6.jpg',
                'is_active' => false,
                'display_order' => 7,
            ],

            // ===== EVENT 8: INACTIVE =====
            [
                'title' => 'Sports Field Renovation Project',
                'short_description' => 'Modern sports facility upgrade with professional-grade turf',
                'description' => 'Complete overhaul of community sports facilities with new professional-grade turf.',
                'category' => 'ongoing',
                'category_label' => 'Ongoing',
                'date' => 'In Progress | Target Completion: December 2025',
                'location' => 'San Pedro Sports Complex, City Hall Grounds',
                'image_path' => 'events/placeholder-7.jpg',
                'is_active' => false,
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
            
            $status = $eventData['is_active'] ? '✅ ACTIVE' : '⏸️ INACTIVE';
            $this->command->line("$status - {$eventData['title']}");
        }

        // ========================================
        // CREATE 4 RANDOM INACTIVE EVENTS
        // ========================================
        Event::factory()
            ->count(4)
            ->inactive()
            ->state([
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
                'published_at' => now(),
            ])
            ->create();

        $this->command->info('✅ EVENT SEEDING COMPLETED!');
    }

    /**
     * Copy event images from public/images/events/ to storage/app/public/events/
     * Similar to SlideshowImagesSeeder pattern
     */
    private function copyEventImages(): void
    {
        $sourceDir = public_path('images/events');
        $destinationDir = 'events';

        // Check if source directory exists
        if (!File::isDirectory($sourceDir)) {
            $this->command->warn("Source directory not found: {$sourceDir}");
            $this->command->line("Please create the folder: public/images/events/");
            $this->command->line("And add your images: placeholder-1.jpg through placeholder-8.jpg");
            return;
        }

        // Get all image files
        $files = File::files($sourceDir);

        if (empty($files)) {
            $this->command->warn("No images found in: {$sourceDir}");
            return;
        }

        foreach ($files as $file) {
            try {
                $filename = $file->getFilename();
                $sourcePath = $file->getRealPath();
                $destinationPath = "{$destinationDir}/{$filename}";

                // Copy file to storage
                Storage::disk('public')->put($destinationPath, File::get($sourcePath));

                $this->command->line("✅ Copied: {$filename}");

            } catch (\Exception $e) {
                $this->command->error("Failed to copy {$filename}: " . $e->getMessage());
            }
        }
    }
}