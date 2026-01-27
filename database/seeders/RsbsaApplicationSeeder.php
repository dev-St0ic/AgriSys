<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RsbsaApplication;
use App\Models\UserRegistration;

class RsbsaApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have users in the user_registration table
        // Check if there are existing users, if not create some
        if (UserRegistration::count() < 10) {
            UserRegistration::factory(20)->create();
        }

        // ==================== PENDING APPLICATIONS ====================
        // 10 pending applications with mixed livelihoods
        RsbsaApplication::factory(5)->pending()->farmer()->create();
        RsbsaApplication::factory(3)->pending()->fisherfolk()->create();
        RsbsaApplication::factory(2)->pending()->agriYouth()->create();

        // ==================== UNDER REVIEW APPLICATIONS ====================
        // 8 under review applications with mixed livelihoods
        RsbsaApplication::factory(3)->underReview()->farmer()->create();
        RsbsaApplication::factory(2)->underReview()->farmworker()->create();
        RsbsaApplication::factory(2)->underReview()->fisherfolk()->create();
        RsbsaApplication::factory(1)->underReview()->agriYouth()->create();

        // ==================== APPROVED APPLICATIONS ====================
        // 15 approved applications with mixed livelihoods
        RsbsaApplication::factory(6)->approved()->farmer()->create();
        RsbsaApplication::factory(4)->approved()->fisherfolk()->create();
        RsbsaApplication::factory(3)->approved()->farmworker()->create();
        RsbsaApplication::factory(2)->approved()->agriYouth()->create();

        // ==================== REJECTED APPLICATIONS ====================
        // 5 rejected applications with mixed livelihoods
        RsbsaApplication::factory(2)->rejected()->farmer()->create();
        RsbsaApplication::factory(2)->rejected()->fisherfolk()->create();
        RsbsaApplication::factory(1)->rejected()->agriYouth()->create();

        // ==================== CREATE APPLICATIONS BY SPECIFIC LIVELIHOOD ====================
        // Create applications specifically for each livelihood type with varied statuses
        
        // FARMERS - Diverse statuses
        RsbsaApplication::factory(5)->create(['main_livelihood' => 'Farmer']);
        
        // FARMWORKERS/LABORERS - Diverse statuses
        RsbsaApplication::factory(4)->create(['main_livelihood' => 'Farmworker/Laborer']);
        
        // FISHERFOLK - Diverse statuses
        RsbsaApplication::factory(3)->create(['main_livelihood' => 'Fisherfolk']);
        
        // AGRI-YOUTH - Diverse statuses
        RsbsaApplication::factory(3)->create(['main_livelihood' => 'Agri-youth']);

        // ==================== CREATE APPLICATIONS BY SPECIFIC BARANGAY ====================
        // Create applications from popular barangays
        $popularBarangays = ['Poblacion', 'San Antonio', 'Bagong Silang', 'Magsaysay', 'Landayan'];
        foreach ($popularBarangays as $barangay) {
            RsbsaApplication::factory(3)->create(['barangay' => $barangay]);
        }

        // ==================== CREATE FARMER-SPECIFIC DATA ====================
        // Create farmers with different crops
        $farmerCrops = ['Rice', 'Corn', 'HVC', 'Livestock', 'Poultry', 'Agri-fishery'];
        foreach ($farmerCrops as $crop) {
            RsbsaApplication::factory(2)->create([
                'main_livelihood' => 'Farmer',
                'farmer_crops' => $crop,
            ]);
        }

        // Create farmers with different farm types
        $farmTypes = ['Irrigated', 'Rainfed Upland', 'Rainfed Lowland'];
        foreach ($farmTypes as $type) {
            RsbsaApplication::factory(2)->create([
                'main_livelihood' => 'Farmer',
                'farmer_type_of_farm' => $type,
            ]);
        }

        // Create farmers with different land ownership
        $landOwnerships = ['Owner', 'Tenant', 'Lessee'];
        foreach ($landOwnerships as $ownership) {
            RsbsaApplication::factory(2)->create([
                'main_livelihood' => 'Farmer',
                'farmer_land_ownership' => $ownership,
            ]);
        }

        // ==================== CREATE FARMWORKER-SPECIFIC DATA ====================
        // Create farmworkers with different work types
        $workTypes = ['Land preparation', 'Planting/Transplanting', 'Cultivation', 'Harvesting'];
        foreach ($workTypes as $type) {
            RsbsaApplication::factory(2)->create([
                'main_livelihood' => 'Farmworker/Laborer',
                'farmworker_type' => $type,
            ]);
        }

        // ==================== CREATE FISHERFOLK-SPECIFIC DATA ====================
        // Create fisherfolk with different activities
        $fishingActivities = ['Fish capture', 'Aquaculture', 'Gleaning', 'Processing', 'Vending'];
        foreach ($fishingActivities as $activity) {
            RsbsaApplication::factory(2)->create([
                'main_livelihood' => 'Fisherfolk',
                'fisherfolk_activity' => $activity,
            ]);
        }

        // ==================== CREATE AGRI-YOUTH-SPECIFIC DATA ====================
        // Create agri-youth with different training levels
        $trainings = ['Formal agri-fishery course', 'Non-formal agri-fishery course', 'None'];
        foreach ($trainings as $training) {
            RsbsaApplication::factory(2)->create([
                'main_livelihood' => 'Agri-youth',
                'agriyouth_training' => $training,
            ]);
        }

        // Create agri-youth with different household statuses
        $householdStatuses = ['Yes', 'No'];
        foreach ($householdStatuses as $status) {
            RsbsaApplication::factory(2)->create([
                'main_livelihood' => 'Agri-youth',
                'agriyouth_farming_household' => $status,
            ]);
        }

        // ==================== CREATE SPECIAL CASES ====================
        
        // Farmer with "Other Crops" specified
        RsbsaApplication::factory(3)->create([
            'main_livelihood' => 'Farmer',
            'farmer_crops' => 'Other Crops',
            'farmer_other_crops' => 'Vegetables and Root Crops',
        ]);

        // Farmer with livestock and land area
        RsbsaApplication::factory(3)->create([
            'main_livelihood' => 'Farmer',
            'farmer_crops' => 'Rice',
            'farmer_livestock' => 'Chickens (50), Pigs (5)',
            'farmer_land_area' => 2.5,
        ]);

        // Farmer with special status
        RsbsaApplication::factory(2)->create([
            'main_livelihood' => 'Farmer',
            'farmer_special_status' => 'Agrarian Reform Beneficiary',
        ]);

        RsbsaApplication::factory(2)->create([
            'main_livelihood' => 'Farmer',
            'farmer_special_status' => 'Ancestral Domain',
        ]);

        // Farmworker with "Others" specified
        RsbsaApplication::factory(2)->create([
            'main_livelihood' => 'Farmworker/Laborer',
            'farmworker_type' => 'Others',
            'farmworker_other_type' => 'Irrigation System Maintenance',
        ]);

        // Fisherfolk with "Others" specified
        RsbsaApplication::factory(2)->create([
            'main_livelihood' => 'Fisherfolk',
            'fisherfolk_activity' => 'Others',
            'fisherfolk_other_activity' => 'Fish Drying and Smoking',
        ]);

        // Agri-youth with participation
        RsbsaApplication::factory(2)->create([
            'main_livelihood' => 'Agri-youth',
            'agriyouth_participation' => 'Participated',
        ]);

        RsbsaApplication::factory(2)->create([
            'main_livelihood' => 'Agri-youth',
            'agriyouth_participation' => 'Not Participated',
        ]);

        // ==================== CREATE APPROVED APPLICATIONS WITH FULL DATA ====================
        // Create some complete, approved applications for admin dashboard display
        
        // Approved Farmer with all fields
        RsbsaApplication::factory(1)->approved()->create([
            'main_livelihood' => 'Farmer',
            'first_name' => 'Juan',
            'last_name' => 'Santos',
            'contact_number' => '09501234567',
            'barangay' => 'Poblacion',
            'address' => '123 Farmers Lane, Poblacion, San Pedro',
            'farmer_crops' => 'Rice',
            'farmer_land_area' => 3.5,
            'farmer_type_of_farm' => 'Irrigated',
            'farmer_land_ownership' => 'Owner',
            'farm_location' => 'Barangay Landayan, San Pedro',
            'farmer_livestock' => 'Chickens (100)',
            'commodity' => 'Rice and Corn',
        ]);

        // Approved Fisherfolk with all fields
        RsbsaApplication::factory(1)->approved()->create([
            'main_livelihood' => 'Fisherfolk',
            'first_name' => 'Maria',
            'last_name' => 'Cruz',
            'contact_number' => '09601234567',
            'barangay' => 'Riverside',
            'address' => '456 Fishing Village Road, Riverside, San Pedro',
            'fisherfolk_activity' => 'Aquaculture',
            'commodity' => 'Tilapia and Bangus',
        ]);

        // Approved Farmworker with all fields
        RsbsaApplication::factory(1)->approved()->create([
            'main_livelihood' => 'Farmworker/Laborer',
            'first_name' => 'Pedro',
            'last_name' => 'Reyes',
            'contact_number' => '09701234567',
            'barangay' => 'San Antonio',
            'address' => '789 Farm Workers Avenue, San Antonio, San Pedro',
            'farmworker_type' => 'Harvesting',
        ]);

        // Approved Agri-youth with all fields
        RsbsaApplication::factory(1)->approved()->create([
            'main_livelihood' => 'Agri-youth',
            'first_name' => 'Angela',
            'last_name' => 'Mendoza',
            'contact_number' => '09801234567',
            'barangay' => 'Bagong Silang',
            'address' => '321 Youth Farm Project, Bagong Silang, San Pedro',
            'agriyouth_farming_household' => 'Yes',
            'agriyouth_training' => 'Formal agri-fishery course',
            'agriyouth_participation' => 'Participated',
        ]);

        // ==================== CREATE REJECTED APPLICATIONS WITH REMARKS ====================
        
        RsbsaApplication::factory(1)->rejected()->create([
            'main_livelihood' => 'Farmer',
            'first_name' => 'Carlos',
            'last_name' => 'Flores',
            'contact_number' => '09901234567',
            'barangay' => 'Magsaysay',
            'address' => '555 Incomplete Farm Road, Magsaysay, San Pedro',
            'remarks' => 'Incomplete supporting documents. Missing farm location proof.',
        ]);

        RsbsaApplication::factory(1)->rejected()->create([
            'main_livelihood' => 'Fisherfolk',
            'first_name' => 'Rosa',
            'last_name' => 'Torres',
            'contact_number' => '09111234567',
            'barangay' => 'Laram',
            'address' => '777 Fishing Area, Laram, San Pedro',
            'remarks' => 'Unable to verify fishing location. Barangay certification not provided.',
        ]);

        echo "✅ RSBSA Applications seeder completed!\n";
        echo "📊 Total applications created: " . RsbsaApplication::count() . "\n";
        echo "📈 By Status:\n";
        echo "   - Pending: " . RsbsaApplication::where('status', 'pending')->count() . "\n";
        echo "   - Under Review: " . RsbsaApplication::where('status', 'under_review')->count() . "\n";
        echo "   - Approved: " . RsbsaApplication::where('status', 'approved')->count() . "\n";
        echo "   - Rejected: " . RsbsaApplication::where('status', 'rejected')->count() . "\n";
        echo "🏭 By Livelihood:\n";
        echo "   - Farmer: " . RsbsaApplication::where('main_livelihood', 'Farmer')->count() . "\n";
        echo "   - Farmworker/Laborer: " . RsbsaApplication::where('main_livelihood', 'Farmworker/Laborer')->count() . "\n";
        echo "   - Fisherfolk: " . RsbsaApplication::where('main_livelihood', 'Fisherfolk')->count() . "\n";
        echo "   - Agri-youth: " . RsbsaApplication::where('main_livelihood', 'Agri-youth')->count() . "\n";
    }
}