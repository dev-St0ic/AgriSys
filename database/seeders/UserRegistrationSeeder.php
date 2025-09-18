<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserRegistration;
use App\Models\User;
use Faker\Factory as Faker;

class UserRegistrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('en_PH'); // Use Philippines locale for more relevant data

        // Create sample registrations with realistic data for Filipino users
        $this->createFarmers($faker);
        $this->createFisherfolk($faker);
        $this->createGeneralPublic($faker);
        
        $this->command->info('User registrations seeded successfully!');
    }

    /**
     * Create farmer registrations
     */
    private function createFarmers($faker)
    {
        $farmerOccupations = [
            'Rice Farmer', 'Vegetable Farmer', 'Fruit Farmer', 'Livestock Farmer',
            'Organic Farmer', 'Crop Farmer', 'Dairy Farmer', 'Poultry Farmer'
        ];

        $farmerOrganizations = [
            'San Pedro Farmers Association',
            'Laguna Rice Farmers Cooperative',
            'Organic Farmers Guild',
            'Biñan Agricultural Cooperative',
            'CALABARZON Farmers Union',
            'Independent Farmer',
            null // Some don't have organizations
        ];

        for ($i = 0; $i < 15; $i++) {
            $firstName = $faker->firstName();
            $lastName = $faker->lastName();
            $email = strtolower($firstName . '.' . $lastName . $faker->numberBetween(1, 99) . '@' . $faker->randomElement(['gmail.com', 'yahoo.com', 'hotmail.com']));
            
            UserRegistration::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password' => 'password123', // Will be hashed by model
                'date_of_birth' => $faker->dateTimeBetween('-65 years', '-18 years'),
                'gender' => $faker->randomElement(['male', 'female', 'prefer_not_to_say']),
                'phone' => '+63' . $faker->numberBetween(900000000, 999999999),
                'address' => $faker->address() . ', ' . $faker->randomElement([
                    'San Pedro, Laguna',
                    'Biñan, Laguna', 
                    'Calamba, Laguna',
                    'Cabuyao, Laguna',
                    'Santa Rosa, Laguna'
                ]),
                'user_type' => 'farmer',
                'occupation' => $faker->randomElement($farmerOccupations),
                'organization' => $faker->randomElement($farmerOrganizations),
                'emergency_contact_name' => $faker->name(),
                'emergency_contact_phone' => '+63' . $faker->numberBetween(900000000, 999999999),
                'status' => $faker->randomElement(['pending', 'approved', 'rejected']),
                'email_verified_at' => $faker->optional(0.8)->dateTimeBetween('-30 days', 'now'),
                'verification_token' => $faker->optional(0.2)->sha256(),
                'terms_accepted' => true,
                'privacy_accepted' => true,
                'marketing_consent' => $faker->boolean(60),
                'registration_ip' => $faker->ipv4(),
                'user_agent' => $this->getRandomUserAgent($faker),
                'referral_source' => $faker->randomElement(['direct', 'facebook', 'google', 'barangay_office', 'friend_referral']),
                'created_at' => $faker->dateTimeBetween('-3 months', 'now'),
            ]);
        }
    }

    /**
     * Create fisherfolk registrations
     */
    private function createFisherfolk($faker)
    {
        $fisherOccupations = [
            'Commercial Fisher', 'Artisanal Fisher', 'Aquaculture Farmer', 
            'Fish Vendor', 'Boat Operator', 'Net Fisherman', 'Fish Cage Operator'
        ];

        $fisherOrganizations = [
            'Laguna de Bay Fishermen Association',
            'San Pedro Bay Fisherfolk Cooperative',
            'CALABARZON Fisheries Alliance',
            'Small-scale Fishermen Federation',
            'Independent Fisher',
            null
        ];

        for ($i = 0; $i < 12; $i++) {
            $firstName = $faker->firstName();
            $lastName = $faker->lastName();
            $email = strtolower($firstName . '.' . $lastName . $faker->numberBetween(1, 99) . '@' . $faker->randomElement(['gmail.com', 'yahoo.com', 'hotmail.com']));
            
            UserRegistration::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password' => 'password123',
                'date_of_birth' => $faker->dateTimeBetween('-60 years', '-18 years'),
                'gender' => $faker->randomElement(['male', 'female']),
                'phone' => '+63' . $faker->numberBetween(900000000, 999999999),
                'address' => $faker->address() . ', ' . $faker->randomElement([
                    'San Pedro, Laguna',
                    'Biñan, Laguna',
                    'Bay, Laguna',
                    'Los Baños, Laguna'
                ]),
                'user_type' => 'fisherfolk',
                'occupation' => $faker->randomElement($fisherOccupations),
                'organization' => $faker->randomElement($fisherOrganizations),
                'emergency_contact_name' => $faker->name(),
                'emergency_contact_phone' => '+63' . $faker->numberBetween(900000000, 999999999),
                'status' => $faker->randomElement(['pending', 'approved', 'rejected']),
                'email_verified_at' => $faker->optional(0.75)->dateTimeBetween('-30 days', 'now'),
                'verification_token' => $faker->optional(0.25)->sha256(),
                'terms_accepted' => true,
                'privacy_accepted' => true,
                'marketing_consent' => $faker->boolean(50),
                'registration_ip' => $faker->ipv4(),
                'user_agent' => $this->getRandomUserAgent($faker),
                'referral_source' => $faker->randomElement(['direct', 'facebook', 'barangay_office', 'cooperative']),
                'created_at' => $faker->dateTimeBetween('-3 months', 'now'),
            ]);
        }
    }

    /**
     * Create general public registrations
     */
    private function createGeneralPublic($faker)
    {
        $generalOccupations = [
            'Teacher', 'Student', 'Business Owner', 'Government Employee',
            'Healthcare Worker', 'Engineer', 'Accountant', 'Entrepreneur',
            'Retiree', 'Homemaker', 'Construction Worker', 'Driver'
        ];

        $generalOrganizations = [
            'San Pedro LGU', 'Local School', 'Community Organization',
            'Religious Group', 'Business Chamber', 'NGO', 'Barangay Council',
            null, null, null // Many won't have organizations
        ];

        for ($i = 0; $i < 20; $i++) {
            $firstName = $faker->firstName();
            $lastName = $faker->lastName();
            $email = strtolower($firstName . '.' . $lastName . $faker->numberBetween(1, 99) . '@' . $faker->randomElement(['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com']));
            
            UserRegistration::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password' => 'password123',
                'date_of_birth' => $faker->dateTimeBetween('-70 years', '-16 years'),
                'gender' => $faker->randomElement(['male', 'female', 'other', 'prefer_not_to_say']),
                'phone' => $faker->optional(0.9)->numerify('+639#########'),
                'address' => $faker->optional(0.8)->address() . ', ' . $faker->randomElement([
                    'San Pedro, Laguna',
                    'Biñan, Laguna',
                    'Calamba, Laguna',
                    'Santa Rosa, Laguna',
                    'Cabuyao, Laguna',
                    'Manila',
                    'Quezon City',
                    'Makati'
                ]),
                'user_type' => 'general',
                'occupation' => $faker->optional(0.85)->randomElement($generalOccupations),
                'organization' => $faker->optional(0.4)->randomElement($generalOrganizations),
                'emergency_contact_name' => $faker->optional(0.7)->name(),
                'emergency_contact_phone' => $faker->optional(0.7)->numerify('+639#########'),
                'status' => $faker->randomElement(['pending', 'approved', 'rejected']),
                'email_verified_at' => $faker->optional(0.85)->dateTimeBetween('-30 days', 'now'),
                'verification_token' => $faker->optional(0.15)->sha256(),
                'terms_accepted' => true,
                'privacy_accepted' => true,
                'marketing_consent' => $faker->boolean(70),
                'registration_ip' => $faker->ipv4(),
                'user_agent' => $this->getRandomUserAgent($faker),
                'referral_source' => $faker->randomElement([
                    'direct', 'facebook', 'google', 'twitter', 'instagram', 
                    'youtube', 'friend_referral', 'government_website'
                ]),
                'created_at' => $faker->dateTimeBetween('-4 months', 'now'),
            ]);
        }
    }

    /**
     * Get random user agent string
     */
    private function getRandomUserAgent($faker)
    {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Edge/120.0.0.0',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:121.0) Gecko/20100101 Firefox/121.0',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (iPad; CPU OS 17_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Linux; Android 14; SM-G998B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36'
        ];

        return $faker->randomElement($userAgents);
    }

    /**
     * Create specific test cases
     */
    private function createTestCases()
    {
        // Create a pending registration that needs approval
        UserRegistration::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test.user@example.com',
            'password' => 'password123',
            'date_of_birth' => '1990-01-01',
            'gender' => 'male',
            'phone' => '+639123456789',
            'address' => '123 Test Street, San Pedro, Laguna',
            'user_type' => 'farmer',
            'occupation' => 'Rice Farmer',
            'organization' => 'Test Farmers Association',
            'emergency_contact_name' => 'Emergency Contact',
            'emergency_contact_phone' => '+639987654321',
            'status' => 'pending',
            'email_verified_at' => now(),
            'terms_accepted' => true,
            'privacy_accepted' => true,
            'marketing_consent' => true,
            'registration_ip' => '127.0.0.1',
            'user_agent' => 'Test User Agent',
            'referral_source' => 'direct',
            'created_at' => now()->subDays(1),
        ]);
    }
}