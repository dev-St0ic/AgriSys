<?php

namespace Database\Factories;

use App\Models\FishrApplication;
use App\Models\User;
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
        $status = $this->faker->randomElement(['under_review', 'approved', 'rejected']);
        $barangays = [
            'Bagong Silang', 'Cuyab', 'Estrella', 'Poblacion', 'Riverside',
            'G.S.I.S.', 'Landayan', 'Langgam', 'Laram', 'Magsaysay',
            'Nueva', 'San Antonio', 'San Roque', 'San Vicente', 'Santo Niño'
        ];
        
        // Generate creation time
        $createdAt = $this->faker->dateTimeBetween('-1 year', 'now');
        
        // Generate status update time (if status is not under_review)
        $statusUpdatedAt = null;
        $updatedBy = null;
        $remarks = null;
        
        if ($status !== 'under_review') {
            $statusUpdatedAt = $this->faker->dateTimeBetween($createdAt, 'now');
            $updatedBy = User::inRandomOrder()->first()?->id ?? 1; // Assumes you have users
            
            // Generate remarks for some entries
            if ($this->faker->boolean(60)) { // 60% chance of having remarks
                $remarks = $this->getRandomRemarks($status);
            }
        }

        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        
        return [
            'registration_number' => 'FISHR-' . strtoupper(Str::random(8)),
            'first_name' => $firstName,
            'middle_name' => $this->faker->optional(0.7)->firstName,
            'last_name' => $lastName,
            'sex' => $this->faker->randomElement(['Male', 'Female']),
            'barangay' => $this->faker->randomElement($barangays),
            'contact_number' => $this->faker->randomElement([
                '09' . $this->faker->numerify('#########'),
                '+639' . $this->faker->numerify('#########')
            ]),
            'email' => $this->faker->optional(0.8)->safeEmail ?? strtolower($firstName . '.' . $lastName . '@example.com'), // Add email field
            'main_livelihood' => $mainLivelihood,
            'livelihood_description' => $this->getLivelihoodDescription($mainLivelihood),
            'other_livelihood' => $mainLivelihood === 'others' ? $this->faker->jobTitle : null,
            'document_path' => $this->faker->optional(0.4)->randomElement([
                'fishr_documents/sample_id.pdf',
                'fishr_documents/barangay_cert.jpg',
                'fishr_documents/proof_fishing.png'
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
     * State for approved registrations
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'remarks' => $this->getRandomRemarks('approved'),
            'status_updated_at' => $this->faker->dateTimeBetween($attributes['created_at'] ?? '-1 month', 'now'),
            'updated_by' => User::inRandomOrder()->first()?->id ?? 1,
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
        ]);
    }
}