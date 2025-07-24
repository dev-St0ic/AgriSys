<?php

namespace Database\Factories;

use App\Models\RsbsaApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RsbsaApplication>
 */
class RsbsaApplicationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RsbsaApplication::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $livelihoods = ['Farmer', 'Farmworker/Laborer', 'Fisherfolk', 'Agri-youth'];
        $mainLivelihood = $this->faker->randomElement($livelihoods);
        $status = $this->faker->randomElement(['pending', 'under_review', 'approved', 'rejected']);
        
        $barangays = [
            'Bagong Silang', 'Cuyab', 'Estrella', 'G.S.I.S.', 'Landayan',
            'Langgam', 'Laram', 'Magsaysay', 'Nueva', 'Poblacion',
            'Riverside', 'San Antonio', 'San Roque', 'San Vicente', 'Santo Niño',
            'United Bayanihan', 'United Better Living', 'Sampaguita Village',
            'Calendola', 'Narra', 'Chrysanthemum', 'Fatima', 'Maharlika',
            'Pacita 1', 'Pacita 2', 'Rosario', 'San Lorenzo Ruiz'
        ];
        
        // Generate creation time
        $createdAt = $this->faker->dateTimeBetween('-1 year', 'now');
        
        // Generate review time and related fields (if status is not pending)
        $reviewedAt = null;
        $reviewedBy = null;
        $approvedAt = null;
        $rejectedAt = null;
        $remarks = null;
        $numberAssignedAt = null;
        $assignedBy = null;
        
        if ($status !== 'pending') {
            $reviewedAt = $this->faker->dateTimeBetween($createdAt, 'now');
            $reviewedBy = User::inRandomOrder()->first()?->id ?? 1; // Assumes you have users
            
            if ($status === 'approved') {
                $approvedAt = $reviewedAt;
                $numberAssignedAt = $reviewedAt;
                $assignedBy = $reviewedBy;
            } elseif ($status === 'rejected') {
                $rejectedAt = $reviewedAt;
            }
            
            // Generate remarks for some entries
            if ($this->faker->boolean(70)) { // 70% chance of having remarks
                $remarks = $this->getRandomRemarks($status);
            }
        }
        
        return [
            'application_number' => 'RSBSA-' . strtoupper(Str::random(8)),
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->optional(0.7)->firstName,
            'last_name' => $this->faker->lastName,
            'sex' => $this->faker->randomElement(['Male', 'Female']),
            'mobile_number' => $this->faker->randomElement([
                '09' . $this->faker->numerify('#########'),
                '+639' . $this->faker->numerify('#########')
            ]),
            'barangay' => $this->faker->randomElement($barangays),
            'main_livelihood' => $mainLivelihood,
            'land_area' => $this->faker->optional(0.8)->randomFloat(2, 0.1, 5.0), // 0.1 to 5 hectares
            'farm_location' => $this->faker->optional(0.8)->address,
            'commodity' => $this->getCommodityBasedOnLivelihood($mainLivelihood),
            'supporting_document_path' => $this->faker->optional(0.6)->randomElement([
                'rsbsa_documents/farm_photo.jpg',
                'rsbsa_documents/land_title.pdf',
                'rsbsa_documents/barangay_cert.jpg',
                'rsbsa_documents/id_photo.png'
            ]),
            'status' => $status,
            'remarks' => $remarks,
            'reviewed_at' => $reviewedAt,
            'reviewed_by' => $reviewedBy,
            'approved_at' => $approvedAt,
            'rejected_at' => $rejectedAt,
            'number_assigned_at' => $numberAssignedAt,
            'assigned_by' => $assignedBy,
            'created_at' => $createdAt,
            'updated_at' => $reviewedAt ?? $createdAt,
        ];
    }

    /**
     * Get commodity based on the selected livelihood
     */
    private function getCommodityBasedOnLivelihood($livelihood): ?string
    {
        $commodities = [
            'Farmer' => [
                'Rice, Corn, Vegetables',
                'Coconut, Banana, Mango',
                'Tomato, Eggplant, Okra',
                'Rice, Sugarcane',
                'Vegetables, Root crops',
                'Fruit trees, Vegetables'
            ],
            'Farmworker/Laborer' => [
                'Rice production',
                'Vegetable farming',
                'Fruit harvesting',
                'General farm work',
                'Crop maintenance'
            ],
            'Fisherfolk' => [
                'Tilapia, Bangus',
                'Freshwater fish farming',
                'Fish cage operations',
                'Pond aquaculture',
                'Fish processing'
            ],
            'Agri-youth' => [
                'Organic vegetables',
                'Urban gardening',
                'Hydroponics',
                'Mushroom cultivation',
                'Herb farming'
            ]
        ];

        $options = $commodities[$livelihood] ?? ['Mixed farming'];
        return $this->faker->randomElement($options);
    }

    /**
     * Get random remarks based on status
     */
    private function getRandomRemarks($status): string
    {
        $remarksOptions = [
            'approved' => [
                'All documents verified and complete.',
                'Applicant meets all RSBSA registration requirements.',
                'Application approved after field verification.',
                'Documents authenticated and validated.',
                'Farm location verified through barangay confirmation.',
                'Complete application with valid supporting documents.',
                'Livelihood activities confirmed.',
            ],
            'rejected' => [
                'Incomplete supporting documents.',
                'Unable to verify farm location.',
                'Invalid or expired identification documents.',
                'Barangay certification required.',
                'Need additional proof of agricultural activity.',
                'Application does not meet RSBSA requirements.',
                'Duplicate registration found in the system.',
                'Farm area could not be verified.',
            ],
            'under_review' => [
                'Pending document verification.',
                'Awaiting barangay confirmation.',
                'Under field verification process.',
                'Documents under review.',
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
            'remarks' => $this->getRandomRemarks('approved'),
            'reviewed_at' => $this->faker->dateTimeBetween($attributes['created_at'] ?? '-1 month', 'now'),
            'reviewed_by' => User::inRandomOrder()->first()?->id ?? 1,
            'approved_at' => $this->faker->dateTimeBetween($attributes['created_at'] ?? '-1 month', 'now'),
            'number_assigned_at' => $this->faker->dateTimeBetween($attributes['created_at'] ?? '-1 month', 'now'),
            'assigned_by' => User::inRandomOrder()->first()?->id ?? 1,
        ]);
    }

    /**
     * State for rejected applications
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'remarks' => $this->getRandomRemarks('rejected'),
            'reviewed_at' => $this->faker->dateTimeBetween($attributes['created_at'] ?? '-1 month', 'now'),
            'reviewed_by' => User::inRandomOrder()->first()?->id ?? 1,
            'rejected_at' => $this->faker->dateTimeBetween($attributes['created_at'] ?? '-1 month', 'now'),
        ]);
    }

    /**
     * State for under review applications
     */
    public function underReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'under_review',
            'remarks' => $this->getRandomRemarks('under_review'),
            'reviewed_at' => $this->faker->dateTimeBetween($attributes['created_at'] ?? '-1 week', 'now'),
            'reviewed_by' => User::inRandomOrder()->first()?->id ?? 1,
        ]);
    }

    /**
     * State for pending applications
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'remarks' => null,
            'reviewed_at' => null,
            'reviewed_by' => null,
            'approved_at' => null,
            'rejected_at' => null,
            'number_assigned_at' => null,
            'assigned_by' => null,
        ]);
    }
}