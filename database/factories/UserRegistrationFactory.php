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
        $filipinoFirstNames = [
            'Juan', 'Jose', 'Pedro', 'Antonio', 'Miguel', 'Fernando', 'Carlos', 'Ricardo',
            'Roberto', 'Mario', 'Raul', 'Luis', 'Manuel', 'Francisco', 'Jorge', 'Rafael',
            'Maria', 'Ana', 'Rosa', 'Carmen', 'Teresa', 'Luz', 'Elena', 'Patricia',
            'Isabel', 'Gloria', 'Margarita', 'Rosario', 'Angelina', 'Cristina'
        ];

        $filipinoLastNames = [
            'Reyes', 'Santos', 'Cruz', 'Bautista', 'Garcia', 'Mendoza', 'Torres', 'Flores',
            'Rivera', 'Gonzales', 'Ramos', 'Dela Cruz', 'Sanchez', 'Villanueva', 'Castro',
            'Martinez', 'Fernandez', 'Lopez', 'Aquino', 'Hernandez', 'Marquez', 'Morales'
        ];

        $allBarangays = [
            'Bagong Silang', 'Cuyab', 'Estrella', 'G.S.I.S.', 'Landayan',
            'Langgam', 'Laram', 'Magsaysay', 'Nueva', 'Poblacion',
            'Riverside', 'San Antonio', 'San Roque', 'San Vicente', 'Santo Niño',
            'United Bayanihan', 'United Better Living', 'Sampaguita Village',
            'Calendola', 'Narra', 'Chrysanthemum', 'Fatima', 'Maharlika',
            'Pacita 1', 'Pacita 2', 'Rosario', 'San Lorenzo Ruiz'
        ];

        $firstName = $this->faker->randomElement($filipinoFirstNames);
        $lastName = $this->faker->randomElement($filipinoLastNames);

        return [
            'username' => $this->faker->unique()->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password123'),
            'status' => $this->faker->randomElement(['unverified', 'pending', 'approved', 'rejected']),
            'terms_accepted' => true,
            'privacy_accepted' => true,

            'first_name' => $firstName,
            'last_name' => $lastName,
            'middle_name' => $this->faker->optional(0.8)->randomElement($filipinoFirstNames),
            'name_extension' => $this->faker->optional(0.15)->randomElement(['Jr.', 'Sr.', 'II', 'III', 'IV']),
            'contact_number' => $this->faker->numerify('09#########'), // FIXED: Always generate
            'complete_address' => $this->faker->optional(0.7)->address(),
            'barangay' => $this->faker->optional(0.7)->randomElement($allBarangays),
            'user_type' => $this->faker->optional(0.8)->randomElement(['farmer', 'fisherfolk']),
            'date_of_birth' => $this->faker->optional(0.8)->dateTimeBetween('-70 years', '-18 years'),
            'gender' => $this->faker->optional(0.9)->randomElement(['male', 'female', 'other', 'prefer_not_to_say']),

            'verification_token' => $this->faker->optional(0.3)->sha256(),
            'email_verified_at' => $this->faker->optional(0.7)->dateTimeBetween('-30 days', 'now'),
            'username_changed_at' => null,
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
            // FIXED: Don't override contact_number - let base definition handle it
            'complete_address' => null,
            'barangay' => null,
            'user_type' => null,
            'date_of_birth' => null,
            'gender' => null,
            'age' => null,
            'approved_at' => null,
            'approved_by' => null,
            'rejection_reason' => null,
            'username_changed_at' => null,
        ]);
    }

    /**
     * Indicate that the registration is pending (profile completed).
     */
    public function pending(): static
    {
        $filipinoFirstNames = [
            'Juan', 'Jose', 'Pedro', 'Antonio', 'Miguel', 'Fernando', 'Carlos', 'Ricardo',
            'Roberto', 'Mario', 'Raul', 'Luis', 'Manuel', 'Francisco', 'Jorge', 'Rafael',
            'Maria', 'Ana', 'Rosa', 'Carmen', 'Teresa', 'Luz', 'Elena', 'Patricia',
            'Isabel', 'Gloria', 'Margarita', 'Rosario', 'Angelina', 'Cristina'
        ];

        $filipinoLastNames = [
            'Reyes', 'Santos', 'Cruz', 'Bautista', 'Garcia', 'Mendoza', 'Torres', 'Flores',
            'Rivera', 'Gonzales', 'Ramos', 'Dela Cruz', 'Sanchez', 'Villanueva', 'Castro',
            'Martinez', 'Fernandez', 'Lopez', 'Aquino', 'Hernandez', 'Marquez', 'Morales'
        ];

        $allBarangays = [
            'Bagong Silang', 'Cuyab', 'Estrella', 'G.S.I.S.', 'Landayan',
            'Langgam', 'Laram', 'Magsaysay', 'Nueva', 'Poblacion',
            'Riverside', 'San Antonio', 'San Roque', 'San Vicente', 'Santo Niño',
            'United Bayanihan', 'United Better Living', 'Sampaguita Village',
            'Calendola', 'Narra', 'Chrysanthemum', 'Fatima', 'Maharlika',
            'Pacita 1', 'Pacita 2', 'Rosario', 'San Lorenzo Ruiz'
        ];

        return $this->state(fn (array $attributes) => [
            'status' => UserRegistration::STATUS_PENDING,
            'first_name' => $this->faker->randomElement($filipinoFirstNames),
            'last_name' => $this->faker->randomElement($filipinoLastNames),
            'middle_name' => $this->faker->optional(0.8)->randomElement($filipinoFirstNames),
            'name_extension' => $this->faker->optional(0.15)->randomElement(['Jr.', 'Sr.', 'II', 'III', 'IV']),
            'contact_number' => '+63' . $this->faker->numberBetween(900000000, 999999999),
            'complete_address' => $this->faker->address(),
            'barangay' => $this->faker->randomElement($allBarangays),
            'user_type' => $this->faker->randomElement(['farmer', 'fisherfolk']),
            'date_of_birth' => $this->faker->dateTimeBetween('-65 years', '-18 years'),
            'gender' => $this->faker->randomElement(['male', 'female', 'other', 'prefer_not_to_say']),
            'approved_at' => null,
            'approved_by' => null,
            'rejection_reason' => null,
            'username_changed_at' => null,
        ]);
    }

    /**
     * Indicate that the registration is approved.
     */
    public function approved(): static
    {
        $filipinoFirstNames = [
            'Juan', 'Jose', 'Pedro', 'Antonio', 'Miguel', 'Fernando', 'Carlos', 'Ricardo',
            'Roberto', 'Mario', 'Raul', 'Luis', 'Manuel', 'Francisco', 'Jorge', 'Rafael',
            'Maria', 'Ana', 'Rosa', 'Carmen', 'Teresa', 'Luz', 'Elena', 'Patricia',
            'Isabel', 'Gloria', 'Margarita', 'Rosario', 'Angelina', 'Cristina'
        ];

        $filipinoLastNames = [
            'Reyes', 'Santos', 'Cruz', 'Bautista', 'Garcia', 'Mendoza', 'Torres', 'Flores',
            'Rivera', 'Gonzales', 'Ramos', 'Dela Cruz', 'Sanchez', 'Villanueva', 'Castro',
            'Martinez', 'Fernandez', 'Lopez', 'Aquino', 'Hernandez', 'Marquez', 'Morales'
        ];

        $allBarangays = [
            'Bagong Silang', 'Cuyab', 'Estrella', 'G.S.I.S.', 'Landayan',
            'Langgam', 'Laram', 'Magsaysay', 'Nueva', 'Poblacion',
            'Riverside', 'San Antonio', 'San Roque', 'San Vicente', 'Santo Niño',
            'United Bayanihan', 'United Better Living', 'Sampaguita Village',
            'Calendola', 'Narra', 'Chrysanthemum', 'Fatima', 'Maharlika',
            'Pacita 1', 'Pacita 2', 'Rosario', 'San Lorenzo Ruiz'
        ];

        return $this->state(fn (array $attributes) => [
            'status' => UserRegistration::STATUS_APPROVED,
            'first_name' => $this->faker->randomElement($filipinoFirstNames),
            'last_name' => $this->faker->randomElement($filipinoLastNames),
            'middle_name' => $this->faker->optional(0.8)->randomElement($filipinoFirstNames),
            'name_extension' => $this->faker->optional(0.15)->randomElement(['Jr.', 'Sr.', 'II', 'III', 'IV']),
            'contact_number' => '+63' . $this->faker->numberBetween(900000000, 999999999),
            'complete_address' => $this->faker->address(),
            'barangay' => $this->faker->randomElement($allBarangays),
            'user_type' => $this->faker->randomElement(['farmer', 'fisherfolk']),
            'date_of_birth' => $this->faker->dateTimeBetween('-65 years', '-18 years'),
            'gender' => $this->faker->randomElement(['male', 'female', 'other', 'prefer_not_to_say']),
            'approved_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'approved_by' => null,
            'rejection_reason' => null,
            'username_changed_at' => null,
        ]);
    }

    /**
     * Indicate that the registration is rejected.
     */
    public function rejected(): static
    {
        $filipinoFirstNames = [
            'Juan', 'Jose', 'Pedro', 'Antonio', 'Miguel', 'Fernando', 'Carlos', 'Ricardo',
            'Roberto', 'Mario', 'Raul', 'Luis', 'Manuel', 'Francisco', 'Jorge', 'Rafael',
            'Maria', 'Ana', 'Rosa', 'Carmen', 'Teresa', 'Luz', 'Elena', 'Patricia',
            'Isabel', 'Gloria', 'Margarita', 'Rosario', 'Angelina', 'Cristina'
        ];

        $filipinoLastNames = [
            'Reyes', 'Santos', 'Cruz', 'Bautista', 'Garcia', 'Mendoza', 'Torres', 'Flores',
            'Rivera', 'Gonzales', 'Ramos', 'Dela Cruz', 'Sanchez', 'Villanueva', 'Castro',
            'Martinez', 'Fernandez', 'Lopez', 'Aquino', 'Hernandez', 'Marquez', 'Morales'
        ];

        $allBarangays = [
            'Bagong Silang', 'Cuyab', 'Estrella', 'G.S.I.S.', 'Landayan',
            'Langgam', 'Laram', 'Magsaysay', 'Nueva', 'Poblacion',
            'Riverside', 'San Antonio', 'San Roque', 'San Vicente', 'Santo Niño',
            'United Bayanihan', 'United Better Living', 'Sampaguita Village',
            'Calendola', 'Narra', 'Chrysanthemum', 'Fatima', 'Maharlika',
            'Pacita 1', 'Pacita 2', 'Rosario', 'San Lorenzo Ruiz'
        ];

        return $this->state(fn (array $attributes) => [
            'status' => UserRegistration::STATUS_REJECTED,
            'first_name' => $this->faker->randomElement($filipinoFirstNames),
            'last_name' => $this->faker->randomElement($filipinoLastNames),
            'middle_name' => $this->faker->optional(0.8)->randomElement($filipinoFirstNames),
            'name_extension' => $this->faker->optional(0.15)->randomElement(['Jr.', 'Sr.', 'II', 'III', 'IV']),
            'contact_number' => '+63' . $this->faker->numberBetween(900000000, 999999999),
            'complete_address' => $this->faker->address(),
            'barangay' => $this->faker->randomElement($allBarangays),
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
            'username_changed_at' => null,
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
            'contact_number' => '+63' . $this->faker->numberBetween(900000000, 999999999),
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
     * FIXED: Don't set contact_number to null - let base definition handle it
     */
    public function incompleteProfile(): static
    {
        return $this->state(fn (array $attributes) => [
            'first_name' => null,
            'last_name' => null,
            'middle_name' => null,
            // FIXED: Don't override contact_number - let base definition handle it
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
     * Username has been changed
     */
    public function usernameChanged(): static
    {
        return $this->state(fn (array $attributes) => [
            'username_changed_at' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }
}