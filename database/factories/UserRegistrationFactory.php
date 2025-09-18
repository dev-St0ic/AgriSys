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
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password123'), // Default password for testing
            'phone' => $this->faker->optional()->phoneNumber(),
            'address' => $this->faker->optional()->address(),
            'user_type' => $this->faker->randomElement(['farmer', 'fisherfolk', 'general']),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'verification_token' => $this->faker->optional()->sha256(),
            'email_verified_at' => $this->faker->optional()->dateTimeBetween('-30 days', 'now'),
        ];
    }

    /**
     * Indicate that the registration is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
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
            'status' => 'approved',
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
            'status' => 'rejected',
            'approved_at' => null,
            'approved_by' => null, // You can set this to an actual admin ID if needed
            'rejection_reason' => $this->faker->sentence(),
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
     * Set specific user type.
     */
    public function farmer(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'farmer',
        ]);
    }

    public function fisherfolk(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'fisherfolk',
        ]);
    }

    public function general(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'general',
        ]);
    }

    /**
     * Create a registration with complete information.
     */
    public function complete(): static
    {
        return $this->state(fn (array $attributes) => [
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
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
}