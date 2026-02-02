<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SeedlingRequest;
use App\Models\User;
use App\Models\UserRegistration;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SeedlingRequest>
 */
class SeedlingRequestFactory extends Factory
{
    protected $model = SeedlingRequest::class;

    public function definition(): array
    {
        // Available items for each category
        $availableSeeds = [
            ['name' => 'Emerald Bitter Gourd Seeds', 'quantity' => $this->faker->numberBetween(1, 10)],
            ['name' => 'Golden Harvest Rice Seeds', 'quantity' => $this->faker->numberBetween(1, 15)],
            ['name' => 'Green Gem String Bean Seeds', 'quantity' => $this->faker->numberBetween(1, 12)],
            ['name' => 'Okra Seeds', 'quantity' => $this->faker->numberBetween(1, 8)],
            ['name' => 'Pioneer Hybrid Corn Seeds', 'quantity' => $this->faker->numberBetween(1, 10)],
            ['name' => 'Red Ruby Tomato Seeds', 'quantity' => $this->faker->numberBetween(1, 15)],
            ['name' => 'Sunshine Carrot Seeds', 'quantity' => $this->faker->numberBetween(1, 20)],
            ['name' => 'Yellow Pearl Squash Seeds', 'quantity' => $this->faker->numberBetween(1, 8)],
        ];

        $availableSeedlings = [
            ['name' => 'Avocado Seedling', 'quantity' => $this->faker->numberBetween(1, 5)],
            ['name' => 'Calamansi Seedling', 'quantity' => $this->faker->numberBetween(1, 8)],
            ['name' => 'Guava Seedling', 'quantity' => $this->faker->numberBetween(1, 6)],
            ['name' => 'Guyabano Seedling', 'quantity' => $this->faker->numberBetween(1, 4)],
            ['name' => 'Mango Seedling', 'quantity' => $this->faker->numberBetween(1, 3)],
            ['name' => 'Papaya Seedling', 'quantity' => $this->faker->numberBetween(1, 5)],
            ['name' => 'Santol Seedling', 'quantity' => $this->faker->numberBetween(1, 4)],
        ];

        $availableFruits = [
            ['name' => 'Dwarf Coconut Tree', 'quantity' => $this->faker->numberBetween(1, 3)],
            ['name' => 'Lakatan Banana Tree', 'quantity' => $this->faker->numberBetween(1, 4)],
            ['name' => 'Rambutan Tree', 'quantity' => $this->faker->numberBetween(1, 2)],
            ['name' => 'Star Apple Tree', 'quantity' => $this->faker->numberBetween(1, 3)],
        ];

        $availableOrnamentals = [
            ['name' => 'Anthurium', 'quantity' => $this->faker->numberBetween(1, 10)],
            ['name' => 'Bougainvillea', 'quantity' => $this->faker->numberBetween(1, 8)],
            ['name' => 'Fortune Plant', 'quantity' => $this->faker->numberBetween(1, 12)],
            ['name' => 'Gumamela (Hibiscus)', 'quantity' => $this->faker->numberBetween(1, 6)],
            ['name' => 'Sansevieria (Snake Plant)', 'quantity' => $this->faker->numberBetween(1, 15)],
        ];

        $availableFingerlings = [
            ['name' => 'Catfish Fingerling', 'quantity' => $this->faker->numberBetween(50, 200)],
            ['name' => 'Milkfish (Bangus) Fingerling', 'quantity' => $this->faker->numberBetween(100, 300)],
            ['name' => 'Tilapia Fingerlings', 'quantity' => $this->faker->numberBetween(100, 500)],
        ];

        $availableFertilizers = [
            ['name' => 'Ammonium Sulfate (21-0-0)', 'quantity' => $this->faker->numberBetween(1, 5)],
            ['name' => 'Humic Acid', 'quantity' => $this->faker->numberBetween(1, 8)],
            ['name' => 'Pre-processed Chicken Manure', 'quantity' => $this->faker->numberBetween(1, 10)],
            ['name' => 'Urea (46-0-0)', 'quantity' => $this->faker->numberBetween(1, 6)],
            ['name' => 'Vermicast Fertilizer', 'quantity' => $this->faker->numberBetween(1, 12)],
        ];

        $barangays = [
            'Bagong Silang', 'Cuyab', 'Estrella', 'G.S.I.S.', 'Landayan',
            'Langgam', 'Laram', 'Magsaysay', 'Nueva', 'Poblacion',
            'Riverside', 'San Antonio', 'San Roque', 'San Vicente', 'Santo Niño',
            'United Bayanihan', 'United Better Living', 'Sampaguita Village',
            'Calendola', 'Narra', 'Chrysanthemum', 'Fatima', 'Maharlika',
            'Pacita 1', 'Pacita 2', 'Rosario', 'San Lorenzo Ruiz'
        ];

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

        // Randomly select items
        $selectedSeeds = $this->faker->randomElements($availableSeeds, $this->faker->numberBetween(0, 3));
        $selectedSeedlings = $this->faker->randomElements($availableSeedlings, $this->faker->numberBetween(0, 3));
        $selectedFruits = $this->faker->randomElements($availableFruits, $this->faker->numberBetween(0, 2));
        $selectedOrnamentals = $this->faker->randomElements($availableOrnamentals, $this->faker->numberBetween(0, 2));
        $selectedFingerlings = $this->faker->randomElements($availableFingerlings, $this->faker->numberBetween(0, 1));
        $selectedFertilizers = $this->faker->randomElements($availableFertilizers, $this->faker->numberBetween(0, 2));

        // Calculate total
        $totalQuantity = collect($selectedSeeds)->sum('quantity') +
                        collect($selectedSeedlings)->sum('quantity') +
                        collect($selectedFruits)->sum('quantity') +
                        collect($selectedOrnamentals)->sum('quantity') +
                        collect($selectedFingerlings)->sum('quantity') +
                        collect($selectedFertilizers)->sum('quantity');

        $firstName = $this->faker->randomElement($filipinoFirstNames);
        $lastName = $this->faker->randomElement($filipinoLastNames);
        $middleName = $this->faker->optional(0.8)->randomElement($filipinoFirstNames);

        return [
            'user_id' => UserRegistration::inRandomOrder()->first()?->id ?? UserRegistration::factory()->create()->id,
            'request_number' => 'SEED-' . strtoupper(Str::random(8)),
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'extension_name' => $this->faker->optional(0.15)->randomElement(['Jr.', 'Sr.', 'II', 'III', 'IV']),
            'contact_number' => $this->faker->phoneNumber,
            'barangay' => $this->faker->randomElement($barangays),
            'seedling_type' => $this->formatSeedlingTypes($selectedSeeds, $selectedSeedlings, $selectedFruits, $selectedOrnamentals, $selectedFingerlings, $selectedFertilizers),

            // Store items - Laravel will cast to JSON automatically
            'seeds' => empty($selectedSeeds) ? null : $selectedSeeds,
            'seedlings' => empty($selectedSeedlings) ? null : $selectedSeedlings,
            'fruits' => empty($selectedFruits) ? null : $selectedFruits,
            'ornamentals' => empty($selectedOrnamentals) ? null : $selectedOrnamentals,
            'fingerlings' => empty($selectedFingerlings) ? null : $selectedFingerlings,
            'fertilizers' => empty($selectedFertilizers) ? null : $selectedFertilizers,

            'requested_quantity' => $totalQuantity,
            'total_quantity' => $totalQuantity,
            'document_path' => $this->faker->optional(0.3)->filePath(),

            // Status fields - default values
            'status' => 'under_review',
            'seeds_status' => null,
            'seedlings_status' => null,
            'fruits_status' => null,
            'ornamentals_status' => null,
            'fingerlings_status' => null,
            'fertilizers_status' => null,

            // Approved/rejected items - null by default
            'seeds_approved_items' => null,
            'seedlings_approved_items' => null,
            'fruits_approved_items' => null,
            'ornamentals_approved_items' => null,
            'fingerlings_approved_items' => null,
            'fertilizers_approved_items' => null,
            'seeds_rejected_items' => null,
            'seedlings_rejected_items' => null,
            'fruits_rejected_items' => null,
            'ornamentals_rejected_items' => null,
            'fingerlings_rejected_items' => null,
            'fertilizers_rejected_items' => null,

            // Review info - null by default
            'reviewed_by' => null,
            'reviewed_at' => null,
            'remarks' => null,
            'approved_quantity' => null,
            'approved_at' => null,
            'rejected_at' => null,
            'pickup_date' => $this->faker->optional(0.7)->dateTimeBetween('now', '+30 days'),
            'pickup_expired_at' => null, // Will be set when pickup_date is set
            'pickup_reminder_sent' => false,
        ];
    }

    private function formatSeedlingTypes($seeds, $seedlings, $fruits, $ornamentals, $fingerlings, $fertilizers): string
    {
        $types = [];
        if (!empty($seeds)) $types[] = 'Seeds: ' . collect($seeds)->pluck('name')->implode(', ');
        if (!empty($seedlings)) $types[] = 'Seedlings: ' . collect($seedlings)->pluck('name')->implode(', ');
        if (!empty($fruits)) $types[] = 'Fruit Trees: ' . collect($fruits)->pluck('name')->implode(', ');
        if (!empty($ornamentals)) $types[] = 'Ornamentals: ' . collect($ornamentals)->pluck('name')->implode(', ');
        if (!empty($fingerlings)) $types[] = 'Fingerlings: ' . collect($fingerlings)->pluck('name')->implode(', ');
        if (!empty($fertilizers)) $types[] = 'Fertilizers: ' . collect($fertilizers)->pluck('name')->implode(', ');
        return implode(' | ', $types);
    }

    // Factory states
    public function approved(): static
    {
        return $this->state(function (array $attributes) {
            // Get the users or use fallback
            $userId = User::inRandomOrder()->first()?->id ?? 1;

            return [
                'status' => 'approved',
                'seeds_status' => !empty($attributes['seeds']) ? 'approved' : null,
                'seedlings_status' => !empty($attributes['seedlings']) ? 'approved' : null,
                'fruits_status' => !empty($attributes['fruits']) ? 'approved' : null,
                'ornamentals_status' => !empty($attributes['ornamentals']) ? 'approved' : null,
                'fingerlings_status' => !empty($attributes['fingerlings']) ? 'approved' : null,
                'fertilizers_status' => !empty($attributes['fertilizers']) ? 'approved' : null,
                'approved_quantity' => $attributes['total_quantity'],
                'approved_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
                'reviewed_by' => $userId,
                'reviewed_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
                'remarks' => 'Request approved and ready for pickup.',

                // Set approved items
                'seeds_approved_items' => !empty($attributes['seeds']) ? $attributes['seeds'] : null,
                'seedlings_approved_items' => !empty($attributes['seedlings']) ? $attributes['seedlings'] : null,
                'fruits_approved_items' => !empty($attributes['fruits']) ? $attributes['fruits'] : null,
                'ornamentals_approved_items' => !empty($attributes['ornamentals']) ? $attributes['ornamentals'] : null,
                'fingerlings_approved_items' => !empty($attributes['fingerlings']) ? $attributes['fingerlings'] : null,
                'fertilizers_approved_items' => !empty($attributes['fertilizers']) ? $attributes['fertilizers'] : null,
            ];
        });
    }

    public function rejected(): static
    {
        return $this->state(function (array $attributes) {
            $userId = User::inRandomOrder()->first()?->id ?? 1;

            return [
                'status' => 'rejected',
                'seeds_status' => !empty($attributes['seeds']) ? 'rejected' : null,
                'seedlings_status' => !empty($attributes['seedlings']) ? 'rejected' : null,
                'fruits_status' => !empty($attributes['fruits']) ? 'rejected' : null,
                'ornamentals_status' => !empty($attributes['ornamentals']) ? 'rejected' : null,
                'fingerlings_status' => !empty($attributes['fingerlings']) ? 'rejected' : null,
                'fertilizers_status' => !empty($attributes['fertilizers']) ? 'rejected' : null,
                'rejected_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
                'reviewed_by' => $userId,
                'reviewed_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
                'remarks' => $this->faker->randomElement([
                    'Insufficient stock available.',
                    'Invalid documentation provided.',
                    'Outside service area.',
                    'Request exceeds allocation limit.',
                    'Incomplete application form.',
                    'Unable to verify applicant information.'
                ]),

                // Set rejected items
                'seeds_rejected_items' => !empty($attributes['seeds']) ? $attributes['seeds'] : null,
                'seedlings_rejected_items' => !empty($attributes['seedlings']) ? $attributes['seedlings'] : null,
                'fruits_rejected_items' => !empty($attributes['fruits']) ? $attributes['fruits'] : null,
                'ornamentals_rejected_items' => !empty($attributes['ornamentals']) ? $attributes['ornamentals'] : null,
                'fingerlings_rejected_items' => !empty($attributes['fingerlings']) ? $attributes['fingerlings'] : null,
                'fertilizers_rejected_items' => !empty($attributes['fertilizers']) ? $attributes['fertilizers'] : null,
            ];
        });
    }

    public function partiallyApproved(): static
    {
        return $this->state(function (array $attributes) {
            $userId = User::inRandomOrder()->first()?->id ?? 1;
            $statuses = ['approved', 'rejected'];

            return [
                'status' => 'partially_approved',
                'seeds_status' => !empty($attributes['seeds']) ? $this->faker->randomElement($statuses) : null,
                'seedlings_status' => !empty($attributes['seedlings']) ? $this->faker->randomElement($statuses) : null,
                'fruits_status' => !empty($attributes['fruits']) ? $this->faker->randomElement($statuses) : null,
                'ornamentals_status' => !empty($attributes['ornamentals']) ? $this->faker->randomElement($statuses) : null,
                'fingerlings_status' => !empty($attributes['fingerlings']) ? $this->faker->randomElement($statuses) : null,
                'fertilizers_status' => !empty($attributes['fertilizers']) ? $this->faker->randomElement($statuses) : null,
                'reviewed_by' => $userId,
                'reviewed_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
                'remarks' => 'Some items approved, others rejected due to stock limitations.',
            ];
        });
    }

    public function pending(): static
    {
        return $this->state([
            'status' => 'under_review',
            'seeds_status' => null,
            'seedlings_status' => null,
            'fruits_status' => null,
            'ornamentals_status' => null,
            'fingerlings_status' => null,
            'fertilizers_status' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
            'remarks' => null,
        ]);
    }

    public function largeOrder(): static
    {
        return $this->state([
            'total_quantity' => $this->faker->numberBetween(100, 500),
            'requested_quantity' => $this->faker->numberBetween(100, 500),
            'document_path' => 'seedling_documents/' . $this->faker->uuid . '.pdf',
            'purpose' => 'Large scale agricultural project',
        ]);
    }

    public function withDocuments(): static
    {
        return $this->state([
            'document_path' => 'seedling_documents/' . $this->faker->uuid . '.pdf',
        ]);
    }

    public function seedsOnly(): static
    {
        $seeds = [
            ['name' => 'Red Ruby Tomato Seeds', 'quantity' => 15],
            ['name' => 'Green Gem String Bean Seeds', 'quantity' => 12],
            ['name' => 'Sunshine Carrot Seeds', 'quantity' => 20],
        ];

        return $this->state([
            'seeds' => $seeds,
            'seedlings' => null,
            'fruits' => null,
            'ornamentals' => null,
            'fingerlings' => null,
            'fertilizers' => null,
            'total_quantity' => collect($seeds)->sum('quantity'),
            'requested_quantity' => collect($seeds)->sum('quantity'),
        ]);
    }

    public function fingerlingsOnly(): static
    {
        $fingerlings = [['name' => 'Tilapia Fingerlings', 'quantity' => 300]];
        return $this->state([
            'seeds' => null,
            'seedlings' => null,
            'fruits' => null,
            'ornamentals' => null,
            'fingerlings' => $fingerlings,
            'fertilizers' => null,
            'total_quantity' => 300,
            'requested_quantity' => 300,
            'purpose' => 'Fish farming project',
        ]);
    }

    public function ornamentalsOnly(): static
    {
        $ornamentals = [
            ['name' => 'Bougainvillea', 'quantity' => 8],
            ['name' => 'Anthurium', 'quantity' => 10],
            ['name' => 'Gumamela (Hibiscus)', 'quantity' => 6],
        ];

        return $this->state([
            'seeds' => null,
            'seedlings' => null,
            'fruits' => null,
            'ornamentals' => $ornamentals,
            'fingerlings' => null,
            'fertilizers' => null,
            'total_quantity' => collect($ornamentals)->sum('quantity'),
            'requested_quantity' => collect($ornamentals)->sum('quantity'),
            'purpose' => 'Landscape beautification',
        ]);
    }
}
