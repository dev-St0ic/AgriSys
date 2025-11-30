<?php

namespace Database\Factories;

use App\Models\BoatrApplication;
use App\Models\User;
use App\Models\UserRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BoatrApplication>
 */
class BoatrApplicationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BoatrApplication::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Filipino names for realistic data
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

        $nameExtensions = ['Jr.', 'Sr.', 'II', 'III', 'IV'];

        $firstName = $this->faker->randomElement($filipinoFirstNames);
        $middleName = $this->faker->optional(0.8)->randomElement($filipinoFirstNames);
        $lastName = $this->faker->randomElement($filipinoLastNames);
        $nameExtension = $this->faker->optional(0.15)->randomElement($nameExtensions);

        // Generate unique application number
        $applicationNumber = 'BOATR-' . strtoupper(Str::random(8));

        // Generate fake FishR number
        $fishrNumber = 'FISHR-' . strtoupper(Str::random(8));

        // Boat types from your model
        $boatTypes = [
            'Spoon',
            'Plumb',
            'Banca',
            'Rake Stem - Rake Stern',
            'Rake Stem - Transom/Spoon/Plumb Stern',
            'Skiff (Typical Design)'
        ];

        // Fishing gear types from your model
        $fishingGears = [
            'Hook and Line',
            'Bottom Set Gill Net',
            'Fish Trap',
            'Fish Coral'
        ];

        // Engine types
        $engineTypes = [
            'Yamaha Outboard Motor',
            'Honda Marine Engine',
            'Suzuki Outboard',
            'Mercury Outboard',
            'Tohatsu Outboard',
            'Johnson Outboard'
        ];

        return [
            'user_id' => UserRegistration::inRandomOrder()->first()?->id ?? UserRegistration::factory()->create()->id,
            'application_number' => $applicationNumber,
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'name_extension' => $nameExtension,
            'contact_number' => $this->faker->phoneNumber(), // Added missing contact_number field
            'barangay' => $this->faker->randomElement([
                'Bagong Silang', 'Cuyab', 'Estrella', 'G.S.I.S.', 'Landayan',
                'Langgam', 'Laram', 'Magsaysay', 'Nueva', 'Poblacion',
                'Riverside', 'San Antonio', 'San Roque', 'San Vicente', 'Santo Niño',
                'United Bayanihan', 'United Better Living', 'Sampaguita Village',
                'Calendola', 'Narra', 'Chrysanthemum', 'Fatima', 'Maharlika',
                'Pacita 1', 'Pacita 2', 'Rosario', 'San Lorenzo Ruiz'
            ]),
            'fishr_number' => $fishrNumber,
            'vessel_name' => 'MV ' . $this->faker->words(2, true),
            'boat_type' => $this->faker->randomElement($boatTypes),
            'boat_length' => $this->faker->randomFloat(2, 8.0, 25.0), // 8-25 feet
            'boat_width' => $this->faker->randomFloat(2, 2.0, 8.0),   // 2-8 feet
            'boat_depth' => $this->faker->randomFloat(2, 1.0, 4.0),   // 1-4 feet
            'engine_type' => $this->faker->randomElement($engineTypes),
            'engine_horsepower' => $this->faker->numberBetween(15, 150),
            'primary_fishing_gear' => $this->faker->randomElement($fishingGears),

            // Single document fields (may be null)
            'user_document_path' => $this->faker->optional(0.6)->passthrough('boatr_documents/user_uploads/sample_' . Str::random(8) . '.pdf'),
            'user_document_name' => $this->faker->optional(0.6)->passthrough('sample_document.pdf'),
            'user_document_type' => $this->faker->optional(0.6)->passthrough('pdf'),
            'user_document_size' => $this->faker->optional(0.6)->numberBetween(100000, 2000000), // 100KB - 2MB
            'user_document_uploaded_at' => $this->faker->optional(0.6)->dateTimeBetween('-30 days', 'now'),

            // Inspection documents (JSON - may be null)
            'inspection_documents' => null, // Will be set by state methods if needed
            'inspection_completed' => false,
            'inspection_date' => null,
            'inspection_notes' => null,
            'inspected_by' => null,
            'documents_verified' => false,
            'documents_verified_at' => null,
            'document_verification_notes' => null,

            // Status and workflow
            'status' => 'pending',
            'remarks' => null,
            'reviewed_at' => null,
            'reviewed_by' => null,
            'status_history' => null,
            'inspection_scheduled_at' => null,
            'approved_at' => null,
            'rejected_at' => null,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now')
        ];
    }

    /**
     * Indicate that the application is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'inspection_completed' => false,
            'documents_verified' => false,
        ]);
    }

    /**
     * Indicate that the application is under review.
     */
    public function underReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'under_review',
            'reviewed_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'reviewed_by' => User::where('role', 'admin')->inRandomOrder()->first()?->id,
            'inspection_completed' => false,
        ]);
    }

    /**
     * Indicate that the application requires inspection.
     */
    public function inspectionRequired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inspection_required',
            'reviewed_at' => $this->faker->dateTimeBetween('-5 days', 'now'),
            'reviewed_by' => User::where('role', 'admin')->inRandomOrder()->first()?->id,
            'inspection_completed' => false,
        ]);
    }

    /**
     * Indicate that the application has inspection scheduled.
     */
    public function inspectionScheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inspection_scheduled',
            'reviewed_at' => $this->faker->dateTimeBetween('-3 days', 'now'),
            'reviewed_by' => User::where('role', 'admin')->inRandomOrder()->first()?->id,
            'inspection_scheduled_at' => $this->faker->dateTimeBetween('now', '+7 days'),
            'inspection_completed' => false,
        ]);
    }

    /**
     * Indicate that the application is awaiting documents.
     */
    public function documentsPending(): static
    {
        $adminUser = User::where('role', 'admin')->inRandomOrder()->first();

        return $this->state(fn (array $attributes) => [
            'status' => 'documents_pending',
            'reviewed_at' => $this->faker->dateTimeBetween('-2 days', 'now'),
            'reviewed_by' => $adminUser?->id,
            'inspection_completed' => true,
            'inspection_date' => $this->faker->dateTimeBetween('-2 days', 'now'),
            'inspection_notes' => $this->faker->sentence(),
            'inspected_by' => $adminUser?->id,
            'inspection_documents' => [
                [
                    'path' => 'boatr_documents/inspection/inspection_' . Str::random(8) . '.pdf',
                    'original_name' => 'inspection_report.pdf',
                    'type' => 'pdf',
                    'uploaded_at' => now()->toISOString(),
                    'uploaded_by' => $adminUser?->id,
                    'notes' => 'Inspection completed successfully',
                    'size' => $this->faker->numberBetween(500000, 2000000)
                ]
            ],
        ]);
    }

    /**
     * Indicate that the application is approved.
     */
    public function approved(): static
    {
        $adminUser = User::where('role', 'admin')->inRandomOrder()->first();
        $inspectionDate = $this->faker->dateTimeBetween('-10 days', '-2 days');
        $approvedDate = $this->faker->dateTimeBetween($inspectionDate, 'now');

        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'reviewed_at' => $approvedDate,
            'reviewed_by' => $adminUser?->id,
            'approved_at' => $approvedDate,
            'inspection_completed' => true,
            'inspection_date' => $inspectionDate,
            'inspection_notes' => 'Inspection passed. Boat meets all requirements.',
            'inspected_by' => $adminUser?->id,
            'documents_verified' => true,
            'documents_verified_at' => $approvedDate,
            'inspection_documents' => [
                [
                    'path' => 'boatr_documents/inspection/inspection_' . Str::random(8) . '.pdf',
                    'original_name' => 'inspection_report.pdf',
                    'type' => 'pdf',
                    'uploaded_at' => $inspectionDate->format('c'),
                    'uploaded_by' => $adminUser?->id,
                    'notes' => 'Final inspection report - approved',
                    'size' => $this->faker->numberBetween(500000, 2000000)
                ],
                [
                    'path' => 'boatr_documents/inspection/boat_photos_' . Str::random(8) . '.jpg',
                    'original_name' => 'boat_photos.jpg',
                    'type' => 'jpg',
                    'uploaded_at' => $inspectionDate->format('c'),
                    'uploaded_by' => $adminUser?->id,
                    'notes' => 'Boat inspection photos',
                    'size' => $this->faker->numberBetween(1000000, 3000000)
                ]
            ],
            'remarks' => 'Application approved after successful inspection.',
        ]);
    }

    /**
     * Indicate that the application is rejected.
     */
    public function rejected(): static
    {
        $adminUser = User::where('role', 'admin')->inRandomOrder()->first();
        $rejectedDate = $this->faker->dateTimeBetween('-5 days', 'now');

        $rejectionReasons = [
            'Boat does not meet safety requirements.',
            'Invalid or expired FishR registration.',
            'Boat dimensions exceed municipal fishing limits.',
            'Required documents not provided.',
            'Boat condition does not meet standards.'
        ];

        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'reviewed_at' => $rejectedDate,
            'reviewed_by' => $adminUser?->id,
            'rejected_at' => $rejectedDate,
            'remarks' => $this->faker->randomElement($rejectionReasons),
            'inspection_completed' => $this->faker->boolean(70), // 70% have completed inspection
        ]);
    }

    /**
     * Indicate that the application has user document uploaded.
     */
    public function withUserDocument(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_document_path' => 'boatr_documents/user_uploads/document_' . Str::random(8) . '.pdf',
            'user_document_name' => 'user_document.pdf',
            'user_document_type' => 'pdf',
            'user_document_size' => $this->faker->numberBetween(100000, 2000000),
            'user_document_uploaded_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    /**
     * Indicate that the application has inspection documents.
     */
    public function withInspectionDocuments(): static
    {
        $adminUser = User::where('role', 'admin')->inRandomOrder()->first();

        return $this->state(fn (array $attributes) => [
            'inspection_documents' => [
                [
                    'path' => 'boatr_documents/inspection/inspection_' . Str::random(8) . '.pdf',
                    'original_name' => 'inspection_report.pdf',
                    'type' => 'pdf',
                    'uploaded_at' => now()->toISOString(),
                    'uploaded_by' => $adminUser?->id,
                    'notes' => 'Boat inspection report',
                    'size' => $this->faker->numberBetween(500000, 2000000)
                ]
            ],
        ]);
    }
}
