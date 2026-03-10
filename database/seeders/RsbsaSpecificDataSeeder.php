<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RsbsaApplication;
use Carbon\Carbon;

class RsbsaSpecificDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $applications = [
            [
                'full_name' => 'CASTASUS DENNIS CESAR V',
                'last_name' => 'CASTASUS',
                'first_name' => 'DENNIS CESAR',
                'middle_name' => 'VILLANUEVA',
                'barangay' => 'SAN VICENTE',
                'municipality' => 'CITY OF SAN PEDRO',
                'year' => 2024,
                'commodity' => 'LIVESTOCK',
                'main_livelihood' => 'Farmer', // Based on LIVESTOCK commodity
            ],
            [
                'full_name' => 'SENAPITE JOSE C',
                'last_name' => 'SENAPITE',
                'first_name' => 'JOSE',
                'middle_name' => 'CASUMPANG',
                'barangay' => 'UNITED BETTER LIVING',
                'municipality' => 'CITY OF SAN PEDRO',
                'year' => 2024,
                'commodity' => 'CROPS',
                'main_livelihood' => 'Farmer',
            ],
            [
                'full_name' => 'ESPALDON MARIO E',
                'last_name' => 'ESPALDON',
                'first_name' => 'MARIO',
                'middle_name' => 'ESTRELLADO',
                'barangay' => 'CALENDOLA',
                'municipality' => 'CITY OF SAN PEDRO',
                'year' => 2024,
                'commodity' => 'CROPS',
                'main_livelihood' => 'Farmer',
            ],
            [
                'full_name' => 'SERRADILLA IGMIDIO B',
                'last_name' => 'SERRADILLA',
                'first_name' => 'IGMIDIO',
                'middle_name' => 'BERROYA',
                'barangay' => 'PACITA 1',
                'municipality' => 'CITY OF SAN PEDRO',
                'year' => 2025,
                'commodity' => 'FISHERFOLK',
                'main_livelihood' => 'Fisherfolk',
            ],
            [
                'full_name' => 'PALAGANAS REGINA M',
                'last_name' => 'PALAGANAS',
                'first_name' => 'REGINA',
                'middle_name' => 'MANIQUIZ',
                'barangay' => 'ROSARIO',
                'municipality' => 'CITY OF SAN PEDRO',
                'year' => 2025,
                'commodity' => 'CROPS',
                'main_livelihood' => 'Farmer',
            ],
            [
                'full_name' => 'SOLER JEROME D',
                'last_name' => 'SOLER',
                'first_name' => 'JEROME',
                'middle_name' => 'DIWA',
                'barangay' => 'PACITA 2',
                'municipality' => 'CITY OF SAN PEDRO',
                'year' => 2025,
                'commodity' => 'CROPS',
                'main_livelihood' => 'Farmer',
            ],
            [
                'full_name' => 'DE OCAMPO ISAGANI V',
                'last_name' => 'DE OCAMPO',
                'first_name' => 'ISAGANI',
                'middle_name' => 'VILLANUEVA',
                'barangay' => 'CUYAB',
                'municipality' => 'CITY OF SAN PEDRO',
                'year' => 2025,
                'commodity' => 'CROPS',
                'main_livelihood' => 'Farmer',
            ],
            [
                'full_name' => 'CONTAPAY CRISTOPHER V',
                'last_name' => 'CONTAPAY',
                'first_name' => 'CRISTOPHER',
                'middle_name' => 'VELLEGAS',
                'barangay' => 'CUYAB',
                'municipality' => 'CITY OF SAN PEDRO',
                'year' => 2025,
                'commodity' => 'FISHERFOLK',
                'main_livelihood' => 'Fisherfolk',
            ],
            [
                'full_name' => 'KAHAL JAYSON W',
                'last_name' => 'KAHAL',
                'first_name' => 'JAYSON',
                'middle_name' => 'WAJENG',
                'barangay' => 'CUYAB',
                'municipality' => 'CITY OF SAN PEDRO',
                'year' => 2025,
                'commodity' => 'FISHERFOLK',
                'main_livelihood' => 'Fisherfolk',
            ],
            [
                'full_name' => 'JACARIA RASID U',
                'last_name' => 'JACARIA',
                'first_name' => 'RASID',
                'middle_name' => 'USMAN',
                'barangay' => 'CUYAB',
                'municipality' => 'CITY OF SAN PEDRO',
                'year' => 2025,
                'commodity' => 'FISHERFOLK',
                'main_livelihood' => 'Fisherfolk',
            ],
            [
                'full_name' => 'HAGASN ASMIL T',
                'last_name' => 'HAGASN',
                'first_name' => 'ASMIL',
                'middle_name' => 'TOTOH',
                'barangay' => 'CUYAB',
                'municipality' => 'CITY OF SAN PEDRO',
                'year' => 2025,
                'commodity' => 'FISHERFOLK',
                'main_livelihood' => 'Fisherfolk',
            ],
            [
                'full_name' => 'ARQUIZA ALDAM A',
                'last_name' => 'ARQUIZA',
                'first_name' => 'ALDAM',
                'middle_name' => 'ALPHA',
                'barangay' => 'CUYAB',
                'municipality' => 'CITY OF SAN PEDRO',
                'year' => 2025,
                'commodity' => 'FISHERFOLK',
                'main_livelihood' => 'Fisherfolk',
            ],
            [
                'full_name' => 'SAHI MOHAMMAD P',
                'last_name' => 'SAHI',
                'first_name' => 'MOHAMMAD',
                'middle_name' => 'PAWADJI',
                'barangay' => 'CUYAB',
                'municipality' => 'CITY OF SAN PEDRO',
                'year' => 2025,
                'commodity' => 'FISHERFOLK',
                'main_livelihood' => 'Fisherfolk',
            ],
            [
                'full_name' => 'VEGA MANUEL A',
                'last_name' => 'VEGA',
                'first_name' => 'MANUEL',
                'middle_name' => 'ATIENZA',
                'barangay' => 'CUYAB',
                'municipality' => 'CITY OF SAN PEDRO',
                'year' => 2025,
                'commodity' => 'FISHERFOLK',
                'main_livelihood' => 'Fisherfolk',
            ],
            [
                'full_name' => 'ESPARAGOZA JONATHAN S',
                'last_name' => 'ESPARAGOZA',
                'first_name' => 'JONATHAN',
                'middle_name' => 'SANTANES',
                'barangay' => 'CUYAB',
                'municipality' => 'CITY OF SAN PEDRO',
                'year' => 2025,
                'commodity' => 'FISHERFOLK',
                'main_livelihood' => 'Fisherfolk',
            ],
            [
                'full_name' => 'MUSTAR JOEL G',
                'last_name' => 'MUSTAR',
                'first_name' => 'JOEL',
                'middle_name' => 'GUEVARRA',
                'barangay' => 'LANDAYAN',
                'municipality' => 'CITY OF SAN PEDRO',
                'year' => 2025,
                'commodity' => 'FISHERFOLK',
                'main_livelihood' => 'Fisherfolk',
            ],
        ];

        foreach ($applications as $data) {
            // Determine commodity type and set farmer_crops or fisherfolk_activity
            $farmerCrops = null;
            $fisherfolkActivity = null;
            
            if ($data['commodity'] === 'CROPS') {
                $farmerCrops = 'Rice, Corn, Vegetables'; // Default value
            } elseif ($data['commodity'] === 'LIVESTOCK') {
                $farmerCrops = 'Livestock';
            } elseif ($data['commodity'] === 'FISHERFOLK') {
                $fisherfolkActivity = 'Fish capture'; // Default value
            }

            // Generate application number based on year
            $applicationNumber = 'RSBSA-' . $data['year'] . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // Set approved date based on year
            $approvedAt = Carbon::createFromDate($data['year'], rand(1, 12), rand(1, 28));

            RsbsaApplication::create([
                'application_number' => $applicationNumber,
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'],
                'last_name' => $data['last_name'],
                'name_extension' => null,
                'sex' => $this->guessSex($data['first_name']), // Simple guess based on name
                'contact_number' => '09' . rand(100000000, 999999999), // Random contact number
                'barangay' => $data['barangay'],
                'address' => $data['barangay'] . ', ' . $data['municipality'],
                'main_livelihood' => $data['main_livelihood'],
                'commodity' => $data['commodity'],
                
                // Farmer-specific fields
                'farmer_crops' => $farmerCrops,
                'farmer_land_area' => $data['main_livelihood'] === 'Farmer' ? rand(10, 500) / 100 : null,
                'farmer_type_of_farm' => $data['main_livelihood'] === 'Farmer' ? 'Rainfed Lowland' : null,
                'farmer_land_ownership' => $data['main_livelihood'] === 'Farmer' ? 'Owner' : null,
                'farm_location' => $data['main_livelihood'] === 'Farmer' ? $data['barangay'] : null,
                
                // Fisherfolk-specific fields
                'fisherfolk_activity' => $fisherfolkActivity,
                
                // Status - set all to approved based on the data
                'status' => 'approved',
                'approved_at' => $approvedAt,
                'reviewed_at' => $approvedAt->copy()->subDays(rand(1, 30)),
                'created_at' => $approvedAt->copy()->subMonths(rand(1, 3)),
                'updated_at' => $approvedAt,
                'number_assigned_at' => $approvedAt,
            ]);
        }
    }

    /**
     * Simple guess for sex based on first name
     */
    private function guessSex($firstName)
    {
        $femaleNames = ['REGINA', 'MARIA', 'ANA', 'ROSA', 'TERESA', 'LUZ', 'ELENA'];
        
        if (in_array($firstName, $femaleNames)) {
            return 'Female';
        }
        
        return 'Male';
    }
}