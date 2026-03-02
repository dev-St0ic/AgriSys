<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FishrApplication;
use Carbon\Carbon;

class FisherfolkRegisteredSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting FisherfolkRegisteredSeeder...');

        $fisherfolkData = [
            // ==================== 2024 DATA ====================
            // No matches found in boat data - use sequential FISHR numbers
            [
                'first_name' => 'MOHAMMAD MUSA',
                'middle_name' => 'ENGKENG',
                'last_name' => 'AHMAD',
                'barangay' => 'Cuyab',
                'main_livelihood' => 'aquaculture',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2024-001', // Sequential
                'registration_number' => 'FISHR-2024-001',
                'created_at' => Carbon::create(2024, 1, 15),
            ],
            [
                'first_name' => 'GLORIA',
                'middle_name' => 'MARQUEZ',
                'last_name' => 'APILAN',
                'barangay' => 'Cuyab',
                'main_livelihood' => 'vending',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2024-002', // Sequential
                'registration_number' => 'FISHR-2024-002',
                'created_at' => Carbon::create(2024, 1, 20),
            ],
            [
                'first_name' => 'MERLINDA',
                'middle_name' => 'PADILLA',
                'last_name' => 'BORJA',
                'barangay' => 'Landayan',
                'main_livelihood' => 'vending',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2024-003', // Sequential
                'registration_number' => 'FISHR-2024-003',
                'created_at' => Carbon::create(2024, 2, 5),
            ],
            [
                'first_name' => 'LUISITO',
                'middle_name' => 'ORPEL',
                'last_name' => 'CASILAG',
                'barangay' => 'Landayan',
                'main_livelihood' => 'capture',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2024-004', // Sequential
                'registration_number' => 'FISHR-2024-004',
                'created_at' => Carbon::create(2024, 2, 10),
            ],
            [
                'first_name' => 'APRONIANO',
                'middle_name' => 'TRASMIL',
                'last_name' => 'FILIPINAS',
                'barangay' => 'Cuyab',
                'main_livelihood' => 'aquaculture',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2024-005', // Sequential
                'registration_number' => 'FISHR-2024-005',
                'created_at' => Carbon::create(2024, 3, 5),
            ],
            [
                'first_name' => 'ALFONSO',
                'middle_name' => 'ABUNDO',
                'last_name' => 'TEMPROZA',
                'barangay' => 'Cuyab',
                'main_livelihood' => 'capture',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2024-006', // Sequential
                'registration_number' => 'FISHR-2024-006',
                'created_at' => Carbon::create(2024, 3, 12),
            ],
            [
                'first_name' => 'NURMIN',
                'middle_name' => 'ASLIM',
                'last_name' => 'TOTOH',
                'barangay' => 'Cuyab',
                'main_livelihood' => 'aquaculture',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2024-007', // Sequential
                'registration_number' => 'FISHR-2024-007',
                'created_at' => Carbon::create(2024, 4, 8),
            ],
            [
                'first_name' => 'SOFIA',
                'middle_name' => 'AMAGO',
                'last_name' => 'TRONO',
                'barangay' => 'Cuyab',
                'main_livelihood' => 'vending',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2024-008', // Sequential
                'registration_number' => 'FISHR-2024-008',
                'created_at' => Carbon::create(2024, 4, 15),
            ],
            [
                'first_name' => 'MELCHOR',
                'middle_name' => 'ROBLES',
                'last_name' => 'YARIS',
                'barangay' => 'Cuyab',
                'main_livelihood' => 'capture',
                'secondary_livelihood' => 'vending',
                'other_secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2024-009', // Sequential
                'registration_number' => 'FISHR-2024-009',
                'created_at' => Carbon::create(2024, 5, 3),
            ],

            // ==================== 2025 DATA ====================
            [
                'first_name' => 'SUSAN',
                'middle_name' => 'LEYVA',
                'last_name' => 'AGUILAR',
                'barangay' => 'Cuyab',
                'main_livelihood' => 'vending',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-001', // Sequential
                'registration_number' => 'FISHR-2025-001',
                'created_at' => Carbon::create(2025, 1, 10),
            ],
            [
                'first_name' => 'JEROME',
                'middle_name' => 'FRANCISCO',
                'last_name' => 'ANDRADA',
                'barangay' => 'Landayan',
                'main_livelihood' => 'vending',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-002', // Sequential
                'registration_number' => 'FISHR-2025-002',
                'created_at' => Carbon::create(2025, 1, 15),
            ],
            [
                'first_name' => 'MARICAR',
                'middle_name' => 'GUTIERES',
                'last_name' => 'ANDRADA',
                'barangay' => 'Landayan',
                'main_livelihood' => 'vending',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-003', // Sequential
                'registration_number' => 'FISHR-2025-003',
                'created_at' => Carbon::create(2025, 1, 20),
            ],
            [
                'first_name' => 'MICHELLE',
                'middle_name' => 'GONZALO',
                'last_name' => 'ANDRADA',
                'barangay' => 'Landayan',
                'main_livelihood' => 'vending',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-004', // Sequential
                'registration_number' => 'FISHR-2025-004',
                'created_at' => Carbon::create(2025, 2, 5),
            ],
            [
                'first_name' => 'RENANTE',
                'middle_name' => 'BALAGOT',
                'last_name' => 'ANDRADA',
                'barangay' => 'Landayan',
                'main_livelihood' => 'vending',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-005', // Sequential
                'registration_number' => 'FISHR-2025-005',
                'created_at' => Carbon::create(2025, 2, 8),
            ],
            [
                'first_name' => 'WILLIE',
                'middle_name' => 'REYES',
                'last_name' => 'ANDRADA',
                'barangay' => 'Landayan',
                'main_livelihood' => 'aquaculture',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-006', // Sequential
                'registration_number' => 'FISHR-2025-006',
                'created_at' => Carbon::create(2025, 2, 12),
            ],
            [
                'first_name' => 'ALDAM',
                'middle_name' => 'ALPHA',
                'last_name' => 'ARQUIZA',
                'barangay' => 'Cuyab',
                'main_livelihood' => 'aquaculture',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-007', // Sequential
                'registration_number' => 'FISHR-2025-007',
                'created_at' => Carbon::create(2025, 3, 3),
            ],
            [
                'first_name' => 'ALMA',
                'middle_name' => 'TAJA',
                'last_name' => 'AVELINA',
                'barangay' => 'Landayan',
                'main_livelihood' => 'vending',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-008', // Sequential
                'registration_number' => 'FISHR-2025-008',
                'created_at' => Carbon::create(2025, 3, 10),
            ],
            [
                'first_name' => 'ROMMEL',
                'middle_name' => 'AQUINO',
                'last_name' => 'BLANCA',
                'barangay' => 'Landayan',
                'main_livelihood' => 'capture',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-009', // Sequential
                'registration_number' => 'FISHR-2025-009',
                'created_at' => Carbon::create(2025, 3, 18),
            ],
            [
                'first_name' => 'FRANCISCO',
                'middle_name' => 'LUMANTAS',
                'last_name' => 'CANILLIAS',
                'barangay' => 'Landayan',
                'main_livelihood' => 'capture',
                'secondary_livelihood' => null,
                'fishr_number' => '2025-043425000-00712', // From boat data - MATCHED
                'registration_number' => 'FISHR-2025-010',
                'created_at' => Carbon::create(2025, 4, 2),
            ],
            [
                'first_name' => 'JOJIE',
                'middle_name' => 'PRODISIMO',
                'last_name' => 'CATALON',
                'barangay' => 'Landayan',
                'main_livelihood' => 'capture',
                'secondary_livelihood' => null,
                'fishr_number' => '27-043425000-00339', // From boat data - MATCHED
                'registration_number' => 'FISHR-2025-011',
                'created_at' => Carbon::create(2025, 4, 12),
            ],
            [
                'first_name' => 'SADJID',
                'middle_name' => 'BRILLANTES',
                'last_name' => 'CASALIN',
                'barangay' => 'Cuyab',
                'main_livelihood' => 'aquaculture',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-012', // Sequential
                'registration_number' => 'FISHR-2025-012',
                'created_at' => Carbon::create(2025, 4, 22),
            ],
            [
                'first_name' => 'ARNOLD',
                'middle_name' => 'PEREZ',
                'last_name' => 'DELA CRUZ',
                'barangay' => 'Landayan',
                'main_livelihood' => 'aquaculture',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-013', // Sequential
                'registration_number' => 'FISHR-2025-013',
                'created_at' => Carbon::create(2025, 5, 5),
            ],
            [
                'first_name' => 'RONILO',
                'middle_name' => 'TURING',
                'last_name' => 'DESTOPA',
                'barangay' => 'Cuyab',
                'main_livelihood' => 'aquaculture',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-014', // Sequential
                'registration_number' => 'FISHR-2025-014',
                'created_at' => Carbon::create(2025, 5, 15),
            ],
            [
                'first_name' => 'JOHN NORMAN',
                'middle_name' => 'SANTOS',
                'last_name' => 'DITABLAN',
                'barangay' => 'Cuyab',
                'main_livelihood' => 'aquaculture',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-015', // Sequential
                'registration_number' => 'FISHR-2025-015',
                'created_at' => Carbon::create(2025, 5, 25),
            ],
            [
                'first_name' => 'JOEL',
                'middle_name' => 'LEDIO',
                'last_name' => 'DOMENGUIS',
                'barangay' => 'Landayan',
                'main_livelihood' => 'vending',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-016', // Sequential
                'registration_number' => 'FISHR-2025-016',
                'created_at' => Carbon::create(2025, 6, 3),
            ],
            [
                'first_name' => 'RHONA',
                'middle_name' => 'RUTAQUIO',
                'last_name' => 'DORADO',
                'barangay' => 'Landayan',
                'main_livelihood' => 'vending',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-017', // Sequential
                'registration_number' => 'FISHR-2025-017',
                'created_at' => Carbon::create(2025, 6, 10),
            ],
            [
                'first_name' => 'ALBERTO',
                'middle_name' => 'VILLAMOR',
                'last_name' => 'FRANCISCO',
                'barangay' => 'Landayan',
                'main_livelihood' => 'aquaculture',
                'secondary_livelihood' => null,
                'fishr_number' => '2025-043425000-00719', // From boat data - MATCHED
                'registration_number' => 'FISHR-2025-018',
                'created_at' => Carbon::create(2025, 6, 18),
            ],
            [
                'first_name' => 'ERICKA MAE',
                'middle_name' => 'JOCSON',
                'last_name' => 'FUENTES',
                'barangay' => 'Landayan',
                'main_livelihood' => 'vending',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-019', // Sequential
                'registration_number' => 'FISHR-2025-019',
                'created_at' => Carbon::create(2025, 7, 2),
            ],
            [
                'first_name' => 'JAY',
                'middle_name' => 'MARCHADO',
                'last_name' => 'FUENTES',
                'barangay' => 'Landayan',
                'main_livelihood' => 'capture',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-020', // Sequential
                'registration_number' => 'FISHR-2025-020',
                'created_at' => Carbon::create(2025, 7, 12),
            ],
            [
                'first_name' => 'DAISYLYN',
                'middle_name' => 'DEO',
                'last_name' => 'GRANADO',
                'barangay' => 'Landayan',
                'main_livelihood' => 'vending',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-021', // Sequential
                'registration_number' => 'FISHR-2025-021',
                'created_at' => Carbon::create(2025, 7, 22),
            ],
            [
                'first_name' => 'MARILYN',
                'middle_name' => 'LOZADA',
                'last_name' => 'GONZALES',
                'barangay' => 'Cuyab',
                'main_livelihood' => 'vending',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-022', // Sequential
                'registration_number' => 'FISHR-2025-022',
                'created_at' => Carbon::create(2025, 8, 5),
            ],
            [
                'first_name' => 'PAULINO',
                'middle_name' => 'OMBIYANG',
                'last_name' => 'GUMIAL',
                'barangay' => 'Landayan',
                'main_livelihood' => 'capture',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-023', // Sequential
                'registration_number' => 'FISHR-2025-023',
                'created_at' => Carbon::create(2025, 8, 15),
            ],
            [
                'first_name' => 'ASMIL',
                'middle_name' => 'TOTOH',
                'last_name' => 'HASAN',
                'barangay' => 'Cuyab',
                'main_livelihood' => 'aquaculture',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-024', // Sequential
                'registration_number' => 'FISHR-2025-024',
                'created_at' => Carbon::create(2025, 8, 25),
            ],
            [
                'first_name' => 'RASID',
                'middle_name' => 'USMAN',
                'last_name' => 'JACARIA',
                'barangay' => 'Cuyab',
                'main_livelihood' => 'aquaculture',
                'secondary_livelihood' => null,
                'fishr_number' => '2025-043425000-00679', // From boat data - MATCHED
                'registration_number' => 'FISHR-2025-025',
                'created_at' => Carbon::create(2025, 9, 3),
            ],
            [
                'first_name' => 'ALBERT',
                'middle_name' => 'DAVID',
                'last_name' => 'MANALO',
                'barangay' => 'Cuyab',
                'main_livelihood' => 'capture',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-026', // Sequential
                'registration_number' => 'FISHR-2025-026',
                'created_at' => Carbon::create(2025, 9, 12),
            ],
            [
                'first_name' => 'REBECCA',
                'middle_name' => 'AMIL',
                'last_name' => 'MARMETO',
                'barangay' => 'Cuyab',
                'main_livelihood' => 'vending',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-027', // Sequential
                'registration_number' => 'FISHR-2025-027',
                'created_at' => Carbon::create(2025, 9, 22),
            ],
            [
                'first_name' => 'LUZVIMINDA',
                'middle_name' => 'RAMOS',
                'last_name' => 'MORALES',
                'barangay' => 'Landayan',
                'main_livelihood' => 'vending',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-028', // Sequential
                'registration_number' => 'FISHR-2025-028',
                'created_at' => Carbon::create(2025, 10, 5),
            ],
            [
                'first_name' => 'ISAIAS',
                'middle_name' => 'BAUSO',
                'last_name' => 'NUEZ',
                'barangay' => 'Landayan',
                'main_livelihood' => 'capture',
                'secondary_livelihood' => 'vending',
                'other_secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-029', // Sequential
                'registration_number' => 'FISHR-2025-029',
                'created_at' => Carbon::create(2025, 10, 15),
            ],
            [
                'first_name' => 'JHON JOSEPH',
                'middle_name' => 'BAUSO',
                'last_name' => 'NUEZ',
                'barangay' => 'Landayan',
                'main_livelihood' => 'capture',
                'secondary_livelihood' => 'vending',
                'other_secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-030', // Sequential
                'registration_number' => 'FISHR-2025-030',
                'created_at' => Carbon::create(2025, 10, 25),
            ],
            [
                'first_name' => 'ARLYN',
                'middle_name' => 'LUCAS',
                'last_name' => 'ORTEGA',
                'barangay' => 'Landayan',
                'main_livelihood' => 'vending',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-031', // Sequential
                'registration_number' => 'FISHR-2025-031',
                'created_at' => Carbon::create(2025, 11, 3),
            ],
            [
                'first_name' => 'RICHARD',
                'middle_name' => 'LASCANO',
                'last_name' => 'PASCASIO',
                'barangay' => 'Landayan',
                'main_livelihood' => 'capture',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-032', // Sequential
                'registration_number' => 'FISHR-2025-032',
                'created_at' => Carbon::create(2025, 11, 12),
            ],
            [
                'first_name' => 'MARGARITO',
                'middle_name' => 'GERONG',
                'last_name' => 'ROA',
                'barangay' => 'Cuyab',
                'main_livelihood' => 'aquaculture',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-033', // Sequential
                'registration_number' => 'FISHR-2025-033',
                'created_at' => Carbon::create(2025, 11, 22),
            ],
            [
                'first_name' => 'RODEL',
                'middle_name' => 'MARTINEZ',
                'last_name' => 'SADIAN',
                'barangay' => 'Landayan',
                'main_livelihood' => 'vending',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-034', // Sequential
                'registration_number' => 'FISHR-2025-034',
                'created_at' => Carbon::create(2025, 12, 2),
            ],
            [
                'first_name' => 'MOHAMMAD',
                'middle_name' => 'PAWADJI',
                'last_name' => 'SAHI',
                'barangay' => 'Cuyab',
                'main_livelihood' => 'aquaculture',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-035', // Sequential
                'registration_number' => 'FISHR-2025-035',
                'created_at' => Carbon::create(2025, 12, 8),
            ],
            [
                'first_name' => 'HECTOR',
                'middle_name' => 'LAURIO',
                'last_name' => 'SAUQUILLO',
                'barangay' => 'Landayan',
                'main_livelihood' => 'capture',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-036', // Sequential
                'registration_number' => 'FISHR-2025-036',
                'created_at' => Carbon::create(2025, 12, 15),
            ],
            [
                'first_name' => 'ANTHONY',
                'middle_name' => 'BAUSO',
                'last_name' => 'TOMAGAN',
                'barangay' => 'Landayan',
                'main_livelihood' => 'capture',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-037', // Sequential
                'registration_number' => 'FISHR-2025-037',
                'created_at' => Carbon::create(2025, 12, 20),
            ],
            [
                'first_name' => 'JOANNE',
                'middle_name' => 'LABINE',
                'last_name' => 'VIBAR',
                'barangay' => 'Landayan',
                'main_livelihood' => 'vending',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2025-038', // Sequential
                'registration_number' => 'FISHR-2025-038',
                'created_at' => Carbon::create(2025, 12, 28),
            ],

            // ==================== 2026 DATA ====================
            [
                'first_name' => 'JELLA',
                'middle_name' => 'MONTEMAYORES',
                'last_name' => 'BACHECHA',
                'barangay' => 'Landayan',
                'main_livelihood' => 'vending',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2026-001', // Sequential
                'registration_number' => 'FISHR-2026-001',
                'created_at' => Carbon::create(2026, 1, 5),
            ],
            [
                'first_name' => 'ELEANOR',
                'middle_name' => 'BECONIA',
                'last_name' => 'BALUYOT',
                'barangay' => 'Landayan',
                'main_livelihood' => 'vending',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2026-002', // Sequential
                'registration_number' => 'FISHR-2026-002',
                'created_at' => Carbon::create(2026, 1, 12),
            ],
            [
                'first_name' => 'RENATO',
                'middle_name' => 'RODRIGUEZ',
                'last_name' => 'CRISTOSTOMO',
                'barangay' => 'Cuyab',
                'main_livelihood' => 'capture',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2026-003', // Sequential
                'registration_number' => 'FISHR-2026-003',
                'created_at' => Carbon::create(2026, 1, 20),
            ],
            [
                'first_name' => 'REYNALDO',
                'middle_name' => 'CASPE',
                'last_name' => 'LIM',
                'barangay' => 'Cuyab',
                'main_livelihood' => 'capture',
                'secondary_livelihood' => null,
                'fishr_number' => 'FISHR-2026-004', // Sequential
                'registration_number' => 'FISHR-2026-004',
                'created_at' => Carbon::create(2026, 2, 1),
            ],
            [
                'first_name' => 'JERNIE',
                'middle_name' => 'CALINOG',
                'last_name' => 'TOLDANES',
                'barangay' => 'Cuyab',
                'main_livelihood' => 'capture',
                'secondary_livelihood' => null,
                'fishr_number' => '2026-043425000-00724', // From boat data - MATCHED
                'registration_number' => 'FISHR-2026-005',
                'created_at' => Carbon::create(2026, 2, 10),
            ],
        ];

        $createdCount = 0;
        $updatedCount = 0;
        $matchedCount = 0;
        $sequentialCount = 0;

        foreach ($fisherfolkData as $data) {
            // Check if record exists by fishr_number
            $existingRecord = FishrApplication::where('fishr_number', $data['fishr_number'])->first();
            
            // Count matched vs sequential
            if (strpos($data['fishr_number'], 'FISHR-') === 0) {
                $sequentialCount++;
            } else {
                $matchedCount++;
            }
            
            $recordData = [
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'],
                'last_name' => $data['last_name'],
                'name_extension' => null,
                'barangay' => $data['barangay'],
                'contact_number' => null,
                'main_livelihood' => $data['main_livelihood'],
                'secondary_livelihood' => $data['secondary_livelihood'] ?? null,
                'other_secondary_livelihood' => $data['other_secondary_livelihood'] ?? null,
                'status' => 'approved',
                'registration_number' => $data['registration_number'],
                'fishr_number_assigned_at' => $data['created_at'],
                'fishr_number_assigned_by' => 1,
                'updated_at' => $data['created_at'],
            ];

            if ($existingRecord) {
                $existingRecord->update($recordData);
                $updatedCount++;
            } else {
                $recordData['fishr_number'] = $data['fishr_number'];
                $recordData['created_at'] = $data['created_at'];
                FishrApplication::create($recordData);
                $createdCount++;
            }
        }

        $this->command->info('FisherfolkRegisteredSeeder executed successfully!');
        $this->command->info("Records created: {$createdCount}");
        $this->command->info("Records updated: {$updatedCount}");
        $this->command->info('Total records in database: ' . FishrApplication::count());
        
        $this->command->info("\n📊 FishR Number Statistics:");
        $this->command->info("Matched from boat data: {$matchedCount}");
        $this->command->info("Sequential FISHR numbers: {$sequentialCount}");
    }
}