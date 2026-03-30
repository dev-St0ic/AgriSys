<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RsbsaApplication;
use Carbon\Carbon;

class RsbsaSpecificDataSeeder extends Seeder
{
    public function run(): void
    {
        $applications = [
            [
                'last_name' => 'CASTASUS',
                'first_name' => 'DENNIS CESAR',
                'middle_name' => 'VILLANUEVA',
                'barangay' => 'SAN VICENTE',
                'municipality' => 'CITY OF SAN PEDRO',
                'year' => 2024,
                'commodity' => 'LIVESTOCK',
                'main_livelihood' => 'Farmer',
            ],
            [
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

        static $counter   = [];
        static $dateOffset = [];

        foreach ($applications as $data) {
            $farmerCrops      = null;
            $fisherfolkActivity = null;

            if ($data['commodity'] === 'CROPS') {
                $farmerCrops = 'Rice, Corn, Vegetables';
            } elseif ($data['commodity'] === 'LIVESTOCK') {
                $farmerCrops = 'Livestock';
            } elseif ($data['commodity'] === 'FISHERFOLK') {
                $fisherfolkActivity = 'Fish capture';
            }

            $year = $data['year'];
            $counter[$year]    = ($counter[$year] ?? 0) + 1;
            $dateOffset[$year] = ($dateOffset[$year] ?? 0) + 14;

            $applicationNumber = 'RSBSA-' . $year . '-' . str_pad($counter[$year], 3, '0', STR_PAD_LEFT);

            // ── KEY FIX: created_at is BEFORE approved_at ──────────────
            $processingDays = rand(7, 30);           // realistic 7–30 day window
            $approvedAt     = Carbon::createFromDate($year, 1, 1)->addDays($dateOffset[$year]);
            $createdAt      = $approvedAt->copy()->subDays($processingDays);
            $reviewedAt     = $createdAt->copy()->addDays(rand(1, max(1, $processingDays - 1)));
            // ──────────────────────────────────────────────────────────

            RsbsaApplication::create([
                'application_number'  => $applicationNumber,
                'first_name'          => $this->toProperCase($data['first_name']),
                'middle_name'         => $this->toProperCase($data['middle_name']),
                'last_name'           => $this->toProperCase($data['last_name']),
                'name_extension'      => null,
                'sex'                 => $this->guessSex($data['first_name']),
                'contact_number'      => '09' . rand(100000000, 999999999),
                'barangay'            => $data['barangay'],
                'address'             => $data['barangay'] . ', ' . $data['municipality'],
                'main_livelihood'     => $data['main_livelihood'],
                'commodity'           => $data['commodity'],

                // Farmer-specific
                'farmer_crops'        => $farmerCrops,
                'farmer_land_area'    => $data['main_livelihood'] === 'Farmer' ? rand(10, 500) / 100 : null,
                'farmer_type_of_farm' => $data['main_livelihood'] === 'Farmer' ? 'Rainfed Lowland' : null,
                'farmer_land_ownership' => $data['main_livelihood'] === 'Farmer' ? 'Owner' : null,
                'farm_location'       => $data['main_livelihood'] === 'Farmer' ? $data['barangay'] : null,

                // Fisherfolk-specific
                'fisherfolk_activity' => $fisherfolkActivity,

                // Status & dates — proper chronological order
                'status'              => 'approved',
                'created_at'          => $createdAt,   // submitted first
                'reviewed_at'         => $reviewedAt,  // reviewed in between
                'approved_at'         => $approvedAt,  // approved last
                'updated_at'          => $approvedAt,
                'number_assigned_at'  => $approvedAt,
            ]);
        }
    }

    private function toProperCase(?string $value): ?string
    {
        return $value === null ? null : ucwords(strtolower($value));
    }

    private function guessSex($firstName): string
    {
        $femaleNames = ['REGINA', 'MARIA', 'ANA', 'ROSA', 'TERESA', 'LUZ', 'ELENA'];
        return in_array($firstName, $femaleNames) ? 'Female' : 'Male';
    }
}