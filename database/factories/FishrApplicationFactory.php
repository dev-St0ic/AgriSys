<?php

namespace Database\Factories;

use App\Models\FishrApplication;
use App\Models\User;
use App\Models\UserRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FishrApplication>
 */
class FishrApplicationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FishrApplication::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $livelihoods = ['capture', 'aquaculture', 'vending', 'processing', 'others'];
        $mainLivelihood = $this->faker->randomElement($livelihoods);
        
        // Secondary livelihood - can be null or different from main
        $secondaryLivelihood = null;
        if ($this->faker->boolean(60)) { // 60% chance of having secondary
            do {
                $secondaryLivelihood = $this->faker->randomElement($livelihoods);
            } while ($secondaryLivelihood === $mainLivelihood);
        }
        
        $status = $this->faker->randomElement(['pending', 'under_review', 'approved', 'rejected']);

        // All 27 barangays from San Pedro, Laguna
        $barangays = [
            'Bagong Silang', 'Cuyab', 'Estrella', 'G.S.I.S.', 'Landayan',
            'Langgam', 'Laram', 'Magsaysay', 'Nueva', 'Poblacion',
            'Riverside', 'San Antonio', 'San Roque', 'San Vicente', 'Santo Niño',
            'United Bayanihan', 'United Better Living', 'Sampaguita Village',
            'Calendola', 'Narra', 'Chrysanthemum', 'Fatima', 'Maharlika',
            'Pacita 1', 'Pacita 2', 'Rosario', 'San Lorenzo Ruiz'
        ];

        // Filipino names
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

        // Generate creation time
        $createdAt = $this->faker->dateTimeBetween('-1 year', 'now');

        // Generate status update time (if status is not under_review)
        $statusUpdatedAt = null;
        $updatedBy = null;
        $remarks = null;

        if ($status !== 'under_review') {
            $statusUpdatedAt = $this->faker->dateTimeBetween($createdAt, 'now');
            $updatedBy = User::inRandomOrder()->first()?->id ?? 1;

            // Generate remarks for some entries
            if ($this->faker->boolean(60)) { // 60% chance of having remarks
                $remarks = $this->getRandomRemarks($status);
            }
        }

        // Generate FishR number for approved registrations
        $fishrNumber = null;
        $fishrNumberAssignedAt = null;
        $fishrNumberAssignedBy = null;

        if ($status === 'approved' && $this->faker->boolean(70)) { // 70% of approved have FishR number
            $fishrNumber = 'FISHR-' . strtoupper(Str::random(8));
            $fishrNumberAssignedAt = $this->faker->dateTimeBetween($statusUpdatedAt ?? $createdAt, 'now');
            $fishrNumberAssignedBy = User::inRandomOrder()->first()?->id ?? 1;
        }

        $firstName = $this->faker->randomElement($filipinoFirstNames);
        $middleName = $this->faker->optional(0.8)->randomElement($filipinoFirstNames);
        $lastName = $this->faker->randomElement($filipinoLastNames);

        return [
            'user_id' => UserRegistration::inRandomOrder()->first()?->id ?? UserRegistration::factory()->create()->id,
            'registration_number' => 'FISHR-' . strtoupper(Str::random(8)),
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'name_extension' => $this->faker->optional(0.15)->randomElement(['Jr.', 'Sr.', 'II', 'III', 'IV']),
            'sex' => $this->faker->randomElement(['Male', 'Female']),
            'barangay' => $this->faker->randomElement($barangays),
            'contact_number' => $this->faker->randomElement([
                '09' . $this->faker->numerify('#########'),
                '09' . $this->faker->numerify('#########')
            ]),
            // Main livelihood
            'main_livelihood' => $mainLivelihood,
            'livelihood_description' => $this->getLivelihoodDescription($mainLivelihood),
            'other_livelihood' => $mainLivelihood === 'others' ? $this->faker->jobTitle : null,
            // Secondary livelihood (NEW)
            'secondary_livelihood' => $secondaryLivelihood,
            'other_secondary_livelihood' => $secondaryLivelihood === 'others' ? $this->faker->jobTitle : null,
            // Document
            'document_path' => $this->faker->optional(0.4)->randomElement([
                'fishr_documents/sample_id.pdf',
                'fishr_documents/barangay_cert.jpg',
                'fishr_documents/proof_fishing.png'
            ]),
            // Status
            'status' => $status,
            'remarks' => $remarks,
            'status_updated_at' => $statusUpdatedAt,
            'updated_by' => $updatedBy,
            // FishR Number
            'fishr_number' => $fishrNumber,
            'fishr_number_assigned_at' => $fishrNumberAssignedAt,
            'fishr_number_assigned_by' => $fishrNumberAssignedBy,
            // Timestamps
            'created_at' => $createdAt,
            'updated_at' => $statusUpdatedAt ?? $createdAt,
        ];
    }

    /**
     * Get livelihood description based on the selected option
     */
    private function getLivelihoodDescription($mainLivelihood): string
    {
        $descriptions = [
            'capture' => 'Capture Fishing',
            'aquaculture' => 'Aquaculture',
            'vending' => 'Fish Vending',
            'processing' => 'Fish Processing',
            'others' => 'Others'
        ];

        return $descriptions[$mainLivelihood] ?? 'Unknown';
    }

    /**
     * Get random remarks based on status
     */
    private function getRandomRemarks($status): string
    {
        $remarksOptions = [
            'pending' => [
                'Awaiting initial review.',
                'New application received.',
                'Pending admin review.',
            ],
            'approved' => [
                'All documents verified and complete.',
                'Applicant meets all requirements for fisherfolk registration.',
                'Approved after site verification.',
                'Documents authenticated. Registration approved.',
                'Livelihood activities confirmed through barangay verification.',
                'Complete application with valid supporting documents.',
            ],
            'rejected' => [
                'Incomplete supporting documents.',
                'Unable to verify fishing activities in the specified area.',
                'Invalid contact information provided.',
                'Barangay certification required.',
                'Need additional proof of fishing livelihood.',
                'Application does not meet minimum requirements.',
                'Duplicate registration found in the system.',
            ],
            'under_review' => [
                'Pending document verification.',
                'Awaiting barangay confirmation.',
                'Under processing.',
            ]
        ];

        $options = $remarksOptions[$status] ?? ['Status updated.'];
        return $this->faker->randomElement($options);
    }

    /**
     * State for pending registrations
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'remarks' => null,
            'status_updated_at' => null,
            'updated_by' => null,
            'fishr_number' => null,
            'fishr_number_assigned_at' => null,
            'fishr_number_assigned_by' => null,
        ]);
    }

    /**
     * State for under review registrations
     */
    public function underReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'under_review',
            'remarks' => null,
            'status_updated_at' => null,
            'updated_by' => null,
            'fishr_number' => null,
            'fishr_number_assigned_at' => null,
            'fishr_number_assigned_by' => null,
        ]);
    }

    /**
     * State for approved registrations
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'remarks' => $this->getRandomRemarks('approved'),
            'status_updated_at' => $this->faker->dateTimeBetween($attributes['created_at'] ?? '-1 month', 'now'),
            'updated_by' => User::inRandomOrder()->first()?->id ?? 1,
            // Maybe assign FishR number
            'fishr_number' => $this->faker->boolean(70) ? 'FISHR-' . strtoupper(Str::random(8)) : null,
            'fishr_number_assigned_at' => $this->faker->boolean(70) ? 
                $this->faker->dateTimeBetween($attributes['created_at'] ?? '-1 month', 'now') : null,
            'fishr_number_assigned_by' => $this->faker->boolean(70) ? 
                User::inRandomOrder()->first()?->id ?? 1 : null,
        ]);
    }

    /**
     * State for rejected registrations
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'remarks' => $this->getRandomRemarks('rejected'),
            'status_updated_at' => $this->faker->dateTimeBetween($attributes['created_at'] ?? '-1 month', 'now'),
            'updated_by' => User::inRandomOrder()->first()?->id ?? 1,
            'fishr_number' => null,
            'fishr_number_assigned_at' => null,
            'fishr_number_assigned_by' => null,
        ]);
    }

    /**
     * State for registrations with secondary livelihood
     */
    public function withSecondaryLivelihood(): static
    {
        return $this->state(fn (array $attributes) => [
            'secondary_livelihood' => $this->faker->randomElement(['capture', 'aquaculture', 'vending', 'processing']),
        ]);
    }

    /**
     * State for registrations without secondary livelihood
     */
    public function withoutSecondaryLivelihood(): static
    {
        return $this->state(fn (array $attributes) => [
            'secondary_livelihood' => null,
            'other_secondary_livelihood' => null,
        ]);
    }
}