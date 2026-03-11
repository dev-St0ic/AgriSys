<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class EventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * ACTIVE: 4 events (High Value Crops, Vegetable Seedlings, Coastal Cleanup, AgriSys)
     * INACTIVE: 4 events (Mushroom, Fish Farming, 2 dummies)
     * 
     * Total: 8 events
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

        $this->command->info('Starting event seeding...');
        
        // Copy event images first
        $this->command->info('Copying event images...');
        $this->copyEventImages();
        $this->command->info('Images copied successfully!');

        $events = [
            // ===== EVENT 1: INACTIVE (Past) =====
            [
                'title' => 'Mushroom Production Training',
                'short_description' => 'Learn mushroom cultivation techniques, substrate preparation, and proper post-harvest handling.',
                'description' => 'Participants learned practical methods for small-scale mushroom production, sanitation practices, and simple marketing strategies for mushroom products.',
                'category' => 'past',
                'category_label' => 'Past',
                'date' => 'February 16, 2026 | 9:00 AM - 3:00 PM',
                'location' => 'City Agriculture Office Training Hall',
                'image_path' => 'events/placeholder-1.jpg',
                'image_alt_text' => 'Mushroom Production Training',
                'details' => [
                    'participants' => 'Farmers and community members',
                    'cost' => 'Free',
                    'requirement' => 'None',
                    'contact' => '(02) 8808-2020, Local 109',
                ],
                'is_active' => false,
                'is_featured' => false,
                'display_order' => 1,
            ],

            // ===== EVENT 2: ACTIVE (Upcoming) =====
            [
                'title' => 'High Value Crops Training',
                'short_description' => 'Training focused on profitable crop production including vegetables, herbs, and specialty crops with high market demand.',
                'description' => 'Farmers were introduced to modern planting techniques, crop management, and marketing strategies for high-value crops.',
                'category' => 'upcoming',
                'category_label' => 'Upcoming',
                'date' => 'April 20, 2026 | 9:00 AM - 3:00 PM',
                'location' => 'City Agriculture Office Training Hall',
                'image_path' => 'events/placeholder-2.jpg',
                'image_alt_text' => 'High Value Crops Training',
                'details' => [
                    'participants' => 'Farmers and agricultural workers',
                    'cost' => 'Free',
                    'requirement' => 'None',
                    'contact' => '(02) 8808-2020, Local 109',
                ],
                'is_active' => true,
                'is_featured' => false,
                'display_order' => 2,
            ],

            // ===== EVENT 3: INACTIVE (Past) =====
            [
                'title' => 'Fish Farming Training',
                'short_description' => 'Learn fish farming techniques, pond management, feeding practices, and disease prevention for tilapia and catfish production.',
                'description' => 'Participants received hands-on guidance on maintaining fish ponds, proper feeding schedules, and sustainable aquaculture practices.',
                'category' => 'past',
                'category_label' => 'Past',
                'date' => 'February 3, 2026 | 8:30 AM - 2:30 PM',
                'location' => 'Barangay San Vicente Aquaculture Area',
                'image_path' => 'events/placeholder-3.jpg',
                'image_alt_text' => 'Fish Farming Training',
                'details' => [
                    'participants' => 'Fisherfolk and farmers',
                    'cost' => 'Free',
                    'requirement' => 'None',
                    'contact' => '(02) 8808-2020, Local 109',
                ],
                'is_active' => false,
                'is_featured' => false,
                'display_order' => 3,
            ],

            // ===== EVENT 4: ACTIVE (Announcement) =====
            [
                'title' => 'Coastal and Water Bodies Cleanup Drive',
                'short_description' => 'A community activity focused on cleaning rivers, coastlines, and other water bodies to help protect marine ecosystems.',
                'description' => 'Community volunteers and environmental groups regularly participate in cleanup drives to reduce pollution and promote environmental awareness.',
                'category' => 'announcement',
                'category_label' => 'Announcement',
                'date' => null,
                'location' => 'Local Coastal and River Areas',
                'image_path' => 'events/placeholder-4.jpg',
                'image_alt_text' => 'Coastal and Water Bodies Cleanup Drive',
                'details' => [
                    'participants' => 'Community volunteers and environmental groups',
                    'cost' => 'Free',
                    'requirement' => 'None',
                    'contact' => '(02) 8808-2020, Local 109',
                ],
                'is_active' => true,
                'is_featured' => false,
                'display_order' => 4,
            ],

            // ===== EVENT 5: ACTIVE (Ongoing) =====
            [
                'title' => 'Vegetable Seedlings Distribution Program',
                'short_description' => 'Distribution of vegetable seedlings to farmers and residents to support backyard gardening and local food production.',
                'description' => 'Seedlings such as eggplant, okra, and chili were distributed to encourage sustainable food production and support local farmers.',
                'category' => 'ongoing',
                'category_label' => 'Ongoing',
                'date' => 'March 1, 2026 to March 31, 2026 | 9:00 AM',
                'location' => 'City Agriculture Office',
                'image_path' => 'events/placeholder-5.jpg',
                'image_alt_text' => 'Vegetable Seedlings Distribution Program',
                'details' => [
                    'participants' => 'Farmers and residents',
                    'cost' => 'Free',
                    'requirement' => 'None',
                    'contact' => '(02) 8808-2020, Local 109',
                ],
                'is_active' => true,
                'is_featured' => false,
                'display_order' => 5,
            ],

            // ===== EVENT 6: ACTIVE (Past) =====
            [
                'title' => 'AgriSys Digital Services Seminar',
                'short_description' => 'A seminar where PUP San Pedro Campus researchers from the AgriSys team proposed the Agricultural Service System to streamline digital services for the City Agriculture Office of San Pedro, Laguna — serving all agricultural stakeholders.',
                'description' => 'Researchers from the Polytechnic University of the Philippines (PUP) San Pedro Campus, under the AgriSys team, formally proposed the Agricultural Service System — a digital platform designed to optimize and streamline service delivery for the City Agriculture Office of San Pedro, Laguna. The seminar presented the system\'s core features including online service requests, faster processing of support, and centralized access to city agriculture programs for all agricultural stakeholders. The proposal highlighted how AgriSys aims to reduce manual processes, eliminate unnecessary paperwork, and ensure that agricultural stakeholders receive timely assistance and government support more efficiently.',
                'category' => 'past',
                'category_label' => 'Past',
                'date' => 'March 3, 2026 | 8:00 PM - 11:00 AM',
                'location' => 'Municipal Conference Hall',
                'image_path' => 'events/placeholder-6.jpg',
                'image_alt_text' => 'AgriSys Digital Streamlining Seminar',
                'details' => [
                    'participants' => 'PUP San Pedro Campus - AgriSys Team, City Agriculture Office officials, and agricultural stakeholders',
                    'cost' => 'Free',
                    'requirement' => 'None',
                    'contact' => '(02) 8808-2020, Local 109',
                ],
                'is_active' => true,
                'is_featured' => false,
                'display_order' => 6,
            ],

            // ===== EVENT 7: INACTIVE (Dummy) =====
            [
                'title' => 'Upcoming Agricultural Event',
                'short_description' => 'Details to be announced soon.',
                'description' => 'Stay tuned for more information about this upcoming agricultural event.',
                'category' => 'announcement',
                'category_label' => 'Announcement',
                'date' => null,
                'location' => 'City Agriculture Office',
                'image_path' => 'events/placeholder-7.jpg',
                'image_alt_text' => 'Upcoming Agricultural Event',
                'details' => [
                    'participants' => 'TBA',
                    'cost' => 'TBA',
                    'requirement' => 'TBA',
                    'contact' => '(02) 8808-2020, Local 109',
                ],
                'is_active' => false,
                'is_featured' => false,
                'display_order' => 7,
            ],

            // ===== EVENT 8: INACTIVE (Dummy) =====
            [
                'title' => 'Community Farming Program',
                'short_description' => 'Details to be announced soon.',
                'description' => 'Stay tuned for more information about this upcoming community farming program.',
                'category' => 'announcement',
                'category_label' => 'Announcement',
                'date' => null,
                'location' => 'City Agriculture Office',
                'image_path' => 'events/placeholder-8.jpg',
                'image_alt_text' => 'Community Farming Program',
                'details' => [
                    'participants' => 'TBA',
                    'cost' => 'TBA',
                    'requirement' => 'TBA',
                    'contact' => '(02) 8808-2020, Local 109',
                ],
                'is_active' => false,
                'is_featured' => false,
                'display_order' => 8,
            ],
        ];

        // ========================================
        // CREATE ALL EVENTS
        // ========================================
        foreach ($events as $eventData) {
            Event::create(array_merge($eventData, [
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
                'published_at' => now(),
            ]));
            
            $status = $eventData['is_active'] ? 'ACTIVE' : 'INACTIVE';
            $this->command->line("$status - {$eventData['title']}");
        }

        $this->command->info('EVENT SEEDING COMPLETED!');
        $this->command->info('Summary: 4 Active | 4 Inactive | 8 Total');
    }

    /**
     * Copy event images from public/images/events/ to storage/app/public/events/
     */
    private function copyEventImages(): void
    {
        $sourceDir = public_path('images/events');
        $destinationDir = 'events';

        if (!File::isDirectory($sourceDir)) {
            $this->command->warn("Source directory not found: {$sourceDir}");
            $this->command->line("Please create the folder: public/images/events/");
            $this->command->line("And add your images: placeholder-1.jpg through placeholder-8.jpg");
            return;
        }

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

                Storage::disk('public')->put($destinationPath, File::get($sourcePath));

                $this->command->line("Copied: {$filename}");

            } catch (\Exception $e) {
                $this->command->error("Failed to copy {$filename}: " . $e->getMessage());
            }
        }
    }
}