<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SlideshowImage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SlideshowImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the existing hero images with their details
        $heroImages = [
            [
                'filename' => 'bg1.jpg',
                'title' => 'Agricultural Excellence',
                'description' => 'Supporting sustainable farming practices and agricultural development in San Pedro, Laguna',
                'order' => 1
            ],
            [
                'filename' => 'bg2.jpg',
                'title' => 'Community Support',
                'description' => 'Empowering local farmers and fishermen through comprehensive agricultural services',
                'order' => 2
            ],
            [
                'filename' => 'bg3.jpg',
                'title' => 'Modern Agriculture',
                'description' => 'Bridging traditional farming with modern agricultural technologies and methods',
                'order' => 3
            ],
            [
                'filename' => 'bg4.jpg',
                'title' => 'Sustainable Growth',
                'description' => 'Promoting environmentally responsible agricultural practices for future generations',
                'order' => 4
            ],
            [
                'filename' => 'bg5.jpg',
                'title' => 'Resource Management',
                'description' => 'Efficient distribution of agricultural resources and seedlings to local farmers',
                'order' => 5
            ],
            [
                'filename' => 'bg6.jpg',
                'title' => 'Training & Education',
                'description' => 'Providing comprehensive training programs to enhance farming skills and knowledge',
                'order' => 6
            ],
            [
                'filename' => 'bg7.png',
                'title' => 'Digital Innovation',
                'description' => 'Leveraging technology to streamline agricultural services and registration processes',
                'order' => 7
            ]
        ];

        $this->command->info('Starting to import existing hero images...');

        foreach ($heroImages as $imageData) {
            $sourcePath = public_path('images/hero/' . $imageData['filename']);

            // Check if the source file exists
            if (!File::exists($sourcePath)) {
                $this->command->warn("Source file not found: {$imageData['filename']}");
                continue;
            }

            // Check if this image is already in the database
            $existingSlide = SlideshowImage::where('image_path', 'LIKE', '%' . $imageData['filename'])->first();
            if ($existingSlide) {
                $this->command->info("Slide already exists: {$imageData['filename']}");
                continue;
            }

            try {
                // Create the destination path in storage/app/public/slideshow
                $destinationPath = 'slideshow/' . $imageData['filename'];

                // Copy the file to storage
                Storage::disk('public')->put($destinationPath, File::get($sourcePath));

                // Create the database record
                SlideshowImage::create([
                    'image_path' => $destinationPath,
                    'title' => $imageData['title'],
                    'description' => $imageData['description'],
                    'order' => $imageData['order'],
                    'is_active' => true
                ]);

                $this->command->info("Successfully imported: {$imageData['filename']}");

            } catch (\Exception $e) {
                $this->command->error("Failed to import {$imageData['filename']}: " . $e->getMessage());
            }
        }

        $this->command->info('Hero images import completed!');
        $this->command->info('You can now manage these images through the admin panel at: /admin/slideshow');
    }
}
