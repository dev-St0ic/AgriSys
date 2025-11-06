<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * EventFactory
 * 
 * Factory for generating random Event instances for testing and seeding
 * Creates realistic agricultural events with varied data
 * 
 * Used by EventSeeder to create 4 random events
 * Can also be used in tests and manual seeding
 * 
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Event::class;

    /**
     * Define the model's default state.
     * 
     * Generates random but realistic event data for agricultural events
     * Ensures data variety while maintaining coherence
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Available categories for agricultural events
        $categories = ['announcement', 'ongoing', 'upcoming', 'past'];
        $category = $this->faker->randomElement($categories);

        // ========================================
        // AGRICULTURAL EVENT TITLES
        // ========================================
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

        // ========================================
        // REALISTIC LOCATIONS (San Pedro City)
        // ========================================
        $locations = [
            'San Pedro Community Garden, Brgy. Riverside',
            'Agriculture Office Training Center',
            'All rice farming areas in San Pedro',
            'All public parks across San Pedro',
            'San Pedro Sports Complex',
            'Various locations citywide',
            '12 Barangays across San Pedro City',
            'Bagong Silang Agricultural Area',
            'Riverside Farming District',
            'City Hall Grounds',
            'Brgy. Poblacion Community Center',
            'San Pedro Municipal Hall',
            'Landayan Farming Village',
            'Estrella Agricultural Zone',
            'Nueva Community Park'
        ];

        // ========================================
        // REALISTIC EVENT DESCRIPTIONS
        // ========================================
        $descriptions = [
            'Urban gardening program bringing together residents to cultivate fresh produce while learning sustainable farming techniques and building community connections.',
            'City-wide landscaping initiative that transformed urban spaces into vibrant green zones, improving air quality and community aesthetics.',
            'Annual tree planting event with ambitious goals to plant indigenous trees and promote environmental conservation across the city.',
            'Expert-led workshops covering advanced cultivation methods, pest management strategies, and market linkage for local farmers.',
            'Year-long program supporting farmers in transitioning to organic farming methods with training and resource support.',
            'Daily maintenance operations ensuring safe, clean, and beautiful recreational spaces for community members.',
            'Complete facility overhaul including modern infrastructure, equipment, and accessibility improvements.',
            'Monthly training series on innovative techniques for growing vegetables in limited spaces using modern methods.',
            'Educational seminar on contemporary agricultural practices, technologies, and sustainability principles.',
            'Comprehensive program on identifying crop diseases and implementing organic management solutions.',
            'Community marketplace promoting local farmers and agricultural products with fair-trade principles.',
            'Technical training focusing on soil health, conservation practices, and sustainable land management.',
            'Modernization project improving water efficiency and agricultural productivity.',
            'Workshop on integrated pest management and sustainable farming without chemical inputs.',
            'Community celebration showcasing agricultural achievements, local produce, and farmer recognition.'
        ];

        // ========================================
        // PARTICIPANT TYPES
        // ========================================
        $participants = [
            'All residents welcome',
            'Local farmers only',
            'Families encouraged',
            'Community members encouraged',
            'Open to all barangay residents',
            'No experience necessary',
            'Experienced farmers preferred',
            'Youth and students welcome',
            'Senior citizens welcome'
        ];

        // ========================================
        // COST STRUCTURES
        // ========================================
        $costs = [
            'Free for all participants',
            'Minimal registration fee',
            'Paid entry (per head)',
            'Members free, non-members P50',
            'Completely free',
            'Nominal fee for materials'
        ];

        // ========================================
        // REQUIREMENTS
        // ========================================
        $requirements = [
            'None - all welcome',
            'Registration required',
            'Prior experience recommended',
            'Bring own tools',
            'Work boots required',
            'Early registration preferred',
            'Valid ID required',
            'Proof of residency required'
        ];

        // ========================================
        // BENEFITS PROVIDED
        // ========================================
        $benefits = [
            'Free seedlings',
            'Certificate of participation',
            'Training materials provided',
            'Refreshments included',
            'Tools and equipment provided',
            'Lunch provided',
            'Transportation assistance',
            'Post-event follow-up support'
        ];

        // Get category label (formatted)
        $categoryLabel = match($category) {
            'announcement' => 'Announcement',
            'ongoing' => 'Ongoing',
            'upcoming' => 'Upcoming',
            'past' => 'Past',
            default => ucfirst($category)
        };

        // ========================================
        // GENERATE DATE BASED ON CATEGORY
        // ========================================
        $date = $this->getDateForCategory($category);

        return [
            // ===== BASIC INFORMATION =====
            'title' => $this->faker->unique()->randomElement($titles),
            'short_description' => $this->faker->sentence(10),
            'description' => $this->faker->randomElement($descriptions),
            
            // ===== CATEGORIZATION =====
            'category' => $category,
            'category_label' => $categoryLabel,
            
            // ===== MEDIA & VISUAL =====
            'image_path' => 'events/placeholder-' . $this->faker->randomElement([1, 2, 3, 4, 5, 6, 7, 8]) . '.jpg',
            'image_alt_text' => $this->faker->sentence(6),
            
            // ===== EVENT DETAILS =====
            'date' => $date,
            'location' => $this->faker->randomElement($locations),
            
            // ===== FLEXIBLE DETAILS (JSON) =====
            'details' => [
                'participants' => $this->faker->randomElement($participants),
                'cost' => $this->faker->randomElement($costs),
                'requirement' => $this->faker->randomElement($requirements),
                'benefits' => $this->faker->randomElement($benefits),
                'contact' => '(049) 123-4567',
                'registration' => $this->faker->boolean(70) ? 'Online or walk-in' : 'Walk-in only'
            ],
            
            // ===== STATUS & DISPLAY =====
            'is_active' => $this->faker->boolean(85), // 85% chance of being active
            'is_featured' => $this->faker->boolean(10), // 10% chance of being featured
            'display_order' => $this->faker->numberBetween(9, 100), // Orders 9+ (after predefined)
            
            // ===== USER TRACKING =====
            'created_by' => User::where('role', 'admin')->inRandomOrder()->first()?->id ?? 1,
            'updated_by' => User::where('role', 'admin')->inRandomOrder()->first()?->id ?? 1,
            'published_at' => $this->faker->boolean(90) ? now() : null,
            
            // ===== TIMESTAMPS =====
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }

    /**
     * Get appropriate date format based on event category
     * 
     * Generates realistic date strings matching the category type
     * 
     * @param string $category
     * @return string|null
     */
    private function getDateForCategory(string $category): ?string
    {
        return match ($category) {
            // Upcoming events: future dates
            'upcoming' => $this->faker->dateTimeBetween('+1 month', '+6 months')->format('M d, Y | g:i A'),
            
            // Ongoing events: recurring schedule
            'ongoing' => $this->faker->randomElement([
                'Every Saturday | 8:00 AM - 12:00 PM',
                'Every Sunday | 6:00 AM - 10:00 AM',
                'Every Monday - Friday | 2:00 PM - 5:00 PM',
                'Weekly | 9:00 AM - 12:00 PM',
                'Bi-weekly | Varies',
                'Ongoing | Daily Operations',
                'Ongoing | Throughout the year'
            ]),
            
            // Past events: completed dates
            'past' => 'Completed: ' . $this->faker->dateTimeBetween('-12 months', '-1 month')->format('F Y'),
            
            // Announcements: no specific date
            'announcement' => null,
            
            default => null,
        };
    }

    // ========================================
    // FACTORY STATES (Methods for specific event types)
    // ========================================

    /**
     * Create an ongoing event
     * 
     * Usage: Event::factory()->ongoing()->create()
     * 
     * @return static
     */
    public function ongoing(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'ongoing',
            'category_label' => 'Ongoing',
            'is_active' => true,
            'date' => 'Every Saturday | 8:00 AM - 12:00 PM',
            'display_order' => $this->faker->numberBetween(9, 50),
        ]);
    }

    /**
     * Create an upcoming event
     * 
     * Usage: Event::factory()->upcoming()->create()
     * 
     * @return static
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'upcoming',
            'category_label' => 'Upcoming',
            'is_active' => true,
            'date' => $this->faker->dateTimeBetween('+1 month', '+6 months')->format('M d, Y | g:i A'),
            'display_order' => $this->faker->numberBetween(9, 50),
        ]);
    }

    /**
     * Create a past event
     * 
     * Usage: Event::factory()->past()->create()
     * 
     * @return static
     */
    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'past',
            'category_label' => 'Past',
            'is_active' => true,
            'date' => 'Completed: ' . $this->faker->dateTimeBetween('-12 months', '-1 month')->format('F Y'),
            'display_order' => $this->faker->numberBetween(9, 50),
        ]);
    }

    /**
     * Create an announcement
     * 
     * Usage: Event::factory()->announcement()->create()
     * 
     * @return static
     */
    public function announcement(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'announcement',
            'category_label' => 'Announcement',
            'is_active' => true,
            'date' => null,
            'display_order' => $this->faker->numberBetween(9, 50),
        ]);
    }

    /**
     * Create an inactive event
     * 
     * Usage: Event::factory()->inactive()->create()
     * 
     * @return static
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create an active event
     * 
     * Usage: Event::factory()->active()->create()
     * 
     * @return static
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'published_at' => now(),
        ]);
    }

    /**
     * Create a featured event
     * 
     * Usage: Event::factory()->featured()->create()
     * 
     * @return static
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'display_order' => $this->faker->numberBetween(1, 3),
        ]);
    }

    /**
     * Create multiple events of a specific category
     * 
     * Usage: Event::factory()->count(5)->inCategory('upcoming')->create()
     * 
     * @param string $category
     * @return static
     */
    public function inCategory(string $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => $category,
            'category_label' => ucfirst($category),
        ]);
    }

    /**
     * Create events with specific display order range
     * 
     * Usage: Event::factory()->count(5)->withDisplayOrder(10, 20)->create()
     * 
     * @param int $min
     * @param int $max
     * @return static
     */
    public function withDisplayOrder(int $min, int $max): static
    {
        return $this->state(fn (array $attributes) => [
            'display_order' => $this->faker->numberBetween($min, $max),
        ]);
    }

    /**
     * Create a published event with publication date
     * 
     * Usage: Event::factory()->published()->create()
     * 
     * @return static
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'published_at' => now(),
        ]);
    }

    /**
     * Create an unpublished event
     * 
     * Usage: Event::factory()->unpublished()->create()
     * 
     * @return static
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'published_at' => null,
        ]);
    }
}