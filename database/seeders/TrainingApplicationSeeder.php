<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TrainingApplication;
use App\Models\UserRegistration;
use Carbon\Carbon;

class TrainingApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have users in the user_registration table
        if (UserRegistration::count() < 10) {
            UserRegistration::factory(20)->create();
        }

        // Clear existing training applications (optional - comment out if you want to keep existing data)
        // TrainingApplication::query()->forceDelete();

        echo "🌱 Seeding Training Applications...\n";

        // Create training applications spread across the last 18 months for better analytics
        $months = [];
        for ($i = 17; $i >= 0; $i--) {
            $months[] = Carbon::now()->subMonths($i);
        }

        $trainingTypes = [
            'tilapia_hito' => 'Tilapia & Hito',
            'hydroponics' => 'Hydroponics',
            'aquaponics' => 'Aquaponics',
            'mushrooms' => 'Mushrooms',
            'livestock_poultry' => 'Livestock & Poultry',
            'high_value_crops' => 'High Value Crops',
            'sampaguita_propagation' => 'Sampaguita Propagation'
        ];

        $totalCreated = 0;

        // Create applications for each month with varying amounts
        foreach ($months as $index => $month) {
            // Varying numbers per month (5-15 applications)
            $applicationsThisMonth = rand(5, 15);

            foreach ($trainingTypes as $type => $name) {
                // Each training type gets 1-3 applications per month
                $count = rand(1, 3);

                for ($i = 0; $i < $count; $i++) {
                    // Random day within the month
                    $createdAt = (clone $month)->addDays(rand(0, 27))->addHours(rand(8, 17))->addMinutes(rand(0, 59));

                    // Status distribution: 50% approved, 25% rejected, 25% under_review
                    $statusRand = rand(1, 100);
                    if ($statusRand <= 50) {
                        $status = 'approved';
                        $statusUpdatedAt = (clone $createdAt)->addDays(rand(1, 7));
                    } elseif ($statusRand <= 75) {
                        $status = 'rejected';
                        $statusUpdatedAt = (clone $createdAt)->addDays(rand(1, 5));
                    } else {
                        $status = 'under_review';
                        $statusUpdatedAt = null;
                    }

                    // Create application with specific created_at
                    $application = TrainingApplication::factory()
                        ->trainingType($type)
                        ->state([
                            'created_at' => $createdAt,
                            'updated_at' => $statusUpdatedAt ?? $createdAt,
                            'status' => $status,
                            'status_updated_at' => $statusUpdatedAt,
                        ])
                        ->create();

                    $totalCreated++;
                }
            }
        }

        echo "✅ Created {$totalCreated} training applications across 18 months\n";

        // Create some additional recent applications with specific statuses
        echo "🎯 Creating recent test applications...\n";

        // Recent approved applications (last 7 days)
        TrainingApplication::factory(8)
            ->trainingType('tilapia_hito')
            ->approved()
            ->state(['created_at' => Carbon::now()->subDays(rand(1, 7))])
            ->create();

        // Recent pending applications
        TrainingApplication::factory(5)
            ->trainingType('hydroponics')
            ->underReview()
            ->state(['created_at' => Carbon::now()->subDays(rand(1, 3))])
            ->create();

        // Recent rejected applications
        TrainingApplication::factory(3)
            ->trainingType('aquaponics')
            ->rejected()
            ->state(['created_at' => Carbon::now()->subDays(rand(1, 5))])
            ->create();

        // Mix of different training types - recent
        TrainingApplication::factory(4)->trainingType('mushrooms')->approved()
            ->state(['created_at' => Carbon::now()->subDays(rand(1, 10))])
            ->create();

        TrainingApplication::factory(3)->trainingType('livestock_poultry')->underReview()
            ->state(['created_at' => Carbon::now()->subDays(rand(1, 7))])
            ->create();

        TrainingApplication::factory(3)->trainingType('high_value_crops')->approved()
            ->state(['created_at' => Carbon::now()->subDays(rand(5, 14))])
            ->create();

        TrainingApplication::factory(2)->trainingType('sampaguita_propagation')->underReview()
            ->state(['created_at' => Carbon::now()->subDays(rand(1, 4))])
            ->create();

        $recentCount = 8 + 5 + 3 + 4 + 3 + 3 + 2;
        $grandTotal = $totalCreated + $recentCount;

        echo "✅ Created {$recentCount} recent test applications\n";
        echo "🎉 Total Training Applications: {$grandTotal}\n";
        echo "📊 Data spread across 18 months for comprehensive analytics\n";

        // Display summary by training type
        echo "\n📋 Training Applications by Type:\n";
        foreach ($trainingTypes as $type => $name) {
            $count = TrainingApplication::where('training_type', $type)->count();
            echo "   • {$name}: {$count}\n";
        }

        // Display summary by status
        echo "\n📊 Applications by Status:\n";
        $approved = TrainingApplication::where('status', 'approved')->count();
        $rejected = TrainingApplication::where('status', 'rejected')->count();
        $underReview = TrainingApplication::where('status', 'under_review')->count();
        $pending = TrainingApplication::where('status', 'pending')->count();

        echo "   • ✅ Approved: {$approved}\n";
        echo "   • ❌ Rejected: {$rejected}\n";
        echo "   • ⏳ Under Review: {$underReview}\n";
        if ($pending > 0) {
            echo "   • ⏰ Pending: {$pending}\n";
        }
    }
}
