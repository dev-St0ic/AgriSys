<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * EventFactory - Updated for only inactive random events
 * 
 * Key: All factory-generated events are INACTIVE by default
 * This allows seeder to control which events are active
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    /**
     * Define the model's default state.
     * DEFAULT: Events are INACTIVE
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
            'Crop Disease Management',
            'Farmers Market Launch',
            'Soil Conservation Program',
            'Irrigation System Upgrade',
            'Pest Management Workshop',
            'Harvest Festival Event',
            'Aquaculture Training Program',
            'Composting Workshop Series',
            'Fruit Tree Grafting Demo',
            'Community Beekeeping Project',
            'Water Management Seminar'
        ];

        $locations = [
            'San Pedro Community Garden, Brgy. Riverside',
            'Agriculture Office Training Center',
            'All rice farming areas in San Pedro',
            'All public parks across San Pedro',
            'San Pedro Sports Complex',
            'Various locations citywide',
            'Bagong Silang Agricultural Area',
            'Riverside Farming District',
            'City Hall Grounds',
            'Brgy. Poblacion Community Center',
            'San Pedro Municipal Hall',
            'Landayan Farming Village',
            'Estrella Agricultural Zone',
            'Nueva Community Park'
        ];

        $descriptions = [
            'Urban gardening program bringing together residents to cultivate fresh produce while learning sustainable farming techniques.',
            'City-wide landscaping initiative that transformed urban spaces into vibrant green zones.',
            'Annual tree planting event with ambitious goals to plant indigenous trees.',
            'Expert-led workshops covering advanced cultivation methods and pest management.',
            'Year-long program supporting farmers in transitioning to organic farming.',
            'Daily maintenance operations ensuring safe, clean recreational spaces.',
            'Complete facility overhaul with modern infrastructure and equipment.',
            'Monthly training series on innovative techniques for growing vegetables.',
            'Educational seminar on contemporary agricultural practices.',
            'Comprehensive program on identifying and managing crop diseases.',
            'Community marketplace promoting local farmers and products.',
            'Technical training on soil health and sustainable management.',
            'Modernization project improving water efficiency.',
            'Workshop on integrated pest management and sustainable farming.',
            'Community celebration showcasing agricultural achievements.'
        ];

        $date = $this->getDateForCategory($category);

        return [
            'title' => $this->faker->unique()->randomElement($titles),
            'description' => $this->faker->randomElement($descriptions),
            'short_description' => $this->faker->sentence(10),
            'category' => $category,
            'category_label' => ucfirst($category),
            'image_path' => 'events/placeholder-' . $this->faker->randomElement([1, 2, 3, 4, 5, 6, 7, 8]) . '.jpg',
            'image_alt_text' => $this->faker->sentence(6),
            'date' => $date,
            'location' => $this->faker->randomElement($locations),
            'details' => [
                'participants' => 'Community members',
                'cost' => 'Free',
                'requirement' => 'None',
                'contact' => '(049) 123-4567',
            ],
            'is_active' => false,
            'is_featured' => false,
            'display_order' => $this->faker->numberBetween(9, 100),
            'created_by' => User::where('role', 'admin')->inRandomOrder()->first()?->id ?? 1,
            'updated_by' => User::where('role', 'admin')->inRandomOrder()->first()?->id ?? 1,
            'published_at' => now(),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }

    private function getDateForCategory(string $category): ?string
    {
        return match ($category) {
            'upcoming' => $this->faker->dateTimeBetween('+1 month', '+6 months')->format('M d, Y | g:i A'),
            'ongoing' => $this->faker->randomElement([
                'Every Saturday | 8:00 AM - 12:00 PM',
                'Every Sunday | 6:00 AM - 10:00 AM',
                'Every Monday - Friday | 2:00 PM - 5:00 PM',
                'Weekly | 9:00 AM - 12:00 PM',
            ]),
            'past' => 'Completed: ' . $this->faker->dateTimeBetween('-12 months', '-1 month')->format('F Y'),
            'announcement' => null,
            default => null,
        };
    }

    public function ongoing(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'ongoing',
            'category_label' => 'Ongoing',
            'date' => 'Every Saturday | 8:00 AM - 12:00 PM',
        ]);
    }

    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'upcoming',
            'category_label' => 'Upcoming',
            'date' => $this->faker->dateTimeBetween('+1 month', '+6 months')->format('M d, Y | g:i A'),
        ]);
    }

    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'past',
            'category_label' => 'Past',
            'date' => 'Completed: ' . $this->faker->dateTimeBetween('-12 months', '-1 month')->format('F Y'),
        ]);
    }

    public function announcement(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'announcement',
            'category_label' => 'Announcement',
            'date' => null,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'published_at' => now(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'display_order' => $this->faker->numberBetween(1, 3),
        ]);
    }

    public function inCategory(string $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => $category,
            'category_label' => ucfirst($category),
        ]);
    }

    public function withDisplayOrder(int $min, int $max): static
    {
        return $this->state(fn (array $attributes) => [
            'display_order' => $this->faker->numberBetween($min, $max),
        ]);
    }
}