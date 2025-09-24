<?php

namespace Database\Factories;

use App\Models\UserRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserRegistration>
 */
class UserRegistrationFactory extends Factory
{
    protected $model = UserRegistration::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        
        return [
            // Basic required fields for signup
            'username' => $this->faker->unique()->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password123'), // Default password for testing
            'status' => $this->faker->randomElement(['unverified', 'pending', 'approved', 'rejected']),
            'terms_accepted' => true,
            'privacy_accepted' => true,
            
            // Extended fields (filled during verification - nullable)
            'first_name' => $firstName,
            'last_name' => $lastName,
            'middle_name' => $this->faker->optional(0.3)->firstName(),
            'name_extension' => $this->faker->optional(0.1)->randomElement(['Jr.', 'Sr.', 'III', 'IV']),
            'contact_number' => $this->faker->optional(0.8)->numerify('+639#########'), // UPDATED: contact_number instead of phone
            'complete_address' => $this->faker->optional(0.7)->address(),
            'barangay' => $this->faker->optional(0.7)->randomElement([
                'Barangay San Antonio', 'Barangay Santo Niño', 'Barangay Nueva',
                'Barangay Poblacion', 'Barangay Riverside', 'Barangay Central'
            ]),
            'user_type' => $this->faker->optional(0.8)->randomElement(['farmer', 'fisherfolk']),
            'date_of_birth' => $this->faker->optional(0.8)->dateTimeBetween('-70 years', '-18 years'),
            'gender' => $this->faker->optional(0.9)->randomElement(['male', 'female', 'other', 'prefer_not_to_say']),
            
            // System fields
            'verification_token' => $this->faker->optional(0.3)->sha256(),
            'email_verified_at' => $this->faker->optional(0.7)->dateTimeBetween('-30 days', 'now'),
            'registration_ip' => $this->faker->ipv4(),
            'user_agent' => $this->getRandomUserAgent(),
            'referral_source' => $this->faker->randomElement(['direct', 'facebook', 'google', 'friend_referral', 'barangay_office']),
            'last_login_at' => $this->faker->optional(0.5)->dateTimeBetween('-7 days', 'now'),
        ];
    }

    /**
     * Indicate that the registration is unverified (basic signup only).
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserRegistration::STATUS_UNVERIFIED,
            'first_name' => null,
            'last_name' => null,
            'middle_name' => null,
            'contact_number' => null, // UPDATED: contact_number instead of phone
            'complete_address' => null,
            'barangay' => null,
            'user_type' => null,
            'date_of_birth' => null,
            'gender' => null,
            'age' => null,
            'approved_at' => null,
            'approved_by' => null,
            'rejection_reason' => null,
        ]);
    }

    /**
     * Indicate that the registration is pending (profile completed).
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserRegistration::STATUS_PENDING,
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'contact_number' => '+63' . $this->faker->numberBetween(900000000, 999999999), // UPDATED: contact_number instead of phone
            'complete_address' => $this->faker->address(),
            'barangay' => $this->faker->randomElement([
                'Barangay San Antonio', 'Barangay Santo Niño', 'Barangay Nueva',
                'Barangay Poblacion', 'Barangay Riverside', 'Barangay Central'
            ]),
            'user_type' => $this->faker->randomElement(['farmer', 'fisherfolk']),
            'date_of_birth' => $this->faker->dateTimeBetween('-65 years', '-18 years'),
            'gender' => $this->faker->randomElement(['male', 'female', 'other', 'prefer_not_to_say']),
            'approved_at' => null,
            'approved_by' => null,
            'rejection_reason' => null,
        ]);
    }

    /**
     * Indicate that the registration is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserRegistration::STATUS_APPROVED,
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'contact_number' => '+63' . $this->faker->numberBetween(900000000, 999999999), // UPDATED: contact_number instead of phone
            'complete_address' => $this->faker->address(),
            'barangay' => $this->faker->randomElement([
                'Barangay San Antonio', 'Barangay Santo Niño', 'Barangay Nueva',
                'Barangay Poblacion', 'Barangay Riverside', 'Barangay Central'
            ]),
            'user_type' => $this->faker->randomElement(['farmer', 'fisherfolk']),
            'date_of_birth' => $this->faker->dateTimeBetween('-65 years', '-18 years'),
            'gender' => $this->faker->randomElement(['male', 'female', 'other', 'prefer_not_to_say']),
            'approved_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'approved_by' => null, // You can set this to an actual admin ID if needed
            'rejection_reason' => null,
        ]);
    }

    /**
     * Indicate that the registration is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserRegistration::STATUS_REJECTED,
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'contact_number' => '+63' . $this->faker->numberBetween(900000000, 999999999), // UPDATED: contact_number instead of phone
            'complete_address' => $this->faker->address(),
            'barangay' => $this->faker->randomElement([
                'Barangay San Antonio', 'Barangay Santo Niño', 'Barangay Nueva',
                'Barangay Poblacion', 'Barangay Riverside', 'Barangay Central'
            ]),
            'user_type' => $this->faker->randomElement(['farmer', 'fisherfolk']),
            'approved_at' => null,
            'approved_by' => null,
            'rejected_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'rejection_reason' => $this->faker->randomElement([
                'Invalid or unreadable ID documents',
                'Incomplete information provided',
                'Unable to verify identity',
                'Documents do not match personal information',
                'Suspicious activity detected'
            ]),
        ]);
    }

    /**
     * Indicate that the email is verified.
     */
    public function emailVerified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'verification_token' => null,
        ]);
    }

    /**
     * Indicate that the email is not verified.
     */
    public function emailUnverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
            'verification_token' => Str::random(64),
        ]);
    }

    /**
     * Set specific user type - Farmer.
     */
    public function farmer(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'farmer',
        ]);
    }

    /**
     * Set specific user type - Fisherfolk.
     */
    public function fisherfolk(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'fisherfolk',
        ]);
    }

    /**
     * Create a registration with complete profile information.
     */
    public function completeProfile(): static
    {
        return $this->state(fn (array $attributes) => [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'contact_number' => '+63' . $this->faker->numberBetween(900000000, 999999999), // UPDATED: contact_number instead of phone
            'complete_address' => $this->faker->streetAddress() . ', ' . $this->faker->city(),
            'barangay' => $this->faker->randomElement([
                'Barangay San Antonio', 'Barangay Santo Niño', 'Barangay Nueva',
                'Barangay Poblacion', 'Barangay Riverside', 'Barangay Central'
            ]),
            'date_of_birth' => $this->faker->dateTimeBetween('-65 years', '-18 years'),
            'gender' => $this->faker->randomElement(['male', 'female', 'other', 'prefer_not_to_say']),
            'user_type' => $this->faker->randomElement(['farmer', 'fisherfolk']),
        ]);
    }

    /**
     * Create a registration with incomplete profile (basic signup only).
     */
    public function incompleteProfile(): static
    {
        return $this->state(fn (array $attributes) => [
            'first_name' => null,
            'last_name' => null,
            'middle_name' => null,
            'contact_number' => null, // UPDATED: contact_number instead of phone
            'complete_address' => null,
            'barangay' => null,
            'user_type' => null,
            'date_of_birth' => null,
            'gender' => null,
            'age' => null,
        ]);
    }

    /**
     * Create a recent registration.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    /**
     * Create an old registration.
     */
    public function old(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => $this->faker->dateTimeBetween('-6 months', '-1 month'),
            'updated_at' => $this->faker->dateTimeBetween('-6 months', '-1 month'),
        ]);
    }

    /**
     * Get random user agent string for testing.
     */
    private function getRandomUserAgent(): string
    {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Linux; Android 14; SM-G998B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36'
        ];

        return $this->faker->randomElement($userAgents);
    }
}