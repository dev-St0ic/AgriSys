<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SeedlingRequest;
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
        ];

        $fruits = [
            ['name' => 'kalamansi', 'quantity' => $this->faker->numberBetween(1, 5)],
            ['name' => 'mangga', 'quantity' => $this->faker->numberBetween(1, 3)],
        ];

        $fertilizers = [
            ['name' => 'chicken manure', 'quantity' => $this->faker->numberBetween(1, 10)],
            ['name' => 'vermicast', 'quantity' => $this->faker->numberBetween(1, 5)],
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

        return [
            'request_number' => 'SEED-' . strtoupper(Str::random(8)),
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->optional(0.7)->firstName,
            'last_name' => $this->faker->lastName,
            'extension_name' => $this->faker->optional(0.2)->suffix,
            'contact_number' => $this->faker->phoneNumber,
            'address' => $this->faker->streetAddress,
            'barangay' => $this->faker->randomElement($barangays),
            'planting_location' => $this->faker->optional(0.8)->address,
            'purpose' => $this->faker->optional(0.9)->sentence,
            'seedling_type' => $this->formatSeedlingTypes($selectedVegetables, $selectedFruits, $selectedFertilizers),
            'vegetables' => $selectedVegetables,
            'fruits' => $selectedFruits,
            'fertilizers' => $selectedFertilizers,
            'requested_quantity' => $totalQuantity,
            'total_quantity' => $totalQuantity,
            'preferred_delivery_date' => $this->faker->optional(0.8)->dateTimeBetween('now', '+30 days'),
            'document_path' => $this->faker->optional(0.3)->filePath(),
            'status' => $this->faker->randomElement(['approved', 'rejected', 'under_review']),
            'reviewed_by' => null,
            'reviewed_at' => null,
            'remarks' => $this->faker->optional(0.4)->sentence,
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
     * Create an approved request
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approved_quantity' => $attributes['total_quantity'],
            'approved_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'reviewed_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'remarks' => 'Request approved and ready for pickup.',
        ]);
    }

    /**
     * Create a rejected request
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'rejected_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'reviewed_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'remarks' => $this->faker->randomElement([
                'Insufficient stock available.',
                'Invalid documentation provided.',
                'Outside service area.',
                'Request exceeds allocation limit.'
            ]),
        ]);
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
        ]);
    }
}