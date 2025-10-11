<?php

namespace Database\Factories;

use App\Models\TrainingApplication;
use App\Models\User;
use App\Models\UserRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TrainingApplication>
 */
class TrainingApplicationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TrainingApplication::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $trainingTypes = [
            'tilapia_hito',
            'hydroponics',
            'aquaponics',
            'mushrooms',
            'livestock_poultry',
            'high_value_crops',
            'sampaguita_propagation'
        ];

        $trainingType = $this->faker->randomElement($trainingTypes);
        $status = $this->faker->randomElement(['under_review', 'approved', 'rejected']);

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
            if ($this->faker->boolean(60)) {
                $remarks = $this->getRandomRemarks($status, $trainingType);
            }
        }

        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;

        return [
            'user_id' => UserRegistration::inRandomOrder()->first()?->id ?? UserRegistration::factory()->create()->id,
            'application_number' => 'TRAIN-' . strtoupper(Str::random(8)),
            'first_name' => $firstName,
            'middle_name' => $this->faker->optional(0.7)->firstName,
            'last_name' => $lastName,
            'contact_number' => $this->faker->randomElement([
                '09' . $this->faker->numerify('#########'),
                '+639' . $this->faker->numerify('#########')
            ]),
            'email' => $this->faker->optional(0.9)->safeEmail ?? strtolower($firstName . '.' . $lastName . '@example.com'),
            'barangay' => $this->faker->randomElement([
                'Bagong Silang', 'Calendola', 'Chrysanthemum', 'Cuyab', 'Fatima',
                'G.S.I.S.', 'Landayan', 'Laram', 'Magsaysay', 'Maharlika',
                'Narra', 'Nueva', 'Pacita 1', 'Pacita 2', 'Poblacion',
                'Rosario', 'Riverside', 'Sampaguita Village', 'San Antonio',
                'San Lorenzo Ruiz', 'San Roque', 'San Vicente', 'United Bayanihan', 'United Better Living'
            ]),
            'training_type' => $trainingType,
            'document_paths' => $this->faker->optional(0.6)->randomElement([
                ['training_documents/sample_id.pdf'],
                ['training_documents/barangay_cert.jpg', 'training_documents/valid_id.png'],
                ['training_documents/proof_residence.pdf', 'training_documents/experience_cert.jpg'],
                null
            ]),
            'status' => $status,
            'remarks' => $remarks,
            'status_updated_at' => $statusUpdatedAt,
            'updated_by' => $updatedBy,
            'created_at' => $createdAt,
            'updated_at' => $statusUpdatedAt ?? $createdAt,
        ];
    }

    /**
     * Get random remarks based on status and training type
     */
    private function getRandomRemarks($status, $trainingType): string
    {
        $trainingNames = [
            'tilapia_hito' => 'Tilapia and Hito Training',
            'hydroponics' => 'Hydroponics Training',
            'aquaponics' => 'Aquaponics Training',
            'mushrooms' => 'Mushrooms Production Training',
            'livestock_poultry' => 'Livestock and Poultry Training',
            'high_value_crops' => 'High Value Crops Training',
            'sampaguita_propagation' => 'Sampaguita Propagation Training'
        ];

        $trainingName = $trainingNames[$trainingType] ?? 'Training';

        $remarksOptions = [
            'approved' => [
                "All requirements met for {$trainingName}.",
                "Application approved. Training schedule will be announced soon.",
                "Documents verified and complete for {$trainingName} enrollment.",
                "Approved for participation in the {$trainingName} program.",
                "Qualification requirements satisfied. Welcome to {$trainingName}!",
                "Application successful. Training materials will be provided.",
                "Enrollment confirmed for {$trainingName} program.",
                "All eligibility criteria met for agricultural training participation."
            ],
            'rejected' => [
                "Incomplete supporting documents submitted.",
                "Training program currently at full capacity.",
                "Age requirement not met for this training program.",
                "Required documents need verification.",
                "Application does not meet minimum requirements for {$trainingName}.",
                "Duplicate application found in the system.",
                "Invalid contact information provided.",
                "Training prerequisites not satisfied.",
                "Program schedule conflicts with applicant availability."
            ],
            'under_review' => [
                "Application under review by training coordinator.",
                "Pending document verification.",
                "Awaiting approval from Agriculture Office.",
                "Under evaluation for program eligibility."
            ]
        ];

        $options = $remarksOptions[$status] ?? ['Status updated.'];
        return $this->faker->randomElement($options);
    }

    /**
     * State for approved applications
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'remarks' => $this->getRandomRemarks('approved', $attributes['training_type'] ?? 'tilapia_hito'),
            'status_updated_at' => $this->faker->dateTimeBetween($attributes['created_at'] ?? '-1 month', 'now'),
            'updated_by' => User::inRandomOrder()->first()?->id ?? 1,
        ]);
    }

    /**
     * State for rejected applications
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'remarks' => $this->getRandomRemarks('rejected', $attributes['training_type'] ?? 'tilapia_hito'),
            'status_updated_at' => $this->faker->dateTimeBetween($attributes['created_at'] ?? '-1 month', 'now'),
            'updated_by' => User::inRandomOrder()->first()?->id ?? 1,
        ]);
    }

    /**
     * State for under review applications
     */
    public function underReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'under_review',
            'remarks' => null,
            'status_updated_at' => null,
            'updated_by' => null,
        ]);
    }

    /**
     * State for specific training types
     */
    public function trainingType($type): static
    {
        return $this->state(fn (array $attributes) => [
            'training_type' => $type,
        ]);
    }
}
