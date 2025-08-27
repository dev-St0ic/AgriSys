<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SeedlingRequest;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SeedlingRequest>
 */
class SeedlingRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SeedlingRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $vegetables = [
            ['name' => 'sampaguita', 'quantity' => $this->faker->numberBetween(1, 20)],
            ['name' => 'siling haba', 'quantity' => $this->faker->numberBetween(1, 15)],
            ['name' => 'eggplant', 'quantity' => $this->faker->numberBetween(1, 10)],
            ['name' => 'tomato', 'quantity' => $this->faker->numberBetween(1, 25)],
            ['name' => 'okra', 'quantity' => $this->faker->numberBetween(1, 15)],
            ['name' => 'kangkong', 'quantity' => $this->faker->numberBetween(1, 30)],
        ];

        $fruits = [
            ['name' => 'kalamansi', 'quantity' => $this->faker->numberBetween(1, 5)],
            ['name' => 'mangga', 'quantity' => $this->faker->numberBetween(1, 3)],
            ['name' => 'avocado', 'quantity' => $this->faker->numberBetween(1, 2)],
            ['name' => 'papaya', 'quantity' => $this->faker->numberBetween(1, 4)],
            ['name' => 'guava', 'quantity' => $this->faker->numberBetween(1, 5)],
        ];

        $fertilizers = [
            ['name' => 'chicken manure', 'quantity' => $this->faker->numberBetween(1, 10)],
            ['name' => 'vermicast', 'quantity' => $this->faker->numberBetween(1, 5)],
            ['name' => 'organic compost', 'quantity' => $this->faker->numberBetween(1, 8)],
            ['name' => 'rice hull', 'quantity' => $this->faker->numberBetween(1, 15)],
        ];

        $barangays = [
            'Bagong Silang', 'Calendola', 'Chrysanthemum', 'Cuyab', 'Fatima',
            'G.S.I.S.', 'Landayan', 'Laram', 'Magsaysay', 'Maharlika',
            'Narra', 'Nueva', 'Pacita 1', 'Pacita 2', 'Poblacion',
            'Rosario', 'Riverside', 'Sampaguita Village', 'San Antonio',
            'San Lorenzo Ruiz', 'San Roque', 'San Vicente',
            'United Bayanihan', 'United Better Living'
        ];

        // Randomly select some items
        $selectedVegetables = $this->faker->randomElements($vegetables, $this->faker->numberBetween(0, 3));
        $selectedFruits = $this->faker->randomElements($fruits, $this->faker->numberBetween(0, 2));
        $selectedFertilizers = $this->faker->randomElements($fertilizers, $this->faker->numberBetween(0, 2));

        // Calculate total quantity
        $totalQuantity = collect($selectedVegetables)->sum('quantity') +
                        collect($selectedFruits)->sum('quantity') +
                        collect($selectedFertilizers)->sum('quantity');

        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;

        // Generate status for categories
        $statuses = ['approved', 'rejected', 'under_review', 'partially_approved'];
        $vegetablesStatus = !empty($selectedVegetables) ? $this->faker->randomElement($statuses) : null;
        $fruitsStatus = !empty($selectedFruits) ? $this->faker->randomElement($statuses) : null;
        $fertilizersStatus = !empty($selectedFertilizers) ? $this->faker->randomElement($statuses) : null;

        // Generate approved items for approved categories
        $vegetablesApprovedItems = $vegetablesStatus === 'approved' ? $selectedVegetables : null;
        $fruitsApprovedItems = $fruitsStatus === 'approved' ? $selectedFruits : null;
        $fertilizersApprovedItems = $fertilizersStatus === 'approved' ? $selectedFertilizers : null;

        return [
            'request_number' => 'SEED-' . strtoupper(Str::random(8)),
            'first_name' => $firstName,
            'middle_name' => $this->faker->optional(0.7)->firstName,
            'last_name' => $lastName,
            'extension_name' => $this->faker->optional(0.2)->randomElement(['Jr.', 'Sr.', 'II', 'III']),
            'contact_number' => $this->faker->phoneNumber,
            'email' => $this->faker->optional(0.8)->safeEmail ?? strtolower($firstName . '.' . $lastName . '@example.com'),
            'address' => $this->faker->streetAddress,
            'barangay' => $this->faker->randomElement($barangays),
            'planting_location' => $this->faker->optional(0.8)->address,
            'purpose' => $this->faker->optional(0.9)->randomElement([
                'Backyard gardening',
                'Community garden project',
                'School garden',
                'Livelihood project',
                'Food security',
                'Educational purposes'
            ]),
            'seedling_type' => $this->formatSeedlingTypes($selectedVegetables, $selectedFruits, $selectedFertilizers),
            'vegetables' => $selectedVegetables,
            'fruits' => $selectedFruits,
            'fertilizers' => $selectedFertilizers,
            'requested_quantity' => $totalQuantity,
            'total_quantity' => $totalQuantity,
            'preferred_delivery_date' => $this->faker->optional(0.8)->dateTimeBetween('now', '+30 days'),
            'document_path' => $this->faker->optional(0.3)->filePath(),

            // Status fields
            'status' => $this->faker->randomElement(['approved', 'rejected', 'under_review', 'partially_approved']),
            'vegetables_status' => $vegetablesStatus,
            'fruits_status' => $fruitsStatus,
            'fertilizers_status' => $fertilizersStatus,

            // Approved items
            'vegetables_approved_items' => $vegetablesApprovedItems,
            'fruits_approved_items' => $fruitsApprovedItems,
            'fertilizers_approved_items' => $fertilizersApprovedItems,

            // Review information
            'reviewed_by' => $this->faker->optional(0.6)->randomElement(User::pluck('id')->toArray() ?: [1]),
            'reviewed_at' => $this->faker->optional(0.6)->dateTimeBetween('-30 days', 'now'),
            'remarks' => $this->faker->optional(0.4)->sentence(10),
            'approved_quantity' => null,
            'approved_at' => null,
            'rejected_at' => null,
        ];
    }

    /**
     * Format seedling types for display
     */
    private function formatSeedlingTypes($vegetables, $fruits, $fertilizers): string
    {
        $types = [];

        if (!empty($vegetables)) {
            $vegNames = collect($vegetables)->pluck('name')->toArray();
            $types[] = 'Vegetables: ' . implode(', ', $vegNames);
        }

        if (!empty($fruits)) {
            $fruitNames = collect($fruits)->pluck('name')->toArray();
            $types[] = 'Fruits: ' . implode(', ', $fruitNames);
        }

        if (!empty($fertilizers)) {
            $fertNames = collect($fertilizers)->pluck('name')->toArray();
            $types[] = 'Fertilizers: ' . implode(', ', $fertNames);
        }

        return implode(' | ', $types);
    }

    /**
     * Create a fully approved request
     */
    public function approved(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved',
                'vegetables_status' => !empty($attributes['vegetables']) ? 'approved' : null,
                'fruits_status' => !empty($attributes['fruits']) ? 'approved' : null,
                'fertilizers_status' => !empty($attributes['fertilizers']) ? 'approved' : null,
                'vegetables_approved_items' => $attributes['vegetables'] ?? null,
                'fruits_approved_items' => $attributes['fruits'] ?? null,
                'fertilizers_approved_items' => $attributes['fertilizers'] ?? null,
                'approved_quantity' => $attributes['total_quantity'],
                'approved_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
                'reviewed_by' => User::inRandomOrder()->first()?->id ?? 1,
                'reviewed_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
                'remarks' => 'Request approved and ready for pickup.',
            ];
        });
    }

    /**
     * Create a rejected request
     */
    public function rejected(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'rejected',
                'vegetables_status' => !empty($attributes['vegetables']) ? 'rejected' : null,
                'fruits_status' => !empty($attributes['fruits']) ? 'rejected' : null,
                'fertilizers_status' => !empty($attributes['fertilizers']) ? 'rejected' : null,
                'vegetables_approved_items' => null,
                'fruits_approved_items' => null,
                'fertilizers_approved_items' => null,
                'rejected_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
                'reviewed_by' => User::inRandomOrder()->first()?->id ?? 1,
                'reviewed_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
                'remarks' => $this->faker->randomElement([
                    'Insufficient stock available.',
                    'Invalid documentation provided.',
                    'Outside service area.',
                    'Request exceeds allocation limit.',
                    'Incomplete application form.',
                    'Unable to verify applicant information.'
                ]),
            ];
        });
    }

    /**
     * Create a partially approved request
     */
    public function partiallyApproved(): static
    {
        return $this->state(function (array $attributes) {
            $statuses = ['approved', 'rejected'];

            return [
                'status' => 'partially_approved',
                'vegetables_status' => !empty($attributes['vegetables']) ? $this->faker->randomElement($statuses) : null,
                'fruits_status' => !empty($attributes['fruits']) ? $this->faker->randomElement($statuses) : null,
                'fertilizers_status' => !empty($attributes['fertilizers']) ? $this->faker->randomElement($statuses) : null,
                'vegetables_approved_items' => !empty($attributes['vegetables']) && $this->faker->boolean() ? $attributes['vegetables'] : null,
                'fruits_approved_items' => !empty($attributes['fruits']) && $this->faker->boolean() ? $attributes['fruits'] : null,
                'fertilizers_approved_items' => !empty($attributes['fertilizers']) && $this->faker->boolean() ? $attributes['fertilizers'] : null,
                'reviewed_by' => User::inRandomOrder()->first()?->id ?? 1,
                'reviewed_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
                'remarks' => 'Some items approved, others rejected due to stock limitations.',
            ];
        });
    }

    /**
     * Create a pending/under review request
     */
    public function pending(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'under_review',
                'vegetables_status' => null,
                'fruits_status' => null,
                'fertilizers_status' => null,
                'vegetables_approved_items' => null,
                'fruits_approved_items' => null,
                'fertilizers_approved_items' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
                'remarks' => null,
            ];
        });
    }

    /**
     * Create a large quantity request (100+ items)
     */
    public function largeOrder(): static
    {
        return $this->state(fn (array $attributes) => [
            'total_quantity' => $this->faker->numberBetween(100, 500),
            'requested_quantity' => $this->faker->numberBetween(100, 500),
            'document_path' => 'seedling_documents/' . $this->faker->uuid . '.pdf',
            'purpose' => 'Large scale agricultural project',
        ]);
    }

    /**
     * Create request with documents
     */
    public function withDocuments(): static
    {
        return $this->state(fn (array $attributes) => [
            'document_path' => 'seedling_documents/' . $this->faker->uuid . '.pdf',
        ]);
    }
}
