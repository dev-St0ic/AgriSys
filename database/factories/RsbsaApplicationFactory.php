<?php

namespace Database\Factories;

use App\Models\RsbsaApplication;
use App\Models\UserRegistration;
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
            $reviewedBy = UserRegistration::inRandomOrder()->first()?->id ?? 1;

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

        $firstName = $this->faker->randomElement($filipinoFirstNames);
        $middleName = $this->faker->optional(0.8)->randomElement($filipinoFirstNames);
        $lastName = $this->faker->randomElement($filipinoLastNames);
        $barangay = $this->faker->randomElement($barangays);

        // Base data
        $data = [
            'user_id' => UserRegistration::inRandomOrder()->first()?->id ?? null,
            'application_number' => 'RSBSA-' . strtoupper(Str::random(8)),
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'name_extension' => $this->faker->optional(0.15)->randomElement(['Jr.', 'Sr.', 'II', 'III', 'IV']),
            'sex' => $this->faker->randomElement(['Male', 'Female']),
            'contact_number' => '09' . $this->faker->numerify('#########'),
            'barangay' => $barangay,
            'address' => $this->faker->address(),
            'main_livelihood' => $mainLivelihood,
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

        // Add livelihood-specific fields
        switch ($mainLivelihood) {
            case 'Farmer':
                $data = array_merge($data, $this->getFarmerFields());
                break;
            case 'Farmworker/Laborer':
                $data = array_merge($data, $this->getFarmworkerFields());
                break;
            case 'Fisherfolk':
                $data = array_merge($data, $this->getFisherfolkFields());
                break;
            case 'Agri-youth':
                $data = array_merge($data, $this->getAgriYouthFields());
                break;
        }

        return $data;
    }

    /**
     * Get farmer-specific fields
     */
    private function getFarmerFields(): array
    {
        $crops = ['Rice', 'Corn', 'HVC', 'Livestock', 'Poultry', 'Agri-fishery', 'Other Crops'];
        $selectedCrop = $this->faker->randomElement($crops);

        $data = [
            'farmer_crops' => $selectedCrop,
            'farmer_land_area' => $this->faker->randomFloat(2, 0.1, 5.0),
            'farmer_type_of_farm' => $this->faker->randomElement(['Irrigated', 'Rainfed Upland', 'Rainfed Lowland']),
            'farmer_land_ownership' => $this->faker->randomElement(['Owner', 'Tenant', 'Lessee']),
            'farmer_special_status' => $this->faker->randomElement(['Ancestral Domain', 'Agrarian Reform Beneficiary', 'None']),
            'farm_location' => $this->faker->address(),
        ];

        // If "Other Crops" is selected, add the specification
        if ($selectedCrop === 'Other Crops') {
            $data['farmer_other_crops'] = $this->faker->randomElement(['Vegetables', 'Fruits', 'Root crops', 'Herbs']);
        }

        // Add optional livestock info
        if ($this->faker->boolean(70)) {
            $data['farmer_livestock'] = $this->faker->randomElement([
                'Chickens (50)',
                'Pigs (5)',
                'Cattle (3)',
                'Goats (10)',
                'Ducks (30)'
            ]);
        }

        return $data;
    }

    /**
     * Get farmworker-specific fields
     */
    private function getFarmworkerFields(): array
    {
        $types = ['Land preparation', 'Planting/Transplanting', 'Cultivation', 'Harvesting', 'Others'];
        $selectedType = $this->faker->randomElement($types);

        $data = [
            'farmworker_type' => $selectedType,
        ];

        // If "Others" is selected, add specification
        if ($selectedType === 'Others') {
            $data['farmworker_other_type'] = $this->faker->randomElement([
                'Pest Management',
                'Irrigation Maintenance',
                'Farm Equipment Operation',
                'Produce Processing'
            ]);
        }

        return $data;
    }

    /**
     * Get fisherfolk-specific fields
     */
    private function getFisherfolkFields(): array
    {
        $activities = ['Fish capture', 'Aquaculture', 'Gleaning', 'Processing', 'Vending', 'Others'];
        $selectedActivity = $this->faker->randomElement($activities);

        $data = [
            'fisherfolk_activity' => $selectedActivity,
        ];

        // If "Others" is selected, add specification
        if ($selectedActivity === 'Others') {
            $data['fisherfolk_other_activity'] = $this->faker->randomElement([
                'Fish Smoking',
                'Net Repair',
                'Fish Drying',
                'Seaweed Farming'
            ]);
        }

        return $data;
    }

    /**
     * Get agri-youth-specific fields
     */
    private function getAgriYouthFields(): array
    {
        return [
            'agriyouth_farming_household' => $this->faker->randomElement(['Yes', 'No']),
            'agriyouth_training' => $this->faker->randomElement([
                'Formal agri-fishery course',
                'Non-formal agri-fishery course',
                'None'
            ]),
            'agriyouth_participation' => $this->faker->randomElement([
                'Participated',
                'Not Participated'
            ]),
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
            'reviewed_by' => UserRegistration::inRandomOrder()->first()?->id ?? 1,
            'approved_at' => $this->faker->dateTimeBetween($attributes['created_at'] ?? '-1 month', 'now'),
            'number_assigned_at' => $this->faker->dateTimeBetween($attributes['created_at'] ?? '-1 month', 'now'),
            'assigned_by' => UserRegistration::inRandomOrder()->first()?->id ?? 1,
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
            'reviewed_by' => UserRegistration::inRandomOrder()->first()?->id ?? 1,
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
            'reviewed_by' => UserRegistration::inRandomOrder()->first()?->id ?? 1,
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

    /**
     * State for farmer applications
     */
    public function farmer(): static
    {
        return $this->state(fn (array $attributes) => array_merge($attributes, [
            'main_livelihood' => 'Farmer',
        ]));
    }

    /**
     * State for farmworker applications
     */
    public function farmworker(): static
    {
        return $this->state(fn (array $attributes) => array_merge($attributes, [
            'main_livelihood' => 'Farmworker/Laborer',
        ]));
    }

    /**
     * State for fisherfolk applications
     */
    public function fisherfolk(): static
    {
        return $this->state(fn (array $attributes) => array_merge($attributes, [
            'main_livelihood' => 'Fisherfolk',
        ]));
    }

    /**
     * State for agri-youth applications
     */
    public function agriYouth(): static
    {
        return $this->state(fn (array $attributes) => array_merge($attributes, [
            'main_livelihood' => 'Agri-youth',
        ]));
    }
}