<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BoatrApplication;
use App\Models\FishrApplication;
use Carbon\Carbon;

class BoatrRegisteredSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Fishing gear mapping:
     *   Gillnet / GILLNET  →  'Bottom Set Gill Net'
     *   N/A                →  'Not Applicable'
     *   Baklad             →  'Fish Coral'
     *   BUBO               →  'Not Applicable'
     *
     * FishR number cross-reference (matches FisherfolkRegisteredSeeder exactly):
     *
     *   2024 motorized boat owners  → FISHR-2024-010 … FISHR-2024-085
     *   2024 non-motorized owners   → FISHR-2024-086 … FISHR-2024-123
     *   2025 non-motorized owners   → FISHR-2025-039 … FISHR-2025-051
     *   2025 motorized boat owners  → FISHR-2025-052 … FISHR-2025-068
     *   2026 non-motorized owners   → FISHR-2026-005
     *   2026 motorized boat owners  → FISHR-2026-006 … FISHR-2026-012
     *
     *   Owners that appear in both non-motorized and motorized sections
     *   share the same single FishR number (one person, multiple boats):
     *     FISHR-2024-017 = Demetrio Moreno        (RICO 3 + DEMETRIO)
     *     FISHR-2024-011 = Ronald Curampez        (TING 1, TING 2, KEEYAN&ANIQA)
     *     FISHR-2024-021 = Rico Machado            (RICO 1, RICO 8, RICO 9)
     *     FISHR-2024-012 = Artemio Guab            (RICO 4, RICO 5, RICO 10)
     *     FISHR-2024-072 = Roberto Raguit          (JUN, NORIEL, JUN 3, JUN 4, JUN 5, JUN 6)
     *     FISHR-2024-063 = Graciano Insorio        (POLYN + non-motorized N/A)
     *     FISHR-2024-064 = Aquilino Yambao         (PJ YAMBAO + PJ)
     *     FISHR-2024-031 = Rodrigo Doroteo         (LA LAKERS, LA LAKERS 2, LA LAKERS 3)
     *     FISHR-2024-114 = Marvic Mejias           (VIC 1 + VIC 2)
     *     FISHR-2025-053 = Christopher Avelina     (CRISTOPHER + TOPHER)
     *     FISHR-2025-025 = Rasid Jacaria           (SHAWY-AJ 1, 2, 3)
     */
    public function run(): void
    {
        $this->command->info('Starting BoatrRegisteredSeeder …');

        // Build a map of fishr_number → FishrApplication record for name resolution
        $fishrMap = FishrApplication::whereNotNull('fishr_number')
            ->get()
            ->keyBy('fishr_number');

        $this->command->info('Found ' . $fishrMap->count() . ' existing FishR records in DB.');

        $pc = fn(?string $v) => $v !== null ? ucwords(strtolower($v)) : null;

        /**
         * $make — when $fn is null the owner name is resolved from the linked FishrApplication.
         * Contact number is always pulled from the linked FishrApplication when available.
         */
        $make = function (
            string  $appNo,
            ?string $fn, ?string $mn, ?string $ln, ?string $ext,
            string  $barangay,
            ?string $fishrNo,
            ?string $vessel,
            string  $class,
            ?float  $tl, ?float $tb, ?float $td,
            ?string $engineType, $hp,
            string  $gear,
            string  $remarks,
            Carbon  $createdAt,
            Carbon  $approvedAt
        ) use ($pc, $fishrMap): array {

            $fishrApp   = ($fishrNo && $fishrMap->has($fishrNo)) ? $fishrMap->get($fishrNo) : null;
            $fishrAppId = $fishrApp?->id;

            if ($fishrApp && $fn === null) {
                $fn  = $fishrApp->first_name;
                $mn  = $fishrApp->middle_name;
                $ln  = $fishrApp->last_name;
                $ext = $fishrApp->name_extension;
            } else {
                $fn  = $pc($fn);
                $mn  = $pc($mn);
                $ln  = $pc($ln);
                $ext = $pc($ext);
            }

            // Always pull contact number from the linked FishR record regardless of name source
            $contact = $fishrApp?->contact_number ?? null;

            // Inspection is done one day before approval
            $inspectionDate = $approvedAt->copy()->subDay();

            return [
                'application_number'          => $appNo,
                'first_name'                  => $fn,
                'middle_name'                 => $mn,
                'last_name'                   => $ln,
                'name_extension'              => $ext,
                'contact_number'              => $contact,
                'barangay'                    => $barangay,
                'fishr_number'                => $fishrNo,
                'fishr_application_id'        => $fishrAppId,
                'vessel_name'                 => $vessel,
                'boat_type'                   => 'Banca',
                'boat_classification'         => $class,
                'boat_length'                 => $tl,
                'boat_width'                  => $tb,
                'boat_depth'                  => $td,
                'engine_type'                 => $engineType,
                'engine_horsepower'           => $hp,
                'primary_fishing_gear'        => $gear,
                'user_document_path'          => null,
                'user_document_name'          => null,
                'user_document_type'          => null,
                'user_document_size'          => null,
                'user_document_uploaded_at'   => null,
                'inspection_documents'        => null,
                'inspection_completed'        => true,
                'inspection_date'             => $inspectionDate,
                'inspection_notes'            => 'Inspection completed',
                'inspected_by'                => 1,
                'documents_verified'          => true,
                'documents_verified_at'       => $approvedAt,
                'document_verification_notes' => 'Documents verified',
                'status'                      => 'approved',
                'remarks'                     => $remarks,
                'reviewed_at'                 => $approvedAt,
                'reviewed_by'                 => 1,
                'status_history'              => json_encode([
                    ['status' => 'pending',              'timestamp' => $createdAt->toDateTimeString(),                          'notes' => 'Application submitted'],
                    ['status' => 'under_review',         'timestamp' => $createdAt->copy()->addDay()->toDateTimeString(),        'notes' => 'Under review'],
                    ['status' => 'inspection_scheduled', 'timestamp' => $createdAt->copy()->addDays(2)->toDateTimeString(),      'notes' => 'Inspection scheduled'],
                    ['status' => 'inspection_required',  'timestamp' => $inspectionDate->toDateTimeString(),                     'notes' => 'Inspection completed'],
                    ['status' => 'approved',             'timestamp' => $approvedAt->toDateTimeString(),                         'notes' => 'Approved'],
                ]),
                'inspection_scheduled_at'     => $createdAt->copy()->addDays(2),
                'approved_at'                 => $approvedAt,
                'rejected_at'                 => null,
                'created_at'                  => $createdAt,
                'updated_at'                  => $approvedAt,
            ];
        };

        $GN  = 'Bottom Set Gill Net';
        $FC  = 'Fish Coral';
        $NA  = 'Not Applicable';

        $WMC = 'with maritime clearance';
        $NMC = 'no maritime clearance';
        $NMB = 'Registered non-motorized fishing boat';

        $boatData = [

            // ==================================================================
            // 2024 MOTORIZED  (FISHR-2024-010 … FISHR-2024-085)
            // ==================================================================

            $make('BOATR-2024-M-001','TEODORO',null,'CLEMENTE',null,'Cuyab',
                'FISHR-2024-010','ATO','Motorized',
                6.98,0.95,0.46,'BS HP 16',16,$GN,$NMC,
                Carbon::create(2024,1,14),Carbon::create(2024,1,16)),

            $make('BOATR-2024-M-002','RONALD','E.','CURAMPEZ',null,'Cuyab',
                'FISHR-2024-011','TING 1','Motorized',
                5.69,1.37,0.51,'YAMADA',10,$NA,$WMC,
                Carbon::create(2024,1,17),Carbon::create(2024,1,19)),

            $make('BOATR-2024-M-003','RONALD','E.','CURAMPEZ',null,'Cuyab',
                'FISHR-2024-011','TING 2','Motorized',
                7.96,1.16,0.46,'KAMA',12,$NA,$WMC,
                Carbon::create(2024,1,20),Carbon::create(2024,1,22)),

            $make('BOATR-2024-M-004','ARTEMIO','B.','GUAB',null,'Cuyab',
                'FISHR-2024-012','RICO 4','Motorized',
                6.70,1.25,0.55,'ALATA',10,$NA,$WMC,
                Carbon::create(2024,1,23),Carbon::create(2024,1,25)),

            $make('BOATR-2024-M-005','ARTEMIO','B.','GUAB',null,'Cuyab',
                'FISHR-2024-012','RICO 5','Motorized',
                9.32,1.82,1.21,'MITSUBISHI',500,$NA,$WMC,
                Carbon::create(2024,1,26),Carbon::create(2024,1,28)),

            $make('BOATR-2024-M-006','ARTEMIO','B.','GUAB',null,'Cuyab',
                'FISHR-2024-012','RICO 10','Motorized',
                8.56,1.67,0.64,'4DR5',300,$NA,$WMC,
                Carbon::create(2024,1,29),Carbon::create(2024,1,31)),

            $make('BOATR-2024-M-007','CHRISTIAN','P.','MAGO',null,'Cuyab',
                'FISHR-2024-013','RICO 2','Motorized',
                6.88,1.12,0.39,'YAMADA',18,$NA,$WMC,
                Carbon::create(2024,2,1),Carbon::create(2024,2,3)),

            $make('BOATR-2024-M-008','FREDDIE','H.','JUANERIO',null,'Cuyab',
                'FISHR-2024-014','RICO 7','Motorized',
                8.50,1.18,0.65,'C240',300,$NA,$WMC,
                Carbon::create(2024,2,4),Carbon::create(2024,2,6)),

            $make('BOATR-2024-M-009','DARYL',null,'ILAGAN',null,'Cuyab',
                'FISHR-2024-015','MV DARYL','Motorized',
                6.10,0.82,0.51,'ROBIN',7.5,$GN,$NMC,
                Carbon::create(2024,2,7),Carbon::create(2024,2,9)),

            $make('BOATR-2024-M-010','CRISPIN','A.','VILLADIEGO',null,'Landayan',
                'FISHR-2024-016','CRISPIN','Motorized',
                6.83,1.00,0.55,'YAMADA',10,$GN,$NMC,
                Carbon::create(2024,2,10),Carbon::create(2024,2,12)),

            $make('BOATR-2024-M-011','DEMETRIO','C.','MORENO',null,'Cuyab',
                'FISHR-2024-017','RICO 3','Motorized',
                8.23,0.88,0.39,'MODEL KM186(A)',10,$NA,$WMC,
                Carbon::create(2024,2,13),Carbon::create(2024,2,15)),

            $make('BOATR-2024-M-012','RYAN','B.','ANGELES',null,'Cuyab',
                'FISHR-2024-018','RYAN','Motorized',
                6.94,1.19,0.31,'YAMADA',10,$NA,$WMC,
                Carbon::create(2024,2,16),Carbon::create(2024,2,18)),

            $make('BOATR-2024-M-013','EDGAR','A.','ALAMO',null,'Cuyab',
                'FISHR-2024-019','ALAMO','Motorized',
                7.01,0.94,0.45,'BRIGGS STRATON',16,$GN,$NMC,
                Carbon::create(2024,2,19),Carbon::create(2024,2,21)),

            $make('BOATR-2024-M-014','MARIANO','M.','MACALINAO',null,'Cuyab',
                'FISHR-2024-020','MV NANING','Motorized',
                8.57,0.70,0.70,'JAPAN',18,$GN,$WMC,
                Carbon::create(2024,2,22),Carbon::create(2024,2,24)),

            $make('BOATR-2024-M-015','RICO','O.','MACHADO',null,'Cuyab',
                'FISHR-2024-021','RICO 1','Motorized',
                6.98,1.18,1.18,'YAMADA',25,$NA,$WMC,
                Carbon::create(2024,2,25),Carbon::create(2024,2,27)),

            $make('BOATR-2024-M-016','RICO','O.','MACHADO',null,'Cuyab',
                'FISHR-2024-021','RICO 8','Motorized',
                8.53,1.20,1.18,'HYUNDAI',300,$NA,$WMC,
                Carbon::create(2024,2,28),Carbon::create(2024,3,1)),

            $make('BOATR-2024-M-017','RICO','O.','MACHADO',null,'Cuyab',
                'FISHR-2024-021','RICO 9','Motorized',
                8.50,1.46,0.70,'MITSUBISHI',300,$NA,$WMC,
                Carbon::create(2024,3,2),Carbon::create(2024,3,4)),

            $make('BOATR-2024-M-018','ALFREDO','T.','CAOYONG',null,'Landayan',
                'FISHR-2024-022','WARAK','Motorized',
                5.19,0.76,0.37,'ROBIN',5,$GN,$WMC,
                Carbon::create(2024,3,5),Carbon::create(2024,3,7)),

            $make('BOATR-2024-M-019','LORENZO','R.','ALMEIDA',null,'Landayan',
                'FISHR-2024-023','BOY','Motorized',
                7.30,0.66,0.45,'KEMBO',6.5,$GN,$WMC,
                Carbon::create(2024,3,8),Carbon::create(2024,3,10)),

            $make('BOATR-2024-M-020','RUEL','M.','GUMAL',null,'Cuyab',
                'FISHR-2024-024','N/A','Motorized',
                6.50,0.92,0.50,'EXTREME GASOLINE',6.5,$GN,$WMC,
                Carbon::create(2024,3,11),Carbon::create(2024,3,13)),

            $make('BOATR-2024-M-021','RODITO','M.','GUMAL',null,'Landayan',
                'FISHR-2024-025','RODY','Motorized',
                7.80,0.90,0.50,'YAMADA',12,$GN,$WMC,
                Carbon::create(2024,3,14),Carbon::create(2024,3,16)),

            $make('BOATR-2024-M-023','NOLLIE','Q.','CAOYONG',null,'Landayan',
                'FISHR-2024-026','KYRIE','Motorized',
                6.00,0.83,0.58,'YAMADA',18,$GN,$WMC,
                Carbon::create(2024,3,17),Carbon::create(2024,3,19)),

            $make('BOATR-2024-M-024','ARIEL','N.','DELOS REYES',null,'Landayan',
                'FISHR-2024-027','MV 4 BOYS','Motorized',
                6.40,0.88,0.88,'ROBIN',6.5,$NA,$WMC,
                Carbon::create(2024,3,20),Carbon::create(2024,3,22)),

            $make('BOATR-2024-M-027','MICHAEL','S.','HEREDIA',null,'Landayan',
                'FISHR-2024-028','MIKE','Motorized',
                3.63,0.88,0.88,'YAMADA',9,$GN,$WMC,
                Carbon::create(2024,3,23),Carbon::create(2024,3,25)),

            $make('BOATR-2024-M-035','ENRICO','S.','DELOS REYES SR.',null,'Landayan',
                'FISHR-2024-029','MV ENRICO','Motorized',
                5.70,0.85,0.30,'ROBIN',7.5,$GN,$WMC,
                Carbon::create(2024,3,26),Carbon::create(2024,3,28)),

            $make('BOATR-2024-M-036','RENATO','B.','OLIVAREZ',null,'Landayan',
                'FISHR-2024-030','MB MERCI','Motorized',
                5.70,0.83,0.48,'KINGSTONE',12,$FC,$WMC,
                Carbon::create(2024,3,29),Carbon::create(2024,3,31)),

            $make('BOATR-2024-M-037','RODRIGO','B.','DOROTEO',null,'Cuyab',
                'FISHR-2024-031','LA LAKERS','Motorized',
                6.20,1.92,0.88,'ISUZU',240,$NA,$WMC,
                Carbon::create(2024,4,1),Carbon::create(2024,4,3)),

            $make('BOATR-2024-M-038','RODRIGO','B.','DOROTEO',null,'Cuyab',
                'FISHR-2024-031','LA LAKERS 2','Motorized',
                8.19,1.31,0.57,'TOYOTA',139,$NA,$WMC,
                Carbon::create(2024,4,4),Carbon::create(2024,4,6)),

            $make('BOATR-2024-M-039','RODRIGO','B.','DOROTEO',null,'Landayan',
                'FISHR-2024-031','LA LAKERS 3','Motorized',
                7.00,0.88,0.48,'ROBIN',8,$NA,$WMC,
                Carbon::create(2024,4,7),Carbon::create(2024,4,9)),

            $make('BOATR-2024-M-042','JERICKSON','C.','SILVANO',null,'Landayan',
                'FISHR-2024-032','JERICK','Motorized',
                7.29,0.73,0.43,'ROBIN',5,$NA,$WMC,
                Carbon::create(2024,4,10),Carbon::create(2024,4,12)),

            $make('BOATR-2024-M-044','CESAR',null,'ABUNDO',null,'Cuyab',
                'FISHR-2024-033','CESAR','Motorized',
                5.30,1.06,0.20,'NITO',6.5,$GN,$WMC,
                Carbon::create(2024,4,13),Carbon::create(2024,4,15)),

            $make('BOATR-2024-M-045','ELISEO','C.','ESCUDERO',null,'Landayan',
                'FISHR-2024-034','ELY','Motorized',
                4.97,0.77,0.48,'KEMBO',6.5,$GN,$WMC,
                Carbon::create(2024,4,16),Carbon::create(2024,4,18)),

            $make('BOATR-2024-M-046','MARCIAL','L.','TORRES',null,'Landayan',
                'FISHR-2024-035','MARCIAL','Motorized',
                3.63,0.85,0.50,'HONDA',13,$GN,$WMC,
                Carbon::create(2024,4,19),Carbon::create(2024,4,21)),

            $make('BOATR-2024-M-048','PELAGIO','A.','GARCIA',null,'Landayan',
                'FISHR-2024-036','RICO 5','Motorized',
                5.75,0.97,0.34,'NITTO',7.5,$NA,$WMC,
                Carbon::create(2024,4,22),Carbon::create(2024,4,24)),

            $make('BOATR-2024-M-049','FERDINAND','P.','GARCIA',null,'Landayan',
                'FISHR-2024-037','FERDINAND','Motorized',
                6.06,0.50,0.57,'SUMO',15,$NA,$WMC,
                Carbon::create(2024,4,25),Carbon::create(2024,4,27)),

            $make('BOATR-2024-M-050','RICKY','C.','CURAMPES',null,'Landayan',
                'FISHR-2024-038','RICKY','Motorized',
                4.30,0.88,0.72,'ROBIN',7.5,$GN,$WMC,
                Carbon::create(2024,4,28),Carbon::create(2024,4,30)),

            $make('BOATR-2024-M-056','EDUARDO','E.','GONZALES',null,'Landayan',
                'FISHR-2024-039','EDUARDO','Motorized',
                5.48,0.90,0.30,'BEARING',6.5,$GN,$WMC,
                Carbon::create(2024,5,1),Carbon::create(2024,5,3)),

            $make('BOATR-2024-M-058','BERLIN','P.','OLIVAREZ',null,'Landayan',
                'FISHR-2024-040','BERLIN','Motorized',
                3.63,0.82,0.31,'BRIGGS STRATON',10,$GN,$WMC,
                Carbon::create(2024,5,4),Carbon::create(2024,5,6)),

            $make('BOATR-2024-M-060','MANUEL','R.','ALMEIDA',null,'Landayan',
                'FISHR-2024-041','MANUELITO','Motorized',
                7.00,0.80,0.50,'NITTO',10,$GN,$WMC,
                Carbon::create(2024,5,7),Carbon::create(2024,5,9)),

            $make('BOATR-2024-M-061','CRISPIN','R.','ALMEIDA',null,'Landayan',
                'FISHR-2024-042','CRIS','Motorized',
                6.50,0.88,0.50,'LONTOP',7.5,$GN,$WMC,
                Carbon::create(2024,5,10),Carbon::create(2024,5,12)),

            $make('BOATR-2024-M-062','LARIO','J.','ALMEIDA',null,'Landayan',
                'FISHR-2024-043','LARIO','Motorized',
                3.93,0.89,0.58,'ROBIN',7,$GN,$WMC,
                Carbon::create(2024,5,13),Carbon::create(2024,5,15)),

            $make('BOATR-2024-M-063','ROBERT','T.','CORDOVA',null,'Landayan',
                'FISHR-2024-044','OBET','Motorized',
                5.30,0.70,0.31,'NITTO',6.5,$GN,$WMC,
                Carbon::create(2024,5,16),Carbon::create(2024,5,18)),

            $make('BOATR-2024-M-064','FLORENTINO','R.','ALMEIDA',null,'Landayan',
                'FISHR-2024-045','JHUN','Motorized',
                3.90,0.75,0.30,'NITTO',6.5,$GN,$WMC,
                Carbon::create(2024,5,19),Carbon::create(2024,5,21)),

            $make('BOATR-2024-M-066','ROGER','A.','TEÑIDO',null,'Landayan',
                'FISHR-2024-046','ROGER','Motorized',
                5.50,0.76,0.34,'SUPREME',6.5,$GN,$WMC,
                Carbon::create(2024,5,22),Carbon::create(2024,5,24)),

            $make('BOATR-2024-M-067','VENANCIO','C.','SARIO',null,'Landayan',
                'FISHR-2024-047','CHING','Motorized',
                5.21,0.82,0.87,'ROBIN',7.5,$GN,$WMC,
                Carbon::create(2024,5,25),Carbon::create(2024,5,27)),

            $make('BOATR-2024-M-068','FERNAND','C.','ALMEIDA',null,'Landayan',
                'FISHR-2024-048','FERNAND','Motorized',
                6.65,0.89,0.58,'YANMAR',8,$GN,$WMC,
                Carbon::create(2024,5,28),Carbon::create(2024,5,30)),

            $make('BOATR-2024-M-069','FERNANDO','R.','ALMEIDA',null,'Landayan',
                'FISHR-2024-049','PANDO','Motorized',
                7.63,0.83,0.53,'SUMO',15,$GN,$WMC,
                Carbon::create(2024,5,31),Carbon::create(2024,6,2)),

            $make('BOATR-2024-M-071','DANILO','V.','MACHADO',null,'Cuyab',
                'FISHR-2024-050','QUIN','Motorized',
                7.39,0.35,0.37,'HONDA',8,$GN,$WMC,
                Carbon::create(2024,6,3),Carbon::create(2024,6,5)),

            $make('BOATR-2024-M-072','DOMINGO','Q.','RONCALES',null,'Landayan',
                'FISHR-2024-051','DQR','Motorized',
                5.97,0.67,0.55,'SUMO',15,$GN,$WMC,
                Carbon::create(2024,6,6),Carbon::create(2024,6,8)),

            $make('BOATR-2024-M-073','GERMAN','M.','HALAYAHAY',null,'Landayan',
                'FISHR-2024-052','GERMAN','Motorized',
                4.60,0.85,0.56,'MARINE',16.5,$GN,$WMC,
                Carbon::create(2024,6,9),Carbon::create(2024,6,11)),

            $make('BOATR-2024-M-074','ROMAR','S.','AMION',null,'Landayan',
                'FISHR-2024-053','AMBER','Motorized',
                7.29,1.19,0.50,'SUMO YAMATO',15,$GN,$WMC,
                Carbon::create(2024,6,12),Carbon::create(2024,6,14)),

            $make('BOATR-2024-M-075','JAIMEE','V.','MACHADO',null,'Landayan',
                'FISHR-2024-054','TOTO','Motorized',
                7.30,0.62,0.43,'NITTO',7,$GN,$WMC,
                Carbon::create(2024,6,15),Carbon::create(2024,6,17)),

            $make('BOATR-2024-M-076','ROMEO','R.','ANDRADA',null,'Landayan',
                'FISHR-2024-055','ROMY','Motorized',
                5.74,0.82,0.40,'YAMADA',18,$GN,$WMC,
                Carbon::create(2024,6,18),Carbon::create(2024,6,20)),

            $make('BOATR-2024-M-077','JIMSON','M.','BACHECHA',null,'Landayan',
                'FISHR-2024-056','ELISON','Motorized',
                5.50,0.80,0.50,'YAMADA',16,$GN,$WMC,
                Carbon::create(2024,6,21),Carbon::create(2024,6,23)),

            $make('BOATR-2024-M-079','JULIUS','P.','ODERO',null,'Cuyab',
                'FISHR-2024-057','JHIRO ANGELO','Motorized',
                5.76,0.82,0.37,'ROBIN',5,$NA,$WMC,
                Carbon::create(2024,6,24),Carbon::create(2024,6,26)),

            $make('BOATR-2024-M-085','RICARDO','R.','ANDRADA',null,'Landayan',
                'FISHR-2024-058','RICARDO','Motorized',
                5.60,0.78,0.10,'KEMBO',6.5,$GN,$WMC,
                Carbon::create(2024,6,27),Carbon::create(2024,6,29)),

            $make('BOATR-2024-M-086','JAVIER','M.','BUCALAN',null,'Landayan',
                'FISHR-2024-059','JAVIER','Motorized',
                6.00,0.80,0.58,'ROBIN',7.5,$GN,$WMC,
                Carbon::create(2024,6,30),Carbon::create(2024,7,2)),

            $make('BOATR-2024-M-091','ROBERTO','S.','TEMPROSA',null,'Landayan',
                'FISHR-2024-060','OBET M/B','Motorized',
                5.91,0.70,0.31,'MITSUBISHI',5,$GN,$WMC,
                Carbon::create(2024,7,3),Carbon::create(2024,7,5)),

            $make('BOATR-2024-M-094','AERON BRIAN','S.','INSORIO',null,'Landayan',
                'FISHR-2024-061','GERON','Motorized',
                6.25,0.76,0.52,'YAMADA',18,$GN,$WMC,
                Carbon::create(2024,7,6),Carbon::create(2024,7,8)),

            $make('BOATR-2024-M-095','DONATO','N.','MARISTANEZ',null,'Landayan',
                'FISHR-2024-062','DEX','Motorized',
                5.51,0.64,0.51,'HONDA',5.5,$GN,$WMC,
                Carbon::create(2024,7,9),Carbon::create(2024,7,11)),

            $make('BOATR-2024-M-097','GRACIANO','R.','INSORIO',null,'Landayan',
                'FISHR-2024-063','POLYN','Motorized',
                5.79,0.61,0.41,'HONDA',5.5,$GN,$WMC,
                Carbon::create(2024,7,12),Carbon::create(2024,7,14)),

            $make('BOATR-2024-M-099','AQUILINO','L.','YAMBAO',null,'Landayan',
                'FISHR-2024-064','PJ YAMBAO','Motorized',
                6.10,0.73,0.48,'KAWAMA',6.5,$FC,$WMC,
                Carbon::create(2024,7,15),Carbon::create(2024,7,17)),

            $make('BOATR-2024-M-101','SENANDO','A.','BACHECHA JR',null,'Landayan',
                'FISHR-2024-065','TIRAMISU','Motorized',
                6.00,0.85,0.45,'YAMADA',18,$GN,$WMC,
                Carbon::create(2024,7,18),Carbon::create(2024,7,20)),

            $make('BOATR-2024-M-102','EDEJIE','D.','CABANLIT',null,'Landayan',
                'FISHR-2024-066','2 BROTHERS','Motorized',
                7.60,0.97,0.48,'SUMO',15,$GN,$WMC,
                Carbon::create(2024,7,21),Carbon::create(2024,7,23)),

            $make('BOATR-2024-M-104','CRISPIN','A.','TEMPROSA',null,'Landayan',
                'FISHR-2024-067','CRISPIN','Motorized',
                6.00,0.65,0.60,'YAMADA',18,$GN,$WMC,
                Carbon::create(2024,7,24),Carbon::create(2024,7,26)),

            $make('BOATR-2024-M-106','ELBERTO','E.','CURAMPES',null,'Landayan',
                'FISHR-2024-068','NONOY','Motorized',
                5.41,0.55,0.43,'NITTO',7.5,$GN,$WMC,
                Carbon::create(2024,7,27),Carbon::create(2024,7,29)),

            $make('BOATR-2024-M-107','MAR','A.','LIMOSA',null,'Landayan',
                'FISHR-2024-069','MAR','Motorized',
                8.00,1.22,0.30,'VANGUARD',13,$GN,$WMC,
                Carbon::create(2024,7,30),Carbon::create(2024,8,1)),

            $make('BOATR-2024-M-108','AGNES','S.','LIMOSA',null,'Landayan',
                'FISHR-2024-070','AGNES','Motorized',
                8.00,1.22,0.34,'ISUZU',15,$GN,$WMC,
                Carbon::create(2024,8,2),Carbon::create(2024,8,4)),

            // Demetrio Moreno — same person, second boat (Landayan)
            $make('BOATR-2024-M-109','DEMETRIO','C.','MORENO',null,'Landayan',
                'FISHR-2024-017','DEMETRIO','Motorized',
                5.87,1.07,0.46,'HONDA',8,$GN,$WMC,
                Carbon::create(2024,8,5),Carbon::create(2024,8,7)),

            $make('BOATR-2024-M-110','JAYMEE','M.','LAZARO',null,'Landayan',
                'FISHR-2024-071','JAYMEE LAZARO','Motorized',
                8.30,0.90,0.57,'YAMADA',18,$GN,$WMC,
                Carbon::create(2024,8,8),Carbon::create(2024,8,10)),

            $make('BOATR-2024-M-111','ROBERTO','B.','RAGUIT',null,'Landayan',
                'FISHR-2024-072','JUN','Motorized',
                6.40,0.70,0.40,'YAMMA',18,$GN,$NMC,
                Carbon::create(2024,8,11),Carbon::create(2024,8,13)),

            $make('BOATR-2024-M-112','ROBERTO','B.','RAGUIT',null,'Landayan',
                'FISHR-2024-072','NORIEL','Motorized',
                6.07,0.50,0.32,'SHINMAX',7.5,$GN,$NMC,
                Carbon::create(2024,8,14),Carbon::create(2024,8,16)),

            $make('BOATR-2024-M-113','JASPER','I.','SARIO',null,'Landayan',
                'FISHR-2024-073','JASPER','Motorized',
                5.10,0.83,0.40,'SHINMAX',7.5,$GN,$NMC,
                Carbon::create(2024,8,17),Carbon::create(2024,8,19)),

            $make('BOATR-2024-M-114','MOHAMMAD MUSA','E.','AHMAD',null,'Landayan',
                'FISHR-2024-074','HORTON','Motorized',
                8.12,1.12,0.49,'KAMA',12,$GN,$NMC,
                Carbon::create(2024,8,20),Carbon::create(2024,8,22)),

            $make('BOATR-2024-M-115','ROBERTO','B.','RAGUIT',null,'Landayan',
                'FISHR-2024-072','JUN 3','Motorized',
                6.06,0.73,1.24,'MARINE',7.5,$GN,$NMC,
                Carbon::create(2024,8,23),Carbon::create(2024,8,25)),

            $make('BOATR-2024-M-116','ROBERTO','B.','RAGUIT',null,'Landayan',
                'FISHR-2024-072','JUN 4','Motorized',
                5.76,0.68,0.30,'YAMADA',7.5,$GN,$NMC,
                Carbon::create(2024,8,26),Carbon::create(2024,8,28)),

            $make('BOATR-2024-M-117','RONALD','A.','LIM',null,'Landayan',
                'FISHR-2024-075','MACOY','Motorized',
                6.67,0.77,0.67,'YAMMA',18,$GN,$NMC,
                Carbon::create(2024,8,29),Carbon::create(2024,8,31)),

            $make('BOATR-2024-M-118','JENNER','E.','AMAGO',null,'Landayan',
                'FISHR-2024-076','N/A','Motorized',
                5.45,0.40,0.76,'ROBIN',7.5,$GN,$NMC,
                Carbon::create(2024,9,1),Carbon::create(2024,9,3)),

            $make('BOATR-2024-M-119','JOSE','A.','TEMPROSA',null,'Landayan',
                'FISHR-2024-077','JOSE','Motorized',
                5.55,0.88,0.38,'MANTRA',16,$GN,$WMC,
                Carbon::create(2024,9,4),Carbon::create(2024,9,6)),

            $make('BOATR-2024-M-120','ALBIN','A.','ALVIAR',null,'Landayan',
                'FISHR-2024-078','KLIMA','Motorized',
                6.37,0.82,0.64,'BRIGGS STRATON',10,$GN,$NMC,
                Carbon::create(2024,9,7),Carbon::create(2024,9,9)),

            $make('BOATR-2024-M-121','FERDINAND','D.','GUILLERMO',null,'Landayan',
                'FISHR-2024-079','BONG','Motorized',
                6.30,0.90,0.31,'HONDA',7,$GN,$NMC,
                Carbon::create(2024,9,10),Carbon::create(2024,9,12)),

            $make('BOATR-2024-M-122','DARWIN','B.','CASINOS',null,'Landayan',
                'FISHR-2024-080','CEEJAY','Motorized',
                9.00,1.34,0.94,'ISUZU',221,$GN,$WMC,
                Carbon::create(2024,9,13),Carbon::create(2024,9,15)),

            // Ronald Curampez — 4th boat, same FishR number
            $make('BOATR-2024-M-123','RONALD','E.','CURAMPEZ',null,'Landayan',
                'FISHR-2024-011','KEEYAN&ANIQA','Motorized',
                11.22,1.20,0.70,'MITSUBISHI',50,$GN,$NMC,
                Carbon::create(2024,9,16),Carbon::create(2024,9,18)),

            $make('BOATR-2024-M-124','ROBERTO','B.','RAGUIT',null,'Landayan',
                'FISHR-2024-072','JUN 6','Motorized',
                5.76,0.67,0.55,'ROBIN',7.5,$GN,$NMC,
                Carbon::create(2024,9,19),Carbon::create(2024,9,21)),

            $make('BOATR-2024-M-125','ROBERTO','B.','RAGUIT',null,'Landayan',
                'FISHR-2024-072','JUN 5','Motorized',
                6.10,0.60,0.37,'EXTREME',5,$GN,$NMC,
                Carbon::create(2024,9,22),Carbon::create(2024,9,24)),

            $make('BOATR-2024-M-126','RANILLO','N.','SANTAÑEZ',null,'Landayan',
                'FISHR-2024-081','RANNY','Motorized',
                5.10,0.70,0.50,'LONTOP',7.5,$GN,$WMC,
                Carbon::create(2024,9,25),Carbon::create(2024,9,27)),

            $make('BOATR-2024-M-127','ARTEMIO','V.','ORTEGA',null,'Landayan',
                'FISHR-2024-082','KHYING/JINICA','Motorized',
                7.00,0.67,0.46,'YAMADA',7.5,$GN,$NMC,
                Carbon::create(2024,9,28),Carbon::create(2024,9,30)),

            $make('BOATR-2024-M-128','MELCHOR','R.','YARIS',null,'Landayan',
                'FISHR-2024-083','MV AUSTINE','Motorized',
                6.08,0.78,0.42,'BRIGGS STRATON',6,$GN,$NMC,
                Carbon::create(2024,10,1),Carbon::create(2024,10,3)),

            $make('BOATR-2024-M-130','GENARO','G.','DE BORJA',null,'Landayan',
                'FISHR-2024-084','GENER','Motorized',
                6.37,0.79,0.37,'NITO',7.5,$GN,$NMC,
                Carbon::create(2024,10,4),Carbon::create(2024,10,6)),

            $make('BOATR-2024-M-131','JONATHAN','S.','ESPARAGOZA',null,'Landayan',
                'FISHR-2024-085','MV JONATHAN','Motorized',
                7.50,0.50,0.40,'BRIGGS STRATON',16,$GN,$WMC,
                Carbon::create(2024,10,7),Carbon::create(2024,10,9)),

            // ==================================================================
            // 2024 NON-MOTORIZED  (FISHR-2024-086 … FISHR-2024-123)
            // ==================================================================

            $make('BOATR-2024-NM-022','JERRY','P.','URIARTE',null,'Landayan',
                'FISHR-2024-086','BUBOY','Non-motorized',
                3.67,0.74,0.27,null,null,$GN,$NMB,
                Carbon::create(2024,1,15),Carbon::create(2024,1,16)),

            $make('BOATR-2024-NM-025','ARIEL','S.','DELOS REYES SR.',null,'Landayan',
                'FISHR-2024-087','MV 4 BOYS','Non-motorized',
                6.30,0.88,0.88,null,null,$GN,$NMB,
                Carbon::create(2024,1,17),Carbon::create(2024,1,18)),

            $make('BOATR-2024-NM-026','DANILO','F.','HILARIO',null,'Landayan',
                'FISHR-2024-088','DANDOY','Non-motorized',
                6.00,0.72,0.31,null,null,$GN,$NMB,
                Carbon::create(2024,1,19),Carbon::create(2024,1,20)),

            $make('BOATR-2024-NM-028','RONALDO','E.','AJEDO',null,'Landayan',
                'FISHR-2024-089','RONALDO','Non-motorized',
                5.97,0.73,0.27,null,null,$GN,$NMB,
                Carbon::create(2024,1,21),Carbon::create(2024,1,22)),

            $make('BOATR-2024-NM-029','SILVERIO','P.','FLAMIANO',null,'Landayan',
                'FISHR-2024-090','BEYONG','Non-motorized',
                5.55,0.67,0.20,null,null,$GN,$NMB,
                Carbon::create(2024,1,23),Carbon::create(2024,1,24)),

            $make('BOATR-2024-NM-030','RONALDO','M.','LOS BAÑOS',null,'Landayan',
                'FISHR-2024-091','PADI','Non-motorized',
                5.76,0.50,0.27,null,null,$GN,$NMB,
                Carbon::create(2024,1,25),Carbon::create(2024,1,26)),

            $make('BOATR-2024-NM-031','MARCOS','B.','CORDIS',null,'Landayan',
                'FISHR-2024-092','MACOY','Non-motorized',
                6.68,0.61,0.27,null,null,$GN,$NMB,
                Carbon::create(2024,1,27),Carbon::create(2024,1,28)),

            $make('BOATR-2024-NM-032','ARMANDO','G.','BAUSO',null,'Landayan',
                'FISHR-2024-093','NONG NONG','Non-motorized',
                6.37,0.58,0.27,null,null,$GN,$NMB,
                Carbon::create(2024,1,29),Carbon::create(2024,1,30)),

            $make('BOATR-2024-NM-033','ALEJANDRO','G.','BAUSO',null,'Landayan',
                'FISHR-2024-094','ANDREW','Non-motorized',
                5.15,0.58,0.20,null,null,$GN,$NMB,
                Carbon::create(2024,1,31),Carbon::create(2024,2,1)),

            $make('BOATR-2024-NM-034','ENRICO','G.','DELOS REYES JR.',null,'Landayan',
                'FISHR-2024-095','ENRICO JR.','Non-motorized',
                5.55,0.85,0.27,null,null,$GN,$NMB,
                Carbon::create(2024,2,2),Carbon::create(2024,2,3)),

            $make('BOATR-2024-NM-040','VIRGILIO','S.','AVELINA',null,'Landayan',
                'FISHR-2024-096','BUDHA','Non-motorized',
                6.07,0.61,0.31,null,null,$GN,$NMB,
                Carbon::create(2024,2,4),Carbon::create(2024,2,5)),

            $make('BOATR-2024-NM-041','JAY-AR','V.','AVELINA',null,'Landayan',
                'FISHR-2024-097','JR','Non-motorized',
                6.05,0.73,0.77,null,null,$GN,$NMB,
                Carbon::create(2024,2,6),Carbon::create(2024,2,7)),

            $make('BOATR-2024-NM-043','ALEXANDER','B.','CARAN',null,'Cuyab',
                'FISHR-2024-098','N/A','Non-motorized',
                5.12,0.73,0.36,null,null,$GN,$NMB,
                Carbon::create(2024,2,8),Carbon::create(2024,2,9)),

            $make('BOATR-2024-NM-047','ROBERTO','R.','ALON-ALON',null,'Landayan',
                'FISHR-2024-099','BERTO','Non-motorized',
                5.75,0.48,0.23,null,null,$GN,$NMB,
                Carbon::create(2024,2,10),Carbon::create(2024,2,11)),

            $make('BOATR-2024-NM-051','BRIAN','P.','NAZARENO',null,'Landayan',
                'FISHR-2024-100','BRAVO','Non-motorized',
                6.00,0.72,0.31,null,null,$GN,$NMB,
                Carbon::create(2024,2,12),Carbon::create(2024,2,13)),

            $make('BOATR-2024-NM-052','LAURO','O.','VIERNEZA',null,'Landayan',
                'FISHR-2024-101','LAURO','Non-motorized',
                5.15,0.48,0.45,null,null,$GN,$NMB,
                Carbon::create(2024,2,14),Carbon::create(2024,2,15)),

            $make('BOATR-2024-NM-053','EFREN','M.','VERGARA',null,'Cuyab',
                'FISHR-2024-102','EFREN','Non-motorized',
                6.98,0.70,0.35,null,null,$NA,$NMB,
                Carbon::create(2024,2,16),Carbon::create(2024,2,17)),

            $make('BOATR-2024-NM-054','NICANOR','A.','BERON',null,'Landayan',
                'FISHR-2024-103','NICK','Non-motorized',
                5.10,0.68,0.16,null,null,$GN,$NMB,
                Carbon::create(2024,2,18),Carbon::create(2024,2,19)),

            $make('BOATR-2024-NM-055','EDGAR','M.','NAVALES',null,'Landayan',
                'FISHR-2024-104','EGAY','Non-motorized',
                5.00,0.70,0.30,null,null,$GN,$NMB,
                Carbon::create(2024,2,20),Carbon::create(2024,2,21)),

            // Eduardo Gonzales also has a motorized boat (FISHR-2024-039)
            $make('BOATR-2024-NM-056','EDUARDO','E.','GONZALES',null,'Landayan',
                'FISHR-2024-039','EDUARDO NM','Non-motorized',
                5.48,0.90,0.30,null,null,$GN,$NMB,
                Carbon::create(2024,2,22),Carbon::create(2024,2,23)),

            $make('BOATR-2024-NM-057','BENJAMIN','S.','BRICENIO',null,'Landayan',
                'FISHR-2024-105','BEN','Non-motorized',
                6.20,0.73,0.34,null,null,$GN,$NMB,
                Carbon::create(2024,2,24),Carbon::create(2024,2,25)),

            $make('BOATR-2024-NM-059','EMMANUEL','P.','CORPUZ',null,'Landayan',
                'FISHR-2024-106','N/A','Non-motorized',
                5.80,0.62,0.27,null,null,$GN,$NMB,
                Carbon::create(2024,2,26),Carbon::create(2024,2,27)),

            $make('BOATR-2024-NM-065','GERARDO','A.','ALMEIDA',null,'Landayan',
                'FISHR-2024-107','GERRY','Non-motorized',
                6.10,0.73,0.33,null,null,$GN,$NMB,
                Carbon::create(2024,2,28),Carbon::create(2024,2,29)),

            $make('BOATR-2024-NM-070','ROMEO','B.','ALMEIDA',null,'Landayan',
                'FISHR-2024-108','ROMEO','Non-motorized',
                5.50,0.70,0.30,null,null,$GN,$NMB,
                Carbon::create(2024,3,1),Carbon::create(2024,3,2)),

            $make('BOATR-2024-NM-078','ONOPRE','V.','DELA CRUZ',null,'Landayan',
                'FISHR-2024-109','OPENG','Non-motorized',
                5.15,0.48,0.45,null,null,$GN,$NMB,
                Carbon::create(2024,3,3),Carbon::create(2024,3,4)),

            // Aquilino Yambao — also has motorized boat (FISHR-2024-064)
            $make('BOATR-2024-NM-080','AQUILINO','L.','YAMBAO',null,'Landayan',
                'FISHR-2024-064','PJ','Non-motorized',
                6.95,0.78,0.43,null,null,$GN,$NMB,
                Carbon::create(2024,3,5),Carbon::create(2024,3,6)),

            $make('BOATR-2024-NM-081','VIRGILIO','V.','CASULLA',null,'Cuyab',
                'FISHR-2024-110','VER','Non-motorized',
                6.50,0.62,0.38,null,null,$GN,$NMB,
                Carbon::create(2024,3,7),Carbon::create(2024,3,8)),

            $make('BOATR-2024-NM-082','ALMARIO','C.','PERBER',null,'Landayan',
                'FISHR-2024-111','N/A','Non-motorized',
                8.50,0.62,0.25,null,null,$GN,$NMB,
                Carbon::create(2024,3,9),Carbon::create(2024,3,10)),

            $make('BOATR-2024-NM-083','CARLITO','O.','AVELINA',null,'Landayan',
                'FISHR-2024-112','N/A','Non-motorized',
                5.40,0.31,0.61,null,null,$GN,$NMB,
                Carbon::create(2024,3,11),Carbon::create(2024,3,12)),

            $make('BOATR-2024-NM-084','ROGELIO','R.','ANDRADA',null,'Landayan',
                'FISHR-2024-113','N/A','Non-motorized',
                5.67,0.30,0.61,null,null,$GN,$NMB,
                Carbon::create(2024,3,13),Carbon::create(2024,3,14)),

            $make('BOATR-2024-NM-087','MARVIC','B.','MEJIAS',null,'Cuyab',
                'FISHR-2024-114','VIC 1','Non-motorized',
                6.67,0.67,0.27,null,null,$GN,$NMB,
                Carbon::create(2024,3,15),Carbon::create(2024,3,16)),

            $make('BOATR-2024-NM-088','MARVIC','B.','MEJIAS',null,'Cuyab',
                'FISHR-2024-114','VIC 2','Non-motorized',
                5.15,0.58,0.27,null,null,$GN,$NMB,
                Carbon::create(2024,3,17),Carbon::create(2024,3,18)),

            $make('BOATR-2024-NM-089','VENANCIO','O.','AVELINA',null,'Landayan',
                'FISHR-2024-115','N/A','Non-motorized',
                6.40,0.70,0.31,null,null,$GN,$NMB,
                Carbon::create(2024,3,19),Carbon::create(2024,3,20)),

            $make('BOATR-2024-NM-090','LARRY','C.','MARQUEZ',null,'Landayan',
                'FISHR-2024-116','N/A','Non-motorized',
                6.00,0.70,0.32,null,null,$GN,$NMB,
                Carbon::create(2024,3,21),Carbon::create(2024,3,22)),

            $make('BOATR-2024-NM-092','ROMANO','B.','AVELINA',null,'Landayan',
                'FISHR-2024-117','OMAN','Non-motorized',
                6.06,0.72,0.27,null,null,$GN,$NMB,
                Carbon::create(2024,3,23),Carbon::create(2024,3,24)),

            $make('BOATR-2024-NM-093','JAIME','R.','ANDRADA',null,'Landayan',
                'FISHR-2024-118','N/A','Non-motorized',
                5.20,0.70,0.28,null,null,$GN,$NMB,
                Carbon::create(2024,3,25),Carbon::create(2024,3,26)),

            $make('BOATR-2024-NM-096','RICO','L.','PASCASIO',null,'Landayan',
                'FISHR-2024-119','RICO L','Non-motorized',
                6.06,0.67,0.31,null,null,$GN,$NMB,
                Carbon::create(2024,3,27),Carbon::create(2024,3,28)),

            // Graciano Insorio — also has motorized boat (FISHR-2024-063)
            $make('BOATR-2024-NM-098','GRACIANO','R.','INSORIO',null,'Landayan',
                'FISHR-2024-063','N/A','Non-motorized',
                5.58,0.82,0.27,null,null,$GN,$NMB,
                Carbon::create(2024,3,29),Carbon::create(2024,3,30)),

            $make('BOATR-2024-NM-100','VLADIMIR','M.','ALAIZA',null,'Landayan',
                'FISHR-2024-120','LOLO UWENG','Non-motorized',
                6.40,0.74,0.31,null,null,$FC,$NMB,
                Carbon::create(2024,3,31),Carbon::create(2024,4,1)),

            $make('BOATR-2024-NM-103','ALMARIO','V.','VIDAL JR',null,'Landayan',
                'FISHR-2024-121','JEALM','Non-motorized',
                5.75,0.58,0.28,null,null,$GN,$NMB,
                Carbon::create(2024,4,2),Carbon::create(2024,4,3)),

            $make('BOATR-2024-NM-105','ROMEO','A.','VIERNEZA JR',null,'Landayan',
                'FISHR-2024-122','N/A','Non-motorized',
                5.00,0.80,0.90,null,null,$GN,$NMB,
                Carbon::create(2024,4,4),Carbon::create(2024,4,5)),

            $make('BOATR-2024-NM-129','ALBERTO','G.','IZON',null,'Landayan',
                'FISHR-2024-123','AMBET','Non-motorized',
                3.93,0.67,0.37,null,null,$GN,$NMB,
                Carbon::create(2024,4,6),Carbon::create(2024,4,7)),

            // ==================================================================
            // 2025 NON-MOTORIZED  (FISHR-2025-011, 025, 039…051)
            // ==================================================================

            // Jojie Catalon — FISHR-2025-011 (non-boat fisherfolk section)
            $make('BOATR-2025-NM-134',null,null,null,null,'Landayan',
                'FISHR-2025-011','OPAG','Non-motorized',
                7.28,0.88,0.27,null,null,$GN,$NMB,
                Carbon::create(2025,1,10),Carbon::create(2025,1,12)),

            $make('BOATR-2025-NM-136',null,null,null,null,'Landayan',
                'FISHR-2025-039','EBO','Non-motorized',
                5.76,0.73,0.27,null,null,$GN,$NMB,
                Carbon::create(2025,1,13),Carbon::create(2025,1,15)),

            $make('BOATR-2025-NM-137',null,null,null,null,'Landayan',
                'FISHR-2025-040','MAR','Non-motorized',
                4.84,0.73,0.27,null,null,$GN,$NMB,
                Carbon::create(2025,1,16),Carbon::create(2025,1,18)),

            $make('BOATR-2025-NM-138',null,null,null,null,'Landayan',
                'FISHR-2025-041','GERRY','Non-motorized',
                5.97,0.78,0.32,null,null,$GN,$NMB,
                Carbon::create(2025,1,19),Carbon::create(2025,1,21)),

            // Rasid Jacaria — FISHR-2025-025 (non-boat section)
            $make('BOATR-2025-NM-144',null,null,null,null,'Landayan',
                'FISHR-2025-025','SHAWY-AJ 3','Non-motorized',
                7.89,1.03,0.42,null,null,$NA,$NMB,
                Carbon::create(2025,1,22),Carbon::create(2025,1,24)),

            $make('BOATR-2025-NM-147',null,null,null,null,'Landayan',
                'FISHR-2025-042','ORLY','Non-motorized',
                5.40,0.61,0.27,null,null,$GN,$NMB,
                Carbon::create(2025,1,25),Carbon::create(2025,1,27)),

            $make('BOATR-2025-NM-155',null,null,null,null,'Landayan',
                'FISHR-2025-043','TOTO','Non-motorized',
                5.76,0.61,0.54,null,null,$GN,$NMB,
                Carbon::create(2025,1,28),Carbon::create(2025,1,30)),

            $make('BOATR-2025-NM-156',null,null,null,null,'Landayan',
                'FISHR-2025-044','ALVIN','Non-motorized',
                5.75,0.64,0.27,null,null,$GN,$NMB,
                Carbon::create(2025,1,31),Carbon::create(2025,2,2)),

            $make('BOATR-2025-NM-159',null,null,null,null,'Landayan',
                'FISHR-2025-045','WILLY','Non-motorized',
                5.76,0.61,0.31,null,null,$GN,$NMB,
                Carbon::create(2025,2,3),Carbon::create(2025,2,5)),

            $make('BOATR-2025-NM-160',null,null,null,null,'Landayan',
                'FISHR-2025-046','ELMER 1','Non-motorized',
                5.20,0.85,0.30,null,null,$GN,$NMB,
                Carbon::create(2025,2,6),Carbon::create(2025,2,8)),

            $make('BOATR-2025-NM-164',null,null,null,null,'Landayan',
                'FISHR-2025-047','BARBERO','Non-motorized',
                6.10,0.76,0.40,null,null,$GN,$NMB,
                Carbon::create(2025,2,9),Carbon::create(2025,2,11)),

            $make('BOATR-2025-NM-165',null,null,null,null,'Landayan',
                'FISHR-2025-048','VINCENT','Non-motorized',
                5.76,0.54,0.51,null,null,$GN,$NMB,
                Carbon::create(2025,2,12),Carbon::create(2025,2,14)),

            $make('BOATR-2025-NM-166',null,null,null,null,'Landayan',
                'FISHR-2025-049','ROMY','Non-motorized',
                6.67,0.67,0.20,null,null,$GN,$NMB,
                Carbon::create(2025,2,15),Carbon::create(2025,2,17)),

            $make('BOATR-2025-NM-168',null,null,null,null,'Landayan',
                'FISHR-2025-050','AKI','Non-motorized',
                5.76,0.64,0.34,null,null,$GN,$NMB,
                Carbon::create(2025,2,18),Carbon::create(2025,2,20)),

            // Francisco Canillias — FISHR-2025-010 (non-boat section)
            $make('BOATR-2025-NM-169',null,null,null,null,'Landayan',
                'FISHR-2025-010','COWBOY','Non-motorized',
                5.15,0.57,0.27,null,null,$GN,$NMB,
                Carbon::create(2025,2,21),Carbon::create(2025,2,23)),

            // Alberto Francisco — FISHR-2025-018 (non-boat section)
            $make('BOATR-2025-NM-170',null,null,null,null,'Landayan',
                'FISHR-2025-018','MACHETE','Non-motorized',
                3.02,0.58,0.43,null,null,$GN,$NMB,
                Carbon::create(2025,2,24),Carbon::create(2025,2,26)),

            $make('BOATR-2025-NM-171',null,null,null,null,'Landayan',
                'FISHR-2025-051','THUNDER','Non-motorized',
                5.76,0.48,0.39,null,null,$GN,$NMB,
                Carbon::create(2025,2,27),Carbon::create(2025,3,1)),

            // ==================================================================
            // 2025 MOTORIZED  (FISHR-2025-052 … FISHR-2025-068 + shared numbers)
            // All names resolved from linked FishrApplication (fn=null)
            // ==================================================================

            $make('BOATR-2025-M-001',null,null,null,null,'Landayan',
                'FISHR-2025-052','TOTO','Motorized',
                5.98,0.31,0.34,'MITSUBISHI',8,$GN,$WMC,
                Carbon::create(2025,1,5),Carbon::create(2025,1,7)),

            $make('BOATR-2025-M-002',null,null,null,null,'Landayan',
                'FISHR-2025-053','CRISTOPHER','Motorized',
                7.30,1.25,0.30,'BRIGGS STRATON',10,$NA,$NMC,
                Carbon::create(2025,1,8),Carbon::create(2025,1,10)),

            $make('BOATR-2025-M-003',null,null,null,null,'Landayan',
                'FISHR-2025-054','BLAN','Motorized',
                5.45,0.88,0.42,'SHINMAX',7.5,$GN,$NMC,
                Carbon::create(2025,1,11),Carbon::create(2025,1,13)),

            $make('BOATR-2025-M-004',null,null,null,null,'Landayan',
                'FISHR-2025-055','SWR','Motorized',
                8.50,1.18,0.57,'YAMAHA',16,$NA,$NMC,
                Carbon::create(2025,2,10),Carbon::create(2025,2,15)),

            $make('BOATR-2025-M-005',null,null,null,null,'Landayan',
                'FISHR-2025-056','BUSHRA 1','Motorized',
                7.74,1.03,0.57,'MEGA',16,$NA,$NMC,
                Carbon::create(2025,2,13),Carbon::create(2025,2,18)),

            $make('BOATR-2025-M-006',null,null,null,null,'Landayan',
                'FISHR-2025-056','BUSHRA 2','Motorized',
                7.89,1.18,0.57,'SUMO',16,$NA,$NMC,
                Carbon::create(2025,2,16),Carbon::create(2025,2,21)),

            // Rasid Jacaria — motorized boats, same FishR-2025-025
            $make('BOATR-2025-M-007',null,null,null,null,'Landayan',
                'FISHR-2025-025','SHAWY-AJ 1','Motorized',
                8.50,1.79,0.73,'ISUZU',95,$NA,$NMC,
                Carbon::create(2025,2,19),Carbon::create(2025,2,24)),

            $make('BOATR-2025-M-008',null,null,null,null,'Landayan',
                'FISHR-2025-025','SHAWY-AJ 2','Motorized',
                7.89,1.03,0.42,'YAMMA',16,$NA,$NMC,
                Carbon::create(2025,2,22),Carbon::create(2025,2,27)),

            $make('BOATR-2025-M-009',null,null,null,null,'Landayan',
                'FISHR-2025-057','TOTOH','Motorized',
                8.50,1.18,0.57,'SUMO',15,$NA,$NMC,
                Carbon::create(2025,3,1),Carbon::create(2025,3,5)),

            $make('BOATR-2025-M-010',null,null,null,null,'Landayan',
                'FISHR-2025-058','BHIJAY','Motorized',
                7.89,1.18,0.42,'YAMMA',18,$NA,$NMC,
                Carbon::create(2025,3,6),Carbon::create(2025,3,10)),

            $make('BOATR-2025-M-011',null,null,null,null,'Landayan',
                'FISHR-2025-059','MANNY','Motorized',
                5.90,0.60,0.42,'SUMO',7,$GN,$NMC,
                Carbon::create(2025,3,11),Carbon::create(2025,3,15)),

            $make('BOATR-2025-M-012',null,null,null,null,'Landayan',
                'FISHR-2025-060','ATAN','Motorized',
                6.06,0.63,0.63,'YAMADA',16,$GN,$NMC,
                Carbon::create(2025,3,16),Carbon::create(2025,3,20)),

            // Christopher Avelina — 2nd boat, same FishR-2025-053
            $make('BOATR-2025-M-013',null,null,null,null,'Landayan',
                'FISHR-2025-053','TOPHER','Motorized',
                6.06,1.03,0.57,'YAMMA',10,$NA,$NMC,
                Carbon::create(2025,3,21),Carbon::create(2025,3,25)),

            $make('BOATR-2025-M-014',null,null,null,null,'Landayan',
                'FISHR-2025-061','SHELVIE ANN','Motorized',
                4.80,0.80,0.45,'MARPRO',7,$GN,$WMC,
                Carbon::create(2025,3,26),Carbon::create(2025,3,30)),

            $make('BOATR-2025-M-015',null,null,null,null,'Landayan',
                'FISHR-2025-062','ELBRANDO','Motorized',
                6.68,1.16,0.49,'NITTO',7.5,$GN,$WMC,
                Carbon::create(2025,4,1),Carbon::create(2025,4,5)),

            $make('BOATR-2025-M-016',null,null,null,null,'Landayan',
                'FISHR-2025-063','AMI','Motorized',
                6.40,0.73,0.33,'SHINMAX',7.5,$GN,$NMC,
                Carbon::create(2025,4,6),Carbon::create(2025,4,10)),

            $make('BOATR-2025-M-017',null,null,null,null,'Landayan',
                'FISHR-2025-064','MB JER-LETH','Motorized',
                8.20,0.50,0.47,'YAMADA',18,$GN,$NMC,
                Carbon::create(2025,4,11),Carbon::create(2025,4,15)),

            $make('BOATR-2025-M-018',null,null,null,null,'Landayan',
                'FISHR-2025-065','CALIX','Motorized',
                5.45,0.30,0.57,'JAPAN TECHNOLOGY',16,$GN,$NMC,
                Carbon::create(2025,4,16),Carbon::create(2025,4,20)),

            // Jimson Bachecha — FISHR-2024-056, second boat in 2025
            $make('BOATR-2025-M-019',null,null,null,null,'Landayan',
                'FISHR-2024-056','ELISON 2','Motorized',
                7.59,0.87,0.57,'YAMADA',16,$GN,$NMC,
                Carbon::create(2025,4,21),Carbon::create(2025,4,25)),

            $make('BOATR-2025-M-020',null,null,null,null,'Landayan',
                'FISHR-2025-066','ARTUR','Motorized',
                5.18,0.37,0.37,'YAMADA',7.5,$GN,$NMC,
                Carbon::create(2025,4,26),Carbon::create(2025,4,30)),

            $make('BOATR-2025-M-021',null,null,null,null,'Landayan',
                'FISHR-2025-067','NORMAN','Motorized',
                9.41,0.87,0.69,'ISUZU C240',300,$FC,$NMC,
                Carbon::create(2025,5,1),Carbon::create(2025,5,5)),

            $make('BOATR-2025-M-022',null,null,null,null,'Landayan',
                'FISHR-2025-068','MARK ANGELO','Motorized',
                5.76,0.73,0.50,'KTEC',16,$GN,$NMC,
                Carbon::create(2025,5,6),Carbon::create(2025,5,10)),

            // ==================================================================
            // 2026 NON-MOTORIZED
            // ==================================================================

            // Jernie Toldanes — FISHR-2026-005 (non-boat section)
            $make('BOATR-2026-NM-176',null,null,null,null,'Landayan',
                'FISHR-2026-005','CALINOG','Non-motorized',
                5.45,0.77,0.32,null,null,$GN,$NMB,
                Carbon::create(2026,2,8),Carbon::create(2026,2,10)),

            // ==================================================================
            // 2026 MOTORIZED  (FISHR-2026-006 … FISHR-2026-012)
            // ==================================================================

            $make('BOATR-2026-M-001',null,null,null,null,'Landayan',
                'FISHR-2026-006','3R','Motorized',
                7.59,0.88,0.73,'BRIGGS STRATON',18,$GN,$NMC,
                Carbon::create(2026,1,5),Carbon::create(2026,1,8)),

            $make('BOATR-2026-M-002',null,null,null,null,'Landayan',
                'FISHR-2026-007','MV EDDIE','Motorized',
                6.85,0.70,0.40,'ROBIN',5,$GN,$NMC,
                Carbon::create(2026,1,10),Carbon::create(2026,1,13)),

            $make('BOATR-2026-M-003',null,null,null,null,'Landayan',
                'FISHR-2026-008','TOTENG','Motorized',
                5.41,0.70,0.43,'BRIGGS STRATON',16,$GN,$NMC,
                Carbon::create(2026,1,15),Carbon::create(2026,1,18)),

            $make('BOATR-2026-M-004',null,null,null,null,'Landayan',
                'FISHR-2026-009','FORTE','Motorized',
                6.10,0.69,0.30,'MOTORSTAR',6.5,$GN,$NMC,
                Carbon::create(2026,1,20),Carbon::create(2026,1,23)),

            $make('BOATR-2026-M-005',null,null,null,null,'Landayan',
                'FISHR-2026-010','ZALDY','Motorized',
                7.28,0.72,0.64,'KEMBO',8,$GN,$NMC,
                Carbon::create(2026,1,25),Carbon::create(2026,1,28)),

            $make('BOATR-2026-M-006',null,null,null,null,'Landayan',
                'FISHR-2026-011','LEO VER','Motorized',
                6.24,0.85,0.51,'KAWASAKI',7.5,$GN,$WMC,
                Carbon::create(2026,1,30),Carbon::create(2026,2,2)),

            $make('BOATR-2026-M-007',null,null,null,null,'Landayan',
                'FISHR-2026-012','MACOY 2','Motorized',
                5.10,0.73,0.27,'ROBIN',5,$GN,$NMC,
                Carbon::create(2026,2,5),Carbon::create(2026,2,8)),
        ];

        // ==================================================================
        // Persist records
        // ==================================================================
        $createdCount  = 0;
        $updatedCount  = 0;
        $linkedCount   = 0;
        $unlinkedCount = 0;

        foreach ($boatData as $data) {
            if (!empty($data['fishr_application_id'])) {
                $linkedCount++;
                $this->command->info("✓ Linked FishR {$data['fishr_number']} (id={$data['fishr_application_id']}) → {$data['application_number']}");
            } else {
                $this->command->warn("⚠ FishR {$data['fishr_number']} not found in fishr_applications — stored as-is for {$data['application_number']}");
                $unlinkedCount++;
            }

            $existing = BoatrApplication::where('application_number', $data['application_number'])->first();
            if ($existing) {
                $existing->update($data);
                $updatedCount++;
            } else {
                BoatrApplication::create($data);
                $createdCount++;
            }
        }

        $total = $createdCount + $updatedCount;
        $this->command->newLine();
        $this->command->info('BoatrRegisteredSeeder completed successfully!');
        $this->command->info("Records created  : {$createdCount}");
        $this->command->info("Records updated  : {$updatedCount}");
        $this->command->info("Total seeded     : {$total}");
        $this->command->info("FishR linked     : {$linkedCount}");
        $this->command->info("FishR unlinked   : {$unlinkedCount}");
        $this->command->info('Total in DB      : ' . BoatrApplication::count());
    }
}