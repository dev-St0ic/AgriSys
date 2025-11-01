<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['announcement', 'ongoing', 'upcoming', 'past'];
        $category = $this->faker->randomElement($categories);

        $titles = [
            'Community Garden Initiative',
            'Green Corridor Project',
            'Tree Planting Drive',
            'Vegetable Farming Workshop',
            'Organic Rice Cultivation',
            'Park Maintenance Program',
            'Sports Field Renovation',
            'Urban Farming Training',
            'Agricultural Seminar',
            'Crop Disease Management'
        ];

        $locations = [
            'San Pedro Community Garden, Brgy. Riverside',
            'Agriculture Office Training Center',
            'All rice farming areas in San Pedro',
            'All public parks across San Pedro',
            'San Pedro Sports Complex',
            'Various locations citywide',
            '12 Barangays across San Pedro City'
        ];

        $descriptions = [
            'Urban gardening program bringing together residents to cultivate fresh produce while learning sustainable farming techniques.',
            'City-wide landscaping initiative that transformed urban spaces into vibrant green zones.',
            'Annual tree planting event with a goal to plant 1,000 indigenous trees across the city.',
            'Expert-led workshops covering advanced cultivation methods and market strategies.',
            'Year-long program supporting farmers transitioning to organic farming methods.',
            'Daily maintenance of city parks through regular mowing, trimming, and landscaping.',
            'Complete overhaul of community sports facilities with modern amenities.',
            'Monthly training on innovative techniques for growing vegetables in small spaces.',
            'Educational seminar on modern agricultural practices and technologies.',
            'Comprehensive program on identifying and managing crop diseases organically.'
        ];

        return [
            'title' => $this->faker->randomElement($titles),
            'description' => $this->faker->randomElement($descriptions),
            'category' => $category,
            'image_path' => 'events/placeholder-' . $this->faker->randomElement([1, 2, 3, 4, 5, 6, 7, 8]) . '.jpg',
            'date' => $category === 'upcoming' ? $this->faker->dateTimeBetween('+1 month', '+6 months')->format('M d, Y | g:i A') : null,
            'location' => $this->faker->randomElement($locations),
            'details' => [
                'participants' => $this->faker->randomElement(['All residents welcome', 'Local farmers only', 'Families encouraged']),
                'cost' => $this->faker->randomElement(['Free for all participants', 'Paid entry', 'Members free, non-members paid']),
                'requirement' => $this->faker->randomElement(['None', 'Registration required', 'Prior experience needed']),
                'benefits' => $this->faker->randomElement(['Free seedlings', 'Certificate of participation', 'Training materials provided'])
            ],
            'is_active' => $this->faker->boolean(80),
            'display_order' => $this->faker->randomNumber(2),
            'created_by' => User::where('role', 'admin')->inRandomOrder()->first()?->id ?? 1,
            'updated_by' => User::where('role', 'admin')->inRandomOrder()->first()?->id ?? 1,
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }

    /**
     * Indicate that the event is ongoing.
     */
    public function ongoing(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'ongoing',
            'is_active' => true,
            'date' => 'Every Saturday | 8:00 AM - 12:00 PM',
        ]);
    }

    /**
     * Indicate that the event is upcoming.
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'upcoming',
            'is_active' => true,
            'date' => $this->faker->dateTimeBetween('+1 month', '+6 months')->format('M d, Y | g:i A'),
        ]);
    }

    /**
     * Indicate that the event is past.
     */
    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'past',
            'is_active' => true,
            'date' => 'Completed: ' . $this->faker->dateTimeBetween('-12 months', '-1 month')->format('F Y'),
        ]);
    }

    /**
     * Indicate that the event is an announcement.
     */
    public function announcement(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'announcement',
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the event is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}