<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserRegistration;
use Illuminate\Support\Facades\Hash;

class UserRegistrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding user registrations...');

        $this->createUnverifiedUsers();
        $this->createPendingUsers();
        $this->createApprovedUsers();
        $this->createRejectedUsers();
        $this->createTestUsers();

        $this->command->info('User registrations seeded successfully!');
        $this->command->info('Total created: ' . UserRegistration::count() . ' registrations');
        $this->command->info('- Unverified: ' . UserRegistration::unverified()->count());
        $this->command->info('- Pending: ' . UserRegistration::pending()->count());
        $this->command->info('- Approved: ' . UserRegistration::approved()->count());
        $this->command->info('- Rejected: ' . UserRegistration::rejected()->count());
    }

    /**
     * Create unverified users (basic signup only)
     */
    private function createUnverifiedUsers()
    {
        $this->command->info('Creating unverified users...');

        UserRegistration::factory()
            ->count(20)
            ->unverified()
            ->incompleteProfile()
            ->recent()
            ->create();
    }

    /**
     * Create pending users (profile completed, awaiting approval)
     */
    private function createPendingUsers()
    {
        $this->command->info('Creating pending users...');

        UserRegistration::factory()
            ->count(6)
            ->farmer()
            ->pending()
            ->completeProfile()
            ->create();

        UserRegistration::factory()
            ->count(5)
            ->fisherfolk()
            ->pending()
            ->completeProfile()
            ->create();

        UserRegistration::factory()
            ->count(4)
            ->pending()
            ->completeProfile()
            ->create();
    }

    /**
     * Create approved users
     */
    private function createApprovedUsers()
    {
        $this->command->info('Creating approved users...');

        UserRegistration::factory()
            ->count(10)
            ->farmer()
            ->approved()
            ->completeProfile()
            ->old()
            ->create();

        UserRegistration::factory()
            ->count(8)
            ->fisherfolk()
            ->approved()
            ->completeProfile()
            ->old()
            ->create();

        UserRegistration::factory()
            ->count(7)
            ->approved()
            ->completeProfile()
            ->old()
            ->create();
    }

    /**
     * Create rejected users
     */
    private function createRejectedUsers()
    {
        $this->command->info('Creating rejected users...');

        UserRegistration::factory()
            ->count(2)
            ->farmer()
            ->rejected()
            ->completeProfile()
            ->create();

        UserRegistration::factory()
            ->count(2)
            ->fisherfolk()
            ->rejected()
            ->completeProfile()
            ->create();

        UserRegistration::factory()
            ->count(1)
            ->rejected()
            ->completeProfile()
            ->create();
    }

    /**
     * Create specific test users for development and testing
     */
    private function createTestUsers()
    {
        $this->command->info('Creating test users...');

        // Test User 1: Basic signup (unverified)
        UserRegistration::create([
            'username' => 'juan_test',
            'password' => Hash::make('password123'),
            'sex' => 'Male',
            'status' => UserRegistration::STATUS_UNVERIFIED,
            'terms_accepted' => true,
            'privacy_accepted' => true,
            'contact_number' => '09123456780',
            'username_changed_at' => null,
            'created_at' => now()->subHours(2),
        ]);

        // Test User 2: Complete profile, pending approval
        UserRegistration::create([
            'username' => 'maria_santos',
            'password' => Hash::make('password123'),
            'sex' => 'Female',
            'status' => UserRegistration::STATUS_PENDING,
            'terms_accepted' => true,
            'privacy_accepted' => true,
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'middle_name' => 'Reyes',
            'contact_number' => '09123456789',
            'complete_address' => '123 Seaside Street, Barangay Baybayin',
            'barangay' => 'Barangay Baybayin',
            'user_type' => 'fisherfolk',
            'date_of_birth' => '1985-03-15',
            'age' => 39,
            'gender' => 'female',
            'username_changed_at' => null,
            'created_at' => now()->subDays(3),
        ]);

        // Test User 3: Approved user
        UserRegistration::create([
            'username' => 'carlos_rodriguez',
            'password' => Hash::make('password123'),
            'sex' => 'Male',
            'status' => UserRegistration::STATUS_APPROVED,
            'terms_accepted' => true,
            'privacy_accepted' => true,
            'first_name' => 'Carlos',
            'last_name' => 'Rodriguez',
            'contact_number' => '09555123456',
            'complete_address' => '456 Main Street, Barangay Centro',
            'barangay' => 'Barangay Centro',
            'user_type' => 'farmer',
            'date_of_birth' => '1990-07-20',
            'age' => 34,
            'gender' => 'male',
            'approved_at' => now()->subDays(5),
            'username_changed_at' => null,
            'created_at' => now()->subDays(20),
        ]);

        // Test User 4: Rejected user
        UserRegistration::create([
            'username' => 'ana_garcia',
            'password' => Hash::make('password123'),
            'sex' => 'Female',
            'status' => UserRegistration::STATUS_REJECTED,
            'terms_accepted' => true,
            'privacy_accepted' => true,
            'first_name' => 'Ana',
            'last_name' => 'Garcia',
            'contact_number' => '09777888999',
            'complete_address' => '789 Farm Road, Barangay Rural',
            'barangay' => 'Barangay Rural',
            'user_type' => 'farmer',
            'date_of_birth' => '1982-11-08',
            'age' => 42,
            'gender' => 'female',
            'rejected_at' => now()->subDays(8),
            'rejection_reason' => 'Unable to verify identity documents. Please resubmit with clearer photos.',
            'username_changed_at' => null,
            'created_at' => now()->subDays(12),
        ]);

        // Test User 5: Recently signed up
        UserRegistration::create([
            'username' => 'elena_villanueva',
            'password' => Hash::make('password123'),
            'sex' => 'Female',
            'status' => UserRegistration::STATUS_UNVERIFIED,
            // 'verification_token' => 'test-verification-token-12345',
            'terms_accepted' => true,
            'privacy_accepted' => true,
            'contact_number' => '09666555444',
            'username_changed_at' => null,
            'created_at' => now()->subMinutes(30),
        ]);

        // Test User 6: User who already changed username
        UserRegistration::create([
            'username' => 'pedro_santos_new',
            'password' => Hash::make('password123'),
            'sex' => 'Male',
            'status' => UserRegistration::STATUS_APPROVED,
            'terms_accepted' => true,
            'privacy_accepted' => true,
            'first_name' => 'Pedro',
            'last_name' => 'Santos',
            'contact_number' => '09888777666',
            'complete_address' => '321 Farm Avenue, Barangay Agricola',
            'barangay' => 'Barangay Agricola',
            'user_type' => 'farmer',
            'date_of_birth' => '1988-05-12',
            'age' => 36,
            'gender' => 'male',
            'approved_at' => now()->subDays(20),
            'username_changed_at' => now()->subDays(10),
            'created_at' => now()->subDays(60),
        ]);
    }
}
