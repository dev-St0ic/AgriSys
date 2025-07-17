<?php

namespace Database\Factories;

use App\Models\BoatrApplication;
use App\Models\User;
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
        $boatTypes = BoatrApplication::getBoatTypes();
        $fishingGears = BoatrApplication::getFishingGearTypes();
        $status = $this->faker->randomElement(['pending', 'approved', 'rejected', 'inspection_required']);
        
        // Generate creation time
        $createdAt = $this->faker->dateTimeBetween('-1 year', 'now');
        
        // Generate review data based on status
        $reviewedAt = null;
        $reviewedBy = null;
        $remarks = null;
        $inspectionCompleted = false;
        $inspectionDate = null;
        $documentPath = null;
        
        if ($status !== 'pending') {
            $reviewedAt = $this->faker->dateTimeBetween($createdAt, 'now');
            $reviewedBy = User::inRandomOrder()->first()?->id ?? 1;
            
            // Generate remarks for some entries
            if ($this->faker->boolean(70)) {
                $remarks = $this->getRandomRemarks($status);
            }
        }
        
        // If approved, ensure inspection is completed and document exists
        if ($status === 'approved') {
            $inspectionCompleted = true;
            $inspectionDate = $this->faker->dateTimeBetween($createdAt, $reviewedAt ?? 'now');
            $documentPath = $this->faker->randomElement([
                'boatr_documents/boat_inspection_report.pdf',
                'boatr_documents/vessel_certificate.jpg',
                'boatr_documents/boat_photos.png'
            ]);
        }
        
        // Some inspection_required status should have completed inspections
        if ($status === 'inspection_required' && $this->faker->boolean(30)) {
            $inspectionCompleted = true;
            $inspectionDate = $this->faker->dateTimeBetween($createdAt, 'now');
            $documentPath = $this->faker->randomElement([
                'boatr_documents/inspection_pending.pdf',
                'boatr_documents/boat_inspection.jpg'
            ]);
        }
        
        return [
            'application_number' => 'BOATR-' . strtoupper(Str::random(8)),
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->optional(0.7)->firstName,
            'last_name' => $this->faker->lastName,
            'fishr_number' => 'FISHR-' . strtoupper(Str::random(8)), // Mock FishR number
            'vessel_name' => $this->generateVesselName(),
            'boat_type' => $this->faker->randomElement($boatTypes),
            'boat_length' => $this->faker->randomFloat(2, 10, 40), // 10-40 feet
            'boat_width' => $this->faker->randomFloat(2, 3, 12),   // 3-12 feet
            'boat_depth' => $this->faker->randomFloat(2, 2, 8),    // 2-8 feet
            'engine_type' => $this->generateEngineType(),
            'engine_horsepower' => $this->faker->numberBetween(5, 150),
            'primary_fishing_gear' => $this->faker->randomElement($fishingGears),
            'supporting_document_path' => $documentPath,
            'inspection_completed' => $inspectionCompleted,
            'inspection_date' => $inspectionDate,
            'status' => $status,
            'remarks' => $remarks,
            'reviewed_at' => $reviewedAt,
            'reviewed_by' => $reviewedBy,
            'created_at' => $createdAt,
            'updated_at' => $reviewedAt ?? $createdAt,
        ];
    }

    /**
     * Generate a realistic vessel name
     */
    private function generateVesselName(): string
    {
        $prefixes = ['MV', 'FB', 'F/B', 'M/V'];
        $names = [
            'Maria Clara', 'San Miguel', 'Blessed Virgin', 'Santa Maria',
            'San Jose', 'Lucky Star', 'Blue Ocean', 'Golden Hope',
            'Sea Eagle', 'Ocean Pearl', 'Flying Fish', 'Deep Blue',
            'Morning Star', 'Silver Wave', 'Big Catch', 'Sea Hunter',
            'Ocean King', 'Wave Runner', 'Blue Marlin', 'Sea Breeze'
        ];
        
        $prefix = $this->faker->randomElement($prefixes);
        $name = $this->faker->randomElement($names);
        
        // Sometimes add a number
        if ($this->faker->boolean(40)) {
            $name .= ' ' . $this->faker->numberBetween(1, 99);
        }
        
        return $prefix . ' ' . $name;
    }

    /**
     * Generate realistic engine type
     */
    private function generateEngineType(): string
    {
        $brands = ['Yamaha', 'Honda', 'Suzuki', 'Mercury', 'Evinrude', 'Tohatsu', 'Johnson'];
        $types = ['Outboard', 'Inboard', 'Outboard Motor', 'Marine Engine'];
        
        $brand = $this->faker->randomElement($brands);
        $type = $this->faker->randomElement($types);
        
        return $brand . ' ' . $type;
    }

    /**
     * Get random remarks based on status
     */
    private function getRandomRemarks($status): string
    {
        $remarksOptions = [
            'approved' => [
                'Vessel inspection completed successfully. All requirements met.',
                'Boat meets safety and technical specifications.',
                'Approved after on-site inspection and document verification.',
                'Vessel construction and engine installation verified.',
                'Registration approved. Document uploaded after inspection.',
                'All safety equipment and requirements satisfied.',
            ],
            'rejected' => [
                'Vessel does not meet minimum safety requirements.',
                'Invalid FishR registration number provided.',
                'Boat dimensions do not match actual measurements.',
                'Engine specifications need verification.',
                'Failed safety inspection - missing required equipment.',
                'Vessel construction materials not suitable for municipal waters.',
                'Incomplete inspection documentation.',
            ],
            'inspection_required' => [
                'Scheduled for on-site vessel inspection.',
                'Awaiting physical inspection of the boat.',
                'Pending verification of vessel specifications.',
                'Requires engine and safety equipment inspection.',
            ],
            'pending' => [
                'Application received and under initial review.',
                'Pending document verification.',
                'Awaiting schedule for inspection.',
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
            'inspection_completed' => true,
            'inspection_date' => $this->faker->dateTimeBetween($attributes['created_at'] ?? '-1 month', 'now'),
            'supporting_document_path' => 'boatr_documents/approved_vessel.pdf',
            'remarks' => $this->getRandomRemarks('approved'),
            'reviewed_at' => $this->faker->dateTimeBetween($attributes['created_at'] ?? '-1 month', 'now'),
            'reviewed_by' => User::inRandomOrder()->first()?->id ?? 1,
        ]);
    }

    /**
     * State for rejected applications
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'inspection_completed' => $this->faker->boolean(50), // Some may have been inspected before rejection
            'inspection_date' => $this->faker->boolean(50) ? 
                $this->faker->dateTimeBetween($attributes['created_at'] ?? '-1 month', 'now') : null,
            'remarks' => $this->getRandomRemarks('rejected'),
            'reviewed_at' => $this->faker->dateTimeBetween($attributes['created_at'] ?? '-1 month', 'now'),
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
            'inspection_completed' => false,
            'inspection_date' => null,
            'supporting_document_path' => null,
            'remarks' => null,
            'reviewed_at' => null,
            'reviewed_by' => null,
        ]);
    }

    /**
     * State for inspection required applications
     */
    public function inspectionRequired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inspection_required',
            'inspection_completed' => false,
            'inspection_date' => null,
            'supporting_document_path' => null,
            'remarks' => $this->getRandomRemarks('inspection_required'),
            'reviewed_at' => $this->faker->dateTimeBetween($attributes['created_at'] ?? '-1 month', 'now'),
            'reviewed_by' => User::inRandomOrder()->first()?->id ?? 1,
        ]);
    }
}