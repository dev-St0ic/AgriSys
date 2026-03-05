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
     * Fishing gear mapping from spreadsheet:
     *   Gillnet / GILLNET  →  'Bottom Set Gill Net'
     *   N/A                →  'Not Applicable'
     *   Baklad             →  'Fish Coral'
     *   BUBO               →  'Not Applicable'   (unrecognised → closest null-safe)
     *
     * Barangay mapping from BOATR/LG number prefix:
     *   LAG-14-xxx          →  Landayan
     *   LG-SP-xxx / LAG-SP-xxx / LAG-SP-298/303/304 → Cuyab
     */
    public function run(): void
    {
        $this->command->info('Starting BoatrRegisteredSeeder …');

        $existingFishrNumbers = FishrApplication::pluck('fishr_number')->toArray();
        $this->command->info('Found ' . count($existingFishrNumbers) . ' existing FishR records in DB.');

        $fishrExists = fn($n) => !empty($n) && in_array($n, $existingFishrNumbers);

        /**
         * Build one boat record.
         *
         * $gear  : raw spreadsheet value → already converted to enum string before passing in
         * $hp    : integer or null
         */
        $make = function (
            string  $appNo,
            ?string $fn, ?string $mn, ?string $ln, ?string $ext,
            string  $barangay,
            ?string $fishrNo,
            ?string $vessel,
            string  $class,          // 'Motorized' | 'Non-motorized'
            ?float  $tl, ?float $tb, ?float $td,
            ?string $engineType, $hp, // int|null
            string  $gear,            // enum value
            string  $remarks,
            Carbon  $createdAt,
            Carbon  $approvedAt
        ): array {
            return [
                'application_number'          => $appNo,
                'first_name'                  => $fn,
                'middle_name'                 => $mn,
                'last_name'                   => $ln,
                'name_extension'              => $ext,
                'contact_number'              => null,
                'barangay'                    => $barangay,
                'fishr_number'                => $fishrNo,
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
                'inspection_completed'        => false,
                'inspection_date'             => null,
                'inspection_notes'            => null,
                'inspected_by'                => null,
                'documents_verified'          => true,
                'documents_verified_at'       => $approvedAt,
                'document_verification_notes' => 'Documents verified',
                'status'                      => 'approved',
                'remarks'                     => $remarks,
                'reviewed_at'                 => $approvedAt,
                'reviewed_by'                 => 1,
                'status_history'              => json_encode([
                    ['status' => 'pending',      'timestamp' => $createdAt->toDateTimeString(),                  'notes' => 'Application submitted'],
                    ['status' => 'under_review', 'timestamp' => $createdAt->copy()->addDay()->toDateTimeString(), 'notes' => 'Under review'],
                    ['status' => 'approved',     'timestamp' => $approvedAt->toDateTimeString(),                 'notes' => 'Approved'],
                ]),
                'inspection_scheduled_at'     => null,
                'approved_at'                 => $approvedAt,
                'rejected_at'                 => null,
                'created_at'                  => $createdAt,
                'updated_at'                  => $approvedAt,
            ];
        };

        // Short aliases for gear strings
        $GN  = 'Bottom Set Gill Net'; // Gillnet
        $FC  = 'Fish Coral';          // Baklad
        $NA  = 'Not Applicable';      // N/A  or  unrecognised

        // Short aliases for remarks
        $WMC = 'with maritime clearance';
        $NMC = 'no maritime clearance';
        $NMB = 'Registered non-motorized fishing boat';

        $boatData = [

            // ==================================================================
            // 2024 MOTORIZED  — source: "REGISTERED MOTORIZED FISHING BOAT 2024"
            // TL/TB/TD from spreadsheet columns TL, TB, TD
            // Barangay: LG-SP-xxx/LAG-SP-xxx → Cuyab ; LAG-14-xxx → Landayan
            // ==================================================================

            // CN-001  TEODORO CLEMENTE  LAG-SP-304/A → Cuyab  Gillnet
            $make('BOATR-2024-M-001','TEODORO',null,'CLEMENTE',null,'Cuyab',
                '2024-043425000-00664','ATO','Motorized',
                6.98,0.95,0.46,'BS HP 16',16,$GN,$NMC,
                Carbon::create(2024,1,14),Carbon::create(2024,1,16)),

            // CN-002  RONALD E. CURAMPEZ  LG-SP-305 → Cuyab  N/A
            $make('BOATR-2024-M-002','RONALD','E.','CURAMPEZ',null,'Cuyab',
                '11-043425000-00632','TING 1','Motorized',
                5.69,1.37,0.51,'YAMADA',10,$NA,$WMC,
                Carbon::create(2024,1,17),Carbon::create(2024,1,19)),

            // CN-003  RONALD E. CURAMPEZ  LG-SP-306 → Cuyab  N/A
            $make('BOATR-2024-M-003','RONALD','E.','CURAMPEZ',null,'Cuyab',
                '11-043425000-00632','TING 2','Motorized',
                7.96,1.16,0.46,'KAMA',12,$NA,$WMC,
                Carbon::create(2024,1,20),Carbon::create(2024,1,22)),

            // CN-004  ARTEMIO B. GUAB  LG-SP-307 → Cuyab  N/A
            $make('BOATR-2024-M-004','ARTEMIO','B.','GUAB',null,'Cuyab',
                '11-043425000-00603','RICO 4','Motorized',
                6.70,1.25,0.55,'ALATA',10,$NA,$WMC,
                Carbon::create(2024,1,23),Carbon::create(2024,1,25)),

            // CN-005  ARTEMIO B. GUAB  LG-SP-308 → Cuyab  N/A
            $make('BOATR-2024-M-005','ARTEMIO','B.','GUAB',null,'Cuyab',
                '11-043425000-00603','RICO 5','Motorized',
                9.32,1.82,1.21,'MITSUBISHI',500,$NA,$WMC,
                Carbon::create(2024,1,26),Carbon::create(2024,1,28)),

            // CN-006  ARTEMIO B. GUAB  LG-SP-309 → Cuyab  N/A
            $make('BOATR-2024-M-006','ARTEMIO','B.','GUAB',null,'Cuyab',
                '11-043425000-00603','RICO 10','Motorized',
                8.56,1.67,0.64,'4DR5',300,$NA,$WMC,
                Carbon::create(2024,1,29),Carbon::create(2024,1,31)),

            // CN-007  CHRISTIAN P. MAGO  LG-SP-310 → Cuyab  N/A
            $make('BOATR-2024-M-007','CHRISTIAN','P.','MAGO',null,'Cuyab',
                '27-043425000-00361','RICO 2','Motorized',
                6.88,1.12,0.39,'YAMADA',18,$NA,$WMC,
                Carbon::create(2024,2,1),Carbon::create(2024,2,3)),

            // CN-008  FREDDIE H. JUANERIO  LG-SP-311 → Cuyab  N/A
            $make('BOATR-2024-M-008','FREDDIE','H.','JUANERIO',null,'Cuyab',
                '03-043425000-00518','RICO 7','Motorized',
                8.50,1.18,0.65,'C240',300,$NA,$WMC,
                Carbon::create(2024,2,4),Carbon::create(2024,2,6)),

            // CN-009  DARYL ILAGAN  LG-SP-312 → Cuyab  Gillnet
            $make('BOATR-2024-M-009','DARYL',null,'ILAGAN',null,'Cuyab',
                '2024-043425000-00668','MV DARYL','Motorized',
                6.10,0.82,0.51,'ROBIN',8,$GN,$NMC,
                Carbon::create(2024,2,7),Carbon::create(2024,2,9)),

            // CN-010  CRISPIN A. VILLADIEGO  LAG-14-151 → Landayan  Gillnet
            $make('BOATR-2024-M-010','CRISPIN','A.','VILLADIEGO',null,'Landayan',
                '11-043425000-00634','CRISPIN','Motorized',
                6.83,1.00,0.55,'YAMADA',10,$GN,$NMC,
                Carbon::create(2024,2,10),Carbon::create(2024,2,12)),

            // CN-011  DEMETRIO C. MORENO  LG-SP-313 → Cuyab  N/A
            $make('BOATR-2024-M-011','DEMETRIO','C.','MORENO',null,'Cuyab',
                '11-043425000-00640','RICO 3','Motorized',
                8.23,0.88,0.39,'MODEL KM186(A)',10,$NA,$WMC,
                Carbon::create(2024,2,13),Carbon::create(2024,2,15)),

            // CN-012  RYAN B. ANGELES  LG-SP-314 → Cuyab  N/A
            $make('BOATR-2024-M-012','RYAN','B.','ANGELES',null,'Cuyab',
                '2024-043425000-00665','RYAN','Motorized',
                6.94,1.19,0.31,'YAMADA',10,$NA,$WMC,
                Carbon::create(2024,2,16),Carbon::create(2024,2,18)),

            // CN-013  EDGAR A. ALAMO  LG-SP-315 → Cuyab  Gillnet
            $make('BOATR-2024-M-013','EDGAR','A.','ALAMO',null,'Cuyab',
                '2024-043425000-00669','ALAMO','Motorized',
                7.01,0.94,0.45,'BRIGGS STRATON',16,$GN,$NMC,
                Carbon::create(2024,2,19),Carbon::create(2024,2,21)),

            // CN-014  MARIANO M. MACALINAO  LG-SP-316 → Cuyab  Gillnet
            $make('BOATR-2024-M-014','MARIANO','M.','MACALINAO',null,'Cuyab',
                '16-043425000-00545','MV NANING','Motorized',
                8.57,0.70,0.70,'JAPAN',18,$GN,$WMC,
                Carbon::create(2024,2,22),Carbon::create(2024,2,24)),

            // CN-015  RICO O. MACHADO  LG-SP-317 → Cuyab  N/A
            $make('BOATR-2024-M-015','RICO','O.','MACHADO',null,'Cuyab',
                '05-043425000-00655','RICO 1','Motorized',
                6.98,1.18,1.18,'YAMADA',25,$NA,$WMC,
                Carbon::create(2024,2,25),Carbon::create(2024,2,27)),

            // CN-016  RICO O. MACHADO  LG-SP-318 → Cuyab  N/A
            $make('BOATR-2024-M-016','RICO','O.','MACHADO',null,'Cuyab',
                '05-043425000-00655','RICO 8','Motorized',
                8.53,1.20,1.18,'HYUNDAI',300,$NA,$WMC,
                Carbon::create(2024,2,28),Carbon::create(2024,3,1)),

            // CN-017  RICO O. MACHADO  LG-SP-319 → Cuyab  N/A
            $make('BOATR-2024-M-017','RICO','O.','MACHADO',null,'Cuyab',
                '05-043425000-00655','RICO 9','Motorized',
                8.50,1.46,0.70,'MITSUBISHI',300,$NA,$WMC,
                Carbon::create(2024,3,2),Carbon::create(2024,3,4)),

            // CN-018  ALFREDO T. CAOYONG  LAG-14-032 → Landayan  Gillnet
            $make('BOATR-2024-M-018','ALFREDO','T.','CAOYONG',null,'Landayan',
                'LG-SP-000330-2017','WARAK','Motorized',
                5.19,0.76,0.37,'ROBIN',5,$GN,$WMC,
                Carbon::create(2024,3,5),Carbon::create(2024,3,7)),

            // CN-019  LORENZO R. ALMEIDA  LAG-14-070 → Landayan  Gillnet
            $make('BOATR-2024-M-019','LORENZO','R.','ALMEIDA',null,'Landayan',
                'LG-SP-000136-2015','BOY','Motorized',
                7.30,0.66,0.45,'KEMBO',7,$GN,$WMC,
                Carbon::create(2024,3,8),Carbon::create(2024,3,10)),

            // CN-020  RUEL M. GUMAL  LG-SP-320 → Cuyab  Gillnet
            $make('BOATR-2024-M-020','RUEL','M.','GUMAL',null,'Cuyab',
                '27-043425000-00367','N/A','Motorized',
                6.50,0.92,0.50,'EXTREME GASOLINE',7,$GN,$WMC,
                Carbon::create(2024,3,11),Carbon::create(2024,3,13)),

            // CN-021  RODITO M. GUMAL  LAG-14-049 → Landayan  Gillnet
            $make('BOATR-2024-M-021','RODITO','M.','GUMAL',null,'Landayan',
                '21-043425000-00400','RODY','Motorized',
                7.80,0.90,0.50,'YAMADA',12,$GN,$WMC,
                Carbon::create(2024,3,14),Carbon::create(2024,3,16)),

            // CN-023  NOLLIE Q. CAOYONG  LAG-14-034 → Landayan  Gillnet
            $make('BOATR-2024-M-023','NOLLIE','Q.','CAOYONG',null,'Landayan',
                '03-043425000-00502','KYRIE','Motorized',
                6.00,0.83,0.58,'YAMADA',18,$GN,$WMC,
                Carbon::create(2024,3,17),Carbon::create(2024,3,19)),

            // CN-024  ARIEL N. DELOS REYES JR.  LAG-14-039 → Landayan  N/A
            $make('BOATR-2024-M-024','ARIEL','N.','DELOS REYES',null,'Landayan',
                '28-043425000-00464','MV 4 BOYS','Motorized',
                6.40,0.88,0.88,'ROBIN',7,$NA,$WMC,
                Carbon::create(2024,3,20),Carbon::create(2024,3,22)),

            // CN-027  MICHAEL S. HEREDIA  LAG-14-109 → Landayan  Gillnet
            $make('BOATR-2024-M-027','MICHAEL','S.','HEREDIA',null,'Landayan',
                '11-043425000-00625','MIKE','Motorized',
                3.63,0.88,0.88,'YAMADA',9,$GN,$WMC,
                Carbon::create(2024,3,23),Carbon::create(2024,3,25)),

            // CN-035  ENRICO S. DELOS REYES SR.  LAG-14-068 → Landayan  Gillnet
            $make('BOATR-2024-M-035','ENRICO','S.','DELOS REYES SR.',null,'Landayan',
                'LG-SP-000170-2015','MV ENRICO','Motorized',
                5.70,0.85,0.30,'ROBIN',8,$GN,$WMC,
                Carbon::create(2024,3,26),Carbon::create(2024,3,28)),

            // CN-036  RENATO B. OLIVAREZ  LAG-14-005 → Landayan  Baklad → Fish Coral
            $make('BOATR-2024-M-036','RENATO','B.','OLIVAREZ',null,'Landayan',
                'LG-SP-000323-2017','MB MERCI','Motorized',
                5.70,0.83,0.48,'KINGSTONE',12,$FC,$WMC,
                Carbon::create(2024,3,29),Carbon::create(2024,3,31)),

            // CN-037  RODRIGO B. DOROTEO  LAG-SP-304/B → Cuyab  N/A
            $make('BOATR-2024-M-037','RODRIGO','B.','DOROTEO',null,'Cuyab',
                '28-043425000-00475','LA LAKERS','Motorized',
                6.20,1.92,0.88,'ISUZU',240,$NA,$WMC,
                Carbon::create(2024,4,1),Carbon::create(2024,4,3)),

            // CN-038  RODRIGO B. DOROTEO  LAG-SP-303 → Cuyab  N/A
            $make('BOATR-2024-M-038','RODRIGO','B.','DOROTEO',null,'Cuyab',
                '28-043425000-00475','LA LAKERS 2','Motorized',
                8.19,1.31,0.57,'TOYOTA',139,$NA,$WMC,
                Carbon::create(2024,4,4),Carbon::create(2024,4,6)),

            // CN-039  RODRIGO B. DOROTEO  LAG-14-198 → Landayan  N/A
            $make('BOATR-2024-M-039','RODRIGO','B.','DOROTEO',null,'Landayan',
                '28-043425000-00475','LA LAKERS 3','Motorized',
                7.00,0.88,0.48,'ROBIN',8,$NA,$WMC,
                Carbon::create(2024,4,7),Carbon::create(2024,4,9)),

            // CN-042  JERICKSON C. SILVANO  LAG-14-153 → Landayan  N/A
            $make('BOATR-2024-M-042','JERICKSON','C.','SILVANO',null,'Landayan',
                'LG-SP-000205-2015','JERICK','Motorized',
                7.29,0.73,0.43,'ROBIN',5,$NA,$WMC,
                Carbon::create(2024,4,10),Carbon::create(2024,4,12)),

            // CN-044  CESAR ABUNDO  LG-SP-321 → Cuyab  Gillnet
            $make('BOATR-2024-M-044','CESAR',null,'ABUNDO',null,'Cuyab',
                'LG-SP-000286-2016','CESAR','Motorized',
                5.30,1.06,0.20,'NITO',7,$GN,$WMC,
                Carbon::create(2024,4,13),Carbon::create(2024,4,15)),

            // CN-045  ELISEO C. ESCUDERO  LAG-14-102 → Landayan  Gillnet
            $make('BOATR-2024-M-045','ELISEO','C.','ESCUDERO',null,'Landayan',
                'LG-SP-000055-2015','ELY','Motorized',
                4.97,0.77,0.48,'KEMBO',7,$GN,$WMC,
                Carbon::create(2024,4,16),Carbon::create(2024,4,18)),

            // CN-046  MARCIAL L. TORRES  LAG-14-093 → Landayan  Gillnet
            $make('BOATR-2024-M-046','MARCIAL','L.','TORRES',null,'Landayan',
                'LG-SP-000315-2017','MARCIAL','Motorized',
                3.63,0.85,0.50,'HONDA',13,$GN,$WMC,
                Carbon::create(2024,4,19),Carbon::create(2024,4,21)),

            // CN-048  PELAGIO A. GARCIA  LAG-14-089 → Landayan  N/A
            $make('BOATR-2024-M-048','PELAGIO','A.','GARCIA',null,'Landayan',
                'LG-SP-000171-2015','RICO 5','Motorized',
                5.75,0.97,0.34,'NITTO',8,$NA,$WMC,
                Carbon::create(2024,4,22),Carbon::create(2024,4,24)),

            // CN-049  FERDINAND P. GARCIA  LAG-14-174 → Landayan  N/A
            $make('BOATR-2024-M-049','FERDINAND','P.','GARCIA',null,'Landayan',
                '11-043425000-00612','FERDINAND','Motorized',
                6.06,0.50,0.57,'SUMO',15,$NA,$WMC,
                Carbon::create(2024,4,25),Carbon::create(2024,4,27)),

            // CN-050  RICKY C. CURAMPES  LAG-14-052 → Landayan  Gillnet
            $make('BOATR-2024-M-050','RICKY','C.','CURAMPES',null,'Landayan',
                'LG-SP-000166-2015','RICKY','Motorized',
                4.30,0.88,0.72,'ROBIN',8,$GN,$WMC,
                Carbon::create(2024,4,28),Carbon::create(2024,4,30)),

            // CN-056  EDUARDO E. GONZALES  LAG-14-003 → Landayan  Gillnet
            $make('BOATR-2024-M-056','EDUARDO','E.','GONZALES',null,'Landayan',
                '27-043425000-00366','EDUARDO','Motorized',
                5.48,0.90,0.30,'BEARING',7,$GN,$WMC,
                Carbon::create(2024,5,1),Carbon::create(2024,5,3)),

            // CN-058  BERLIN P. OLIVAREZ  LAG-14-092 → Landayan  Gillnet
            $make('BOATR-2024-M-058','BERLIN','P.','OLIVAREZ',null,'Landayan',
                'LG-SP-000194-2015','BERLIN','Motorized',
                3.63,0.82,0.31,'BRIGGS STRATON',10,$GN,$WMC,
                Carbon::create(2024,5,4),Carbon::create(2024,5,6)),

            // CN-060  MANUEL R. ALMEIDA  LAG-14-051 → Landayan  Gillnet
            $make('BOATR-2024-M-060','MANUEL','R.','ALMEIDA',null,'Landayan',
                'LG-SP-000314-2017','MANUELITO','Motorized',
                7.00,0.80,0.50,'NITTO',10,$GN,$WMC,
                Carbon::create(2024,5,7),Carbon::create(2024,5,9)),

            // CN-061  CRISPIN R. ALMEIDA  LAG-14-086 → Landayan  Gillnet
            $make('BOATR-2024-M-061','CRISPIN','R.','ALMEIDA',null,'Landayan',
                '21-043425000-00399','CRIS','Motorized',
                6.50,0.88,0.50,'LONTOP',8,$GN,$WMC,
                Carbon::create(2024,5,10),Carbon::create(2024,5,12)),

            // CN-062  LARIO J. ALMEIDA  LAG-14-096 → Landayan  Gillnet
            $make('BOATR-2024-M-062','LARIO','J.','ALMEIDA',null,'Landayan',
                'LG-SP-000291-2016','LARIO','Motorized',
                3.93,0.89,0.58,'ROBIN',7,$GN,$WMC,
                Carbon::create(2024,5,13),Carbon::create(2024,5,15)),

            // CN-063  ROBERT T. CORDOVA  LAG-14-112 → Landayan  Gillnet
            $make('BOATR-2024-M-063','ROBERT','T.','CORDOVA',null,'Landayan',
                '11-043425000-00596','OBET','Motorized',
                5.30,0.70,0.31,'NITTO',7,$GN,$WMC,
                Carbon::create(2024,5,16),Carbon::create(2024,5,18)),

            // CN-064  FLORENTINO R. ALMEIDA  LAG-14-058 → Landayan  Gillnet
            $make('BOATR-2024-M-064','FLORENTINO','R.','ALMEIDA',null,'Landayan',
                'LG-SP-000151-2015','JHUN','Motorized',
                3.90,0.75,0.30,'NITTO',7,$GN,$WMC,
                Carbon::create(2024,5,19),Carbon::create(2024,5,21)),

            // CN-066  ROGER A. TEÑIDO  LAG-14-069 → Landayan  Gillnet
            $make('BOATR-2024-M-066','ROGER','A.','TEÑIDO',null,'Landayan',
                'LG-SP-000214-2015','ROGER','Motorized',
                5.50,0.76,0.34,'SUPREME',7,$GN,$WMC,
                Carbon::create(2024,5,22),Carbon::create(2024,5,24)),

            // CN-067  VENANCIO C. SARIO  LAG-14-016 → Landayan  Gillnet
            $make('BOATR-2024-M-067','VENANCIO','C.','SARIO',null,'Landayan',
                'LG-SP-000084-2015','CHING','Motorized',
                5.21,0.82,0.87,'ROBIN',8,$GN,$WMC,
                Carbon::create(2024,5,25),Carbon::create(2024,5,27)),

            // CN-068  FERNAND C. ALMEIDA  LAG-14-082 → Landayan  Gillnet
            $make('BOATR-2024-M-068','FERNAND','C.','ALMEIDA',null,'Landayan',
                '27-043425000-00341','FERNAND','Motorized',
                6.65,0.89,0.58,'YANMAR',8,$GN,$WMC,
                Carbon::create(2024,5,28),Carbon::create(2024,5,30)),

            // CN-069  FERNANDO R. ALMEIDA  LAG-14-001 → Landayan  Gillnet
            $make('BOATR-2024-M-069','FERNANDO','R.','ALMEIDA',null,'Landayan',
                'LG-SP-000257-2015','PANDO','Motorized',
                7.63,0.83,0.53,'SUMO',15,$GN,$WMC,
                Carbon::create(2024,5,31),Carbon::create(2024,6,2)),

            // CN-071  DANILO V. MACHADO  LG-SP-324 → Cuyab  Gillnet
            $make('BOATR-2024-M-071','DANILO','V.','MACHADO',null,'Cuyab',
                'LG-SP-000319-2017','QUIN','Motorized',
                7.39,0.35,0.37,'HONDA',8,$GN,$WMC,
                Carbon::create(2024,6,3),Carbon::create(2024,6,5)),

            // CN-072  DOMINGO Q. RONCALES  LAG-14-199 → Landayan  Gillnet
            $make('BOATR-2024-M-072','DOMINGO','Q.','RONCALES',null,'Landayan',
                '21-043425000-00375','DQR','Motorized',
                5.97,0.67,0.55,'SUMO',15,$GN,$WMC,
                Carbon::create(2024,6,6),Carbon::create(2024,6,8)),

            // CN-073  GERMAN M. HALAYAHAY  LAG-14-074 → Landayan  Gillnet
            $make('BOATR-2024-M-073','GERMAN','M.','HALAYAHAY',null,'Landayan',
                'LG-SP-000310-2017','GERMAN','Motorized',
                4.60,0.85,0.56,'MARINE',17,$GN,$WMC,
                Carbon::create(2024,6,9),Carbon::create(2024,6,11)),

            // CN-074  ROMAR S. AMION  LAG-14-091 → Landayan  Gillnet
            $make('BOATR-2024-M-074','ROMAR','S.','AMION',null,'Landayan',
                '11-043425000-00608','AMBER','Motorized',
                7.29,1.19,0.50,'SUMO YAMATO',15,$GN,$WMC,
                Carbon::create(2024,6,12),Carbon::create(2024,6,14)),

            // CN-075  JAIMEE V. MACHADO  LAG-14-077 → Landayan  Gillnet
            $make('BOATR-2024-M-075','JAIMEE','V.','MACHADO',null,'Landayan',
                '11-043425000-00602','TOTO','Motorized',
                7.30,0.62,0.43,'NITTO',7,$GN,$WMC,
                Carbon::create(2024,6,15),Carbon::create(2024,6,17)),

            // CN-076  ROMEO R. ANDRADA  LAG-14-025 → Landayan  Gillnet
            $make('BOATR-2024-M-076','ROMEO','R.','ANDRADA',null,'Landayan',
                'LG-SP-000231-2015','ROMY','Motorized',
                5.74,0.82,0.40,'YAMADA',18,$GN,$WMC,
                Carbon::create(2024,6,18),Carbon::create(2024,6,20)),

            // CN-077  JIMSON M. BACHECHA  LAG-14-022 → Landayan  Gillnet
            $make('BOATR-2024-M-077','JIMSON','M.','BACHECHA',null,'Landayan',
                'LG-SP-000336-2017','ELISON','Motorized',
                5.50,0.80,0.50,'YAMADA',16,$GN,$WMC,
                Carbon::create(2024,6,21),Carbon::create(2024,6,23)),

            // CN-079  JULIUS P. ODERO  LAG-SP-298 → Cuyab  N/A
            $make('BOATR-2024-M-079','JULIUS','P.','ODERO',null,'Cuyab',
                'LG-SP-000344-2018','JHIRO ANGELO','Motorized',
                5.76,0.82,0.37,'ROBIN',5,$NA,$WMC,
                Carbon::create(2024,6,24),Carbon::create(2024,6,26)),

            // CN-085  RICARDO R. ANDRADA  LAG-14-027 → Landayan  Gillnet
            $make('BOATR-2024-M-085','RICARDO','R.','ANDRADA',null,'Landayan',
                'LG-SP-000229-2015','RICARDO','Motorized',
                5.60,0.78,0.10,'KEMBO',7,$GN,$WMC,
                Carbon::create(2024,6,27),Carbon::create(2024,6,29)),

            // CN-086  JAVIER M. BUCALAN  LAG-14-031 → Landayan  Gillnet
            $make('BOATR-2024-M-086','JAVIER','M.','BUCALAN',null,'Landayan',
                'LG-SP-000308-2017','JAVIER','Motorized',
                6.00,0.80,0.58,'ROBIN',8,$GN,$WMC,
                Carbon::create(2024,6,30),Carbon::create(2024,7,2)),

            // CN-091  ROBERTO S. TEMPROSA  LAG-14-111 → Landayan  Gillnet
            $make('BOATR-2024-M-091','ROBERTO','S.','TEMPROSA',null,'Landayan',
                'LG-SP-000209-2015','OBET M/B','Motorized',
                5.91,0.70,0.31,'MITSUBISHI',5,$GN,$WMC,
                Carbon::create(2024,7,3),Carbon::create(2024,7,5)),

            // CN-094  AERON BRIAN S. INSORIO  LAG-14-118 → Landayan  Gillnet
            $make('BOATR-2024-M-094','AERON BRIAN','S.','INSORIO',null,'Landayan',
                '03-043425000-00482','GERON','Motorized',
                6.25,0.76,0.52,'YAMADA',18,$GN,$WMC,
                Carbon::create(2024,7,6),Carbon::create(2024,7,8)),

            // CN-095  DONATO N. MARISTANEZ  LAG-14-178 → Landayan  Gillnet
            $make('BOATR-2024-M-095','DONATO','N.','MARISTANEZ',null,'Landayan',
                '03-043425000-00481','DEX','Motorized',
                5.51,0.64,0.51,'HONDA',6,$GN,$WMC,
                Carbon::create(2024,7,9),Carbon::create(2024,7,11)),

            // CN-097  GRACIANO R. INSORIO  LAG-14-128 → Landayan  Gillnet
            $make('BOATR-2024-M-097','GRACIANO','R.','INSORIO',null,'Landayan',
                'LG-SP-000070-2015','POLYN','Motorized',
                5.79,0.61,0.41,'HONDA',6,$GN,$WMC,
                Carbon::create(2024,7,12),Carbon::create(2024,7,14)),

            // CN-099  AQUILINO L. YAMBAO  LAG-14-161 → Landayan  Baklad → Fish Coral
            $make('BOATR-2024-M-099','AQUILINO','L.','YAMBAO',null,'Landayan',
                'LG-SP-000048-2015','PJ YAMBAO','Motorized',
                6.10,0.73,0.48,'KAWAMA',7,$FC,$WMC,
                Carbon::create(2024,7,15),Carbon::create(2024,7,17)),

            // CN-101  SENANDO A. BACHECHA JR  LAG-14-024 → Landayan  Gillnet
            $make('BOATR-2024-M-101','SENANDO','A.','BACHECHA JR',null,'Landayan',
                'LG-SP-000284-2016','TIRAMISU','Motorized',
                6.00,0.85,0.45,'YAMADA',18,$GN,$WMC,
                Carbon::create(2024,7,18),Carbon::create(2024,7,20)),

            // CN-102  EDEJIE D. CABANLIT  LAG-14-081 → Landayan  Gillnet
            $make('BOATR-2024-M-102','EDEJIE','D.','CABANLIT',null,'Landayan',
                'LG-SP-000275-2015','2 BROTHERS','Motorized',
                7.60,0.97,0.48,'SUMO',15,$GN,$WMC,
                Carbon::create(2024,7,21),Carbon::create(2024,7,23)),

            // CN-104  CRISPIN A. TEMPROSA  LAG-14-004 → Landayan  Gillnet
            $make('BOATR-2024-M-104','CRISPIN','A.','TEMPROSA',null,'Landayan',
                'LG-SP-000208-2015','CRISPIN','Motorized',
                6.00,0.65,0.60,'YAMADA',18,$GN,$WMC,
                Carbon::create(2024,7,24),Carbon::create(2024,7,26)),

            // CN-106  ELBERTO E. CURAMPES  LAG-14-099 → Landayan  Gillnet
            $make('BOATR-2024-M-106','ELBERTO','E.','CURAMPES',null,'Landayan',
                '16-043425000-00546','NONOY','Motorized',
                5.41,0.55,0.43,'NITTO',8,$GN,$WMC,
                Carbon::create(2024,7,27),Carbon::create(2024,7,29)),

            // CN-107  MAR A. LIMOSA  LAG-14-029 → Landayan  Gillnet
            $make('BOATR-2024-M-107','MAR','A.','LIMOSA',null,'Landayan',
                'LG-SP-000303-2017','MAR','Motorized',
                8.00,1.22,0.30,'VANGUARD',13,$GN,$WMC,
                Carbon::create(2024,7,30),Carbon::create(2024,8,1)),

            // CN-108  AGNES S. LIMOSA  LAG-14-028 → Landayan  Gillnet
            $make('BOATR-2024-M-108','AGNES','S.','LIMOSA',null,'Landayan',
                '13-043425000-00338','AGNES','Motorized',
                8.00,1.22,0.34,'ISUZU',15,$GN,$WMC,
                Carbon::create(2024,8,2),Carbon::create(2024,8,4)),

            // CN-109  DEMETRIO C. MORENO  LAG-14-154 → Landayan  Gillnet
            $make('BOATR-2024-M-109','DEMETRIO','C.','MORENO',null,'Landayan',
                '11-043425000-00640','DEMETRIO','Motorized',
                5.87,1.07,0.46,'HONDA',8,$GN,$WMC,
                Carbon::create(2024,8,5),Carbon::create(2024,8,7)),

            // CN-110  JAYMEE M. LAZARO  LAG-14-002 → Landayan  Gillnet
            $make('BOATR-2024-M-110','JAYMEE','M.','LAZARO',null,'Landayan',
                'LG-SP-000183-2015','JAYMEE LAZARO','Motorized',
                8.30,0.90,0.57,'YAMADA',18,$GN,$WMC,
                Carbon::create(2024,8,8),Carbon::create(2024,8,10)),

            // CN-111  ROBERTO B. RAGUIT  LAG-14-328 → Landayan  Gillnet
            $make('BOATR-2024-M-111','ROBERTO','B.','RAGUIT',null,'Landayan',
                '21-043425000-00371','JUN','Motorized',
                6.40,0.70,0.40,'YAMMA',18,$GN,$NMC,
                Carbon::create(2024,8,11),Carbon::create(2024,8,13)),

            // CN-112  ROBERTO B. RAGUIT  LAG-14-329 → Landayan  Gillnet
            $make('BOATR-2024-M-112','ROBERTO','B.','RAGUIT',null,'Landayan',
                '21-043425000-00371','NORIEL','Motorized',
                6.07,0.50,0.32,'SHINMAX',8,$GN,$NMC,
                Carbon::create(2024,8,14),Carbon::create(2024,8,16)),

            // CN-113  JASPER I. SARIO  LAG-14-330 → Landayan  Gillnet
            $make('BOATR-2024-M-113','JASPER','I.','SARIO',null,'Landayan',
                '21-043425000-00369','JASPER','Motorized',
                5.10,0.83,0.40,'SHINMAX',8,$GN,$NMC,
                Carbon::create(2024,8,17),Carbon::create(2024,8,19)),

            // CN-114  MOHAMMAD MUSA E. AHMAD  LAG-14-331 → Landayan  Gillnet
            $make('BOATR-2024-M-114','MOHAMMAD MUSA','E.','AHMAD',null,'Landayan',
                '2024-043425000-00672','HORTON','Motorized',
                8.12,1.12,0.49,'KAMA',12,$GN,$NMC,
                Carbon::create(2024,8,20),Carbon::create(2024,8,22)),

            // CN-115  ROBERTO B. RAGUIT  LAG-14-332 → Landayan  Gillnet
            $make('BOATR-2024-M-115','ROBERTO','B.','RAGUIT',null,'Landayan',
                '21-043425000-00371','JUN 3','Motorized',
                6.06,0.73,1.24,'MARINE',8,$GN,$NMC,
                Carbon::create(2024,8,23),Carbon::create(2024,8,25)),

            // CN-116  ROBERTO B. RAGUIT  LAG-14-333 → Landayan  Gillnet
            $make('BOATR-2024-M-116','ROBERTO','B.','RAGUIT',null,'Landayan',
                '21-043425000-00371','JUN 4','Motorized',
                5.76,0.68,0.30,'YAMADA',8,$GN,$NMC,
                Carbon::create(2024,8,26),Carbon::create(2024,8,28)),

            // CN-117  RONALD A. LIM  LAG-14-334 → Landayan  Gillnet
            $make('BOATR-2024-M-117','RONALD','A.','LIM',null,'Landayan',
                '2024-043425000-00675','MACOY','Motorized',
                6.67,0.77,0.67,'YAMMA',18,$GN,$NMC,
                Carbon::create(2024,8,29),Carbon::create(2024,8,31)),

            // CN-118  JENNER E. AMAGO  LAG-14-072 → Landayan  Gillnet
            $make('BOATR-2024-M-118','JENNER','E.','AMAGO',null,'Landayan',
                '11-043425000-00607','N/A','Motorized',
                5.45,0.40,0.76,'ROBIN',8,$GN,$NMC,
                Carbon::create(2024,9,1),Carbon::create(2024,9,3)),

            // CN-119  JOSE A. TEMPROSA  LAG-14-085 → Landayan  Gillnet
            $make('BOATR-2024-M-119','JOSE','A.','TEMPROSA',null,'Landayan',
                '11-043425000-00614','JOSE','Motorized',
                5.55,0.88,0.38,'MANTRA',16,$GN,$WMC,
                Carbon::create(2024,9,4),Carbon::create(2024,9,6)),

            // CN-120  ALBIN A. ALVIAR  LAG-14-107 → Landayan  Gillnet
            $make('BOATR-2024-M-120','ALBIN','A.','ALVIAR',null,'Landayan',
                '11-043425000-00623','KLIMA','Motorized',
                6.37,0.82,0.64,'BRIGGS STRATON',10,$GN,$NMC,
                Carbon::create(2024,9,7),Carbon::create(2024,9,9)),

            // CN-121  FERDINAND D. GUILLERMO  LAG-14-076 → Landayan  Gillnet
            $make('BOATR-2024-M-121','FERDINAND','D.','GUILLERMO',null,'Landayan',
                'LG-SP-000316-2017','BONG','Motorized',
                6.30,0.90,0.31,'HONDA',7,$GN,$NMC,
                Carbon::create(2024,9,10),Carbon::create(2024,9,12)),

            // CN-122  DARWIN B. CASINOS  LAG-14-087 → Landayan  Gillnet
            $make('BOATR-2024-M-122','DARWIN','B.','CASINOS',null,'Landayan',
                '11-043425000-00595','CEEJAY','Motorized',
                9.00,1.34,0.94,'ISUZU',221,$GN,$WMC,
                Carbon::create(2024,9,13),Carbon::create(2024,9,15)),

            // CN-123  RONALD E. CURAMPEZ  LAG-14-335 → Landayan  Gillnet
            $make('BOATR-2024-M-123','RONALD','E.','CURAMPEZ',null,'Landayan',
                '11-043425000-00632','KEEYAN&ANIQA','Motorized',
                11.22,1.20,0.70,'MITSUBISHI',50,$GN,$NMC,
                Carbon::create(2024,9,16),Carbon::create(2024,9,18)),

            // CN-124  ROBERTO B. RAGUIT  LAG-14-336 → Landayan  Gillnet
            $make('BOATR-2024-M-124','ROBERTO','B.','RAGUIT',null,'Landayan',
                '21-043425000-00371','JUN 6','Motorized',
                5.76,0.67,0.55,'ROBIN',8,$GN,$NMC,
                Carbon::create(2024,9,19),Carbon::create(2024,9,21)),

            // CN-125  ROBERTO B. RAGUIT  LAG-14-337 → Landayan  Gillnet
            $make('BOATR-2024-M-125','ROBERTO','B.','RAGUIT',null,'Landayan',
                '21-043425000-00371','JUN 5','Motorized',
                6.10,0.60,0.37,'EXTREME',5,$GN,$NMC,
                Carbon::create(2024,9,22),Carbon::create(2024,9,24)),

            // CN-126  RANILLO N. SANTAÑEZ  LAG-14-055 → Landayan  Gillnet
            $make('BOATR-2024-M-126','RANILLO','N.','SANTAÑEZ',null,'Landayan',
                '11-043425000-00613','RANNY','Motorized',
                5.10,0.70,0.50,'LONTOP',8,$GN,$WMC,
                Carbon::create(2024,9,25),Carbon::create(2024,9,27)),

            // CN-127  ARTEMIO V. ORTEGA  LAG-14-132 → Landayan  Gillnet
            $make('BOATR-2024-M-127','ARTEMIO','V.','ORTEGA',null,'Landayan',
                'LG-SP-000078-2015','KHYING/JINICA','Motorized',
                7.00,0.67,0.46,'YAMADA',8,$GN,$NMC,
                Carbon::create(2024,9,28),Carbon::create(2024,9,30)),

            // CN-128  MELCHOR R. YARIS  LAG-14-338 → Landayan  Gillnet
            $make('BOATR-2024-M-128','MELCHOR','R.','YARIS',null,'Landayan',
                '2024-043425000-00676','MV AUSTINE','Motorized',
                6.08,0.78,0.42,'BRIGGS STRATON',6,$GN,$NMC,
                Carbon::create(2024,10,1),Carbon::create(2024,10,3)),

            // CN-130  GENARO G. DE BORJA  LAG-14-098 → Landayan  Gillnet
            $make('BOATR-2024-M-130','GENARO','G.','DE BORJA',null,'Landayan',
                'LG-SP-000167-2015','GENER','Motorized',
                6.37,0.79,0.37,'NITO',8,$GN,$NMC,
                Carbon::create(2024,10,4),Carbon::create(2024,10,6)),

            // CN-131  JONATHAN S. ESPARAGOZA  LAG-14-296 → Landayan  Gillnet
            $make('BOATR-2024-M-131','JONATHAN','S.','ESPARAGOZA',null,'Landayan',
                '2023-043425000-00660','MV JONATHAN','Motorized',
                7.50,0.50,0.40,'BRIGGS STRATON',16,$GN,$WMC,
                Carbon::create(2024,10,7),Carbon::create(2024,10,9)),

            // ==================================================================
            // 2024 NON-MOTORIZED  — source: "REGISTERED NON-MOTORIZED FISHING BOAT 2024"
            // All GILLNET → Bottom Set Gill Net ; BUBO → Not Applicable
            // All LAG-14-xxx → Landayan ; LG-SP-xxx → Cuyab
            // ==================================================================

            $make('BOATR-2024-NM-022','JERRY','P.','URIARTE',null,'Landayan',
                '21-043425000-00407','BUBOY','Non-motorized',
                3.67,0.74,0.27,null,null,$GN,$NMB,
                Carbon::create(2024,1,15),Carbon::create(2024,1,16)),

            $make('BOATR-2024-NM-025','ARIEL','S.','DELOS REYES SR.',null,'Landayan',
                'LG-SP-000169-2015','MV 4 BOYS','Non-motorized',
                6.30,0.88,0.88,null,null,$GN,$NMB,
                Carbon::create(2024,1,17),Carbon::create(2024,1,18)),

            $make('BOATR-2024-NM-026','DANILO','F.','HILARIO',null,'Landayan',
                'LG SP-000324-2017','DANDOY','Non-motorized',
                6.00,0.72,0.31,null,null,$GN,$NMB,
                Carbon::create(2024,1,19),Carbon::create(2024,1,20)),

            $make('BOATR-2024-NM-028','RONALDO','E.','AJEDO',null,'Landayan',
                '11-043425000-00618','RONALDO','Non-motorized',
                5.97,0.73,0.27,null,null,$GN,$NMB,
                Carbon::create(2024,1,21),Carbon::create(2024,1,22)),

            $make('BOATR-2024-NM-029','SILVERIO','P.','FLAMIANO',null,'Landayan',
                '03-043425000-00490','BEYONG','Non-motorized',
                5.55,0.67,0.20,null,null,$GN,$NMB,
                Carbon::create(2024,1,23),Carbon::create(2024,1,24)),

            $make('BOATR-2024-NM-030','RONALDO','M.','LOS BAÑOS',null,'Landayan',
                'LG-SP-000072-2015','PADI','Non-motorized',
                5.76,0.50,0.27,null,null,$GN,$NMB,
                Carbon::create(2024,1,25),Carbon::create(2024,1,26)),

            $make('BOATR-2024-NM-031','MARCOS','B.','CORDIS',null,'Landayan',
                'LG-SP-000056-2015','MACOY','Non-motorized',
                6.68,0.61,0.27,null,null,$GN,$NMB,
                Carbon::create(2024,1,27),Carbon::create(2024,1,28)),

            $make('BOATR-2024-NM-032','ARMANDO','G.','BAUSO',null,'Landayan',
                'LG-SP-000241-2015','NONG NONG','Non-motorized',
                6.37,0.58,0.27,null,null,$GN,$NMB,
                Carbon::create(2024,1,29),Carbon::create(2024,1,30)),

            $make('BOATR-2024-NM-033','ALEJANDRO','G.','BAUSO',null,'Landayan',
                '03-043425000-00489','ANDREW','Non-motorized',
                5.15,0.58,0.20,null,null,$GN,$NMB,
                Carbon::create(2024,1,31),Carbon::create(2024,2,1)),

            $make('BOATR-2024-NM-034','ENRICO','G.','DELOS REYES JR.',null,'Landayan',
                '28-043425000-00456','ENRICO JR.','Non-motorized',
                5.55,0.85,0.27,null,null,$GN,$NMB,
                Carbon::create(2024,2,2),Carbon::create(2024,2,3)),

            $make('BOATR-2024-NM-040','VIRGILIO','S.','AVELINA',null,'Landayan',
                'LG-SP-000346-2018','BUDHA','Non-motorized',
                6.07,0.61,0.31,null,null,$GN,$NMB,
                Carbon::create(2024,2,4),Carbon::create(2024,2,5)),

            $make('BOATR-2024-NM-041','JAY-AR','V.','AVELINA',null,'Landayan',
                'LG-SP-000236-2015','JR','Non-motorized',
                6.05,0.73,0.77,null,null,$GN,$NMB,
                Carbon::create(2024,2,6),Carbon::create(2024,2,7)),

            // CN-043  LG-SP-322 → Cuyab
            $make('BOATR-2024-NM-043','ALEXANDER','B.','CARAN',null,'Cuyab',
                'LG-SP-000164-2015','N/A','Non-motorized',
                5.12,0.73,0.36,null,null,$GN,$NMB,
                Carbon::create(2024,2,8),Carbon::create(2024,2,9)),

            $make('BOATR-2024-NM-047','ROBERTO','R.','ALON-ALON',null,'Landayan',
                '11-043425000-00642','BERTO','Non-motorized',
                5.75,0.48,0.23,null,null,$GN,$NMB,
                Carbon::create(2024,2,10),Carbon::create(2024,2,11)),

            $make('BOATR-2024-NM-051','BRIAN','P.','NAZARENO',null,'Landayan',
                'LG-SP-000029-2015','BRAVO','Non-motorized',
                6.00,0.72,0.31,null,null,$GN,$NMB,
                Carbon::create(2024,2,12),Carbon::create(2024,2,13)),

            $make('BOATR-2024-NM-052','LAURO','O.','VIERNEZA',null,'Landayan',
                'LG-SP-000096-2015','LAURO','Non-motorized',
                5.15,0.48,0.45,null,null,$GN,$NMB,
                Carbon::create(2024,2,14),Carbon::create(2024,2,15)),

            // CN-053  LG-SP-323 → Cuyab  BUBO → Not Applicable
            $make('BOATR-2024-NM-053','EFREN','M.','VERGARA',null,'Cuyab',
                'LG-SP-000221-2015','EFREN','Non-motorized',
                6.98,0.70,0.35,null,null,$NA,$NMB,
                Carbon::create(2024,2,16),Carbon::create(2024,2,17)),

            $make('BOATR-2024-NM-054','NICANOR','A.','BERON',null,'Landayan',
                '03-043425000-00510','NICK','Non-motorized',
                5.10,0.68,0.16,null,null,$GN,$NMB,
                Carbon::create(2024,2,18),Carbon::create(2024,2,19)),

            $make('BOATR-2024-NM-055','EDGAR','M.','NAVALES',null,'Landayan',
                'LG-SP-000076-2015','EGAY','Non-motorized',
                5.00,0.70,0.30,null,null,$GN,$NMB,
                Carbon::create(2024,2,20),Carbon::create(2024,2,21)),

            // CN-056 NM — same owner/fishr as motorized CN-056 but separate record
            $make('BOATR-2024-NM-056','EDUARDO','E.','GONZALES',null,'Landayan',
                '27-043425000-00366','EDUARDO','Non-motorized',
                5.48,0.90,0.30,null,null,$GN,$NMB,
                Carbon::create(2024,2,22),Carbon::create(2024,2,23)),

            $make('BOATR-2024-NM-057','BENJAMIN','S.','BRICENIO',null,'Landayan',
                'LG-SP-000304-2017','BEN','Non-motorized',
                6.20,0.73,0.34,null,null,$GN,$NMB,
                Carbon::create(2024,2,24),Carbon::create(2024,2,25)),

            $make('BOATR-2024-NM-059','EMMANUEL','P.','CORPUZ',null,'Landayan',
                'LG-SP-000014-2015','N/A','Non-motorized',
                5.80,0.62,0.27,null,null,$GN,$NMB,
                Carbon::create(2024,2,26),Carbon::create(2024,2,27)),

            $make('BOATR-2024-NM-065','GERARDO','A.','ALMEIDA',null,'Landayan',
                'LG-SP-000011-2015','GERRY','Non-motorized',
                6.10,0.73,0.33,null,null,$GN,$NMB,
                Carbon::create(2024,2,28),Carbon::create(2024,2,29)),

            $make('BOATR-2024-NM-070','ROMEO','B.','ALMEIDA',null,'Landayan',
                'LG-SP-000249-2015','ROMEO','Non-motorized',
                5.50,0.70,0.30,null,null,$GN,$NMB,
                Carbon::create(2024,3,1),Carbon::create(2024,3,2)),

            $make('BOATR-2024-NM-078','ONOPRE','V.','DELA CRUZ',null,'Landayan',
                'LG-SP-000061-2015','OPENG','Non-motorized',
                5.15,0.48,0.45,null,null,$GN,$NMB,
                Carbon::create(2024,3,3),Carbon::create(2024,3,4)),

            $make('BOATR-2024-NM-080','AQUILINO','L.','YAMBAO',null,'Landayan',
                'LG-SP-000048-2015','PJ','Non-motorized',
                6.95,0.78,0.43,null,null,$GN,$NMB,
                Carbon::create(2024,3,5),Carbon::create(2024,3,6)),

            // CN-081  LG-SP-325 → Cuyab
            $make('BOATR-2024-NM-081','VIRGILIO','V.','CASULLA',null,'Cuyab',
                'LG-SP-000054-2015','VER','Non-motorized',
                6.50,0.62,0.38,null,null,$GN,$NMB,
                Carbon::create(2024,3,7),Carbon::create(2024,3,8)),

            $make('BOATR-2024-NM-082','ALMARIO','C.','PERBER',null,'Landayan',
                '07-043425000-00413','N/A','Non-motorized',
                8.50,0.62,0.25,null,null,$GN,$NMB,
                Carbon::create(2024,3,9),Carbon::create(2024,3,10)),

            $make('BOATR-2024-NM-083','CARLITO','O.','AVELINA',null,'Landayan',
                'LG-SP-000232-2015','N/A','Non-motorized',
                5.40,0.31,0.61,null,null,$GN,$NMB,
                Carbon::create(2024,3,11),Carbon::create(2024,3,12)),

            $make('BOATR-2024-NM-084','ROGELIO','R.','ANDRADA',null,'Landayan',
                'LG-SP-000230-2015','N/A','Non-motorized',
                5.67,0.30,0.61,null,null,$GN,$NMB,
                Carbon::create(2024,3,13),Carbon::create(2024,3,14)),

            // CN-087  LG-SP-326 → Cuyab
            $make('BOATR-2024-NM-087','MARVIC','B.','MEJIAS',null,'Cuyab',
                '11-043425000-00644','VIC 1','Non-motorized',
                6.67,0.67,0.27,null,null,$GN,$NMB,
                Carbon::create(2024,3,15),Carbon::create(2024,3,16)),

            // CN-088  LG-SP-327 → Cuyab
            $make('BOATR-2024-NM-088','MARVIC','B.','MEJIAS',null,'Cuyab',
                '11-043425000-00644','VIC 2','Non-motorized',
                5.15,0.58,0.27,null,null,$GN,$NMB,
                Carbon::create(2024,3,17),Carbon::create(2024,3,18)),

            $make('BOATR-2024-NM-089','VENANCIO','O.','AVELINA',null,'Landayan',
                'LG-SP-000235-2015','N/A','Non-motorized',
                6.40,0.70,0.31,null,null,$GN,$NMB,
                Carbon::create(2024,3,19),Carbon::create(2024,3,20)),

            $make('BOATR-2024-NM-090','LARRY','C.','MARQUEZ',null,'Landayan',
                'LG-SP-000325-2017','N/A','Non-motorized',
                6.00,0.70,0.32,null,null,$GN,$NMB,
                Carbon::create(2024,3,21),Carbon::create(2024,3,22)),

            $make('BOATR-2024-NM-092','ROMANO','B.','AVELINA',null,'Landayan',
                'LG-SP-000234-2015','OMAN','Non-motorized',
                6.06,0.72,0.27,null,null,$GN,$NMB,
                Carbon::create(2024,3,23),Carbon::create(2024,3,24)),

            $make('BOATR-2024-NM-093','JAIME','R.','ANDRADA',null,'Landayan',
                'LG-SP-000226-2015','N/A','Non-motorized',
                5.20,0.70,0.28,null,null,$GN,$NMB,
                Carbon::create(2024,3,25),Carbon::create(2024,3,26)),

            $make('BOATR-2024-NM-096','RICO','L.','PASCASIO',null,'Landayan',
                '03-043425000-00509','RICO L','Non-motorized',
                6.06,0.67,0.31,null,null,$GN,$NMB,
                Carbon::create(2024,3,27),Carbon::create(2024,3,28)),

            $make('BOATR-2024-NM-098','GRACIANO','R.','INSORIO',null,'Landayan',
                'LG-SP-000070-2015','N/A','Non-motorized',
                5.58,0.82,0.27,null,null,$GN,$NMB,
                Carbon::create(2024,3,29),Carbon::create(2024,3,30)),

            // CN-100  Baklad → Fish Coral
            $make('BOATR-2024-NM-100','VLADIMIR','M.','ALAIZA',null,'Landayan',
                'LG-SP-000343-2018','LOLO UWENG','Non-motorized',
                6.40,0.74,0.31,null,null,$FC,$NMB,
                Carbon::create(2024,3,31),Carbon::create(2024,4,1)),

            $make('BOATR-2024-NM-103','ALMARIO','V.','VIDAL JR',null,'Landayan',
                '11-043425000-00641','JEALM','Non-motorized',
                5.75,0.58,0.28,null,null,$GN,$NMB,
                Carbon::create(2024,4,2),Carbon::create(2024,4,3)),

            $make('BOATR-2024-NM-105','ROMEO','A.','VIERNEZA JR',null,'Landayan',
                'LG-SP-000099-2015','N/A','Non-motorized',
                5.00,0.80,0.90,null,null,$GN,$NMB,
                Carbon::create(2024,4,4),Carbon::create(2024,4,5)),

            $make('BOATR-2024-NM-129','ALBERTO','G.','IZON',null,'Landayan',
                'LG-SP-000176-2015','AMBET','Non-motorized',
                3.93,0.67,0.37,null,null,$GN,$NMB,
                Carbon::create(2024,4,6),Carbon::create(2024,4,7)),

            // ==================================================================
            // 2025 NON-MOTORIZED  — source: "REGISTERED NON-MOTORIZED FISHING BOAT 2025"
            // All LAG-14-xxx → Landayan ; LAG-14-345 listed under Cuyab owner Rasid Jacaria
            // N/A gear → Not Applicable
            // ==================================================================

            $make('BOATR-2025-NM-134','JOJIE','H.','CATALON',null,'Landayan',
                '27-043425000-00339','OPAG','Non-motorized',
                7.28,0.88,0.27,null,null,$GN,$NMB,
                Carbon::create(2025,1,10),Carbon::create(2025,1,12)),

            $make('BOATR-2025-NM-136','MARIO','V.','AVELINA JR',null,'Landayan',
                'LG-SP-000337-2017','EBO','Non-motorized',
                5.76,0.73,0.27,null,null,$GN,$NMB,
                Carbon::create(2025,1,13),Carbon::create(2025,1,15)),

            $make('BOATR-2025-NM-137','MARIO','O.','AVELINA SR',null,'Landayan',
                'LG-SP-000233-2015','MAR','Non-motorized',
                4.84,0.73,0.27,null,null,$GN,$NMB,
                Carbon::create(2025,1,16),Carbon::create(2025,1,18)),

            $make('BOATR-2025-NM-138','GERARDO','Q.','INSORIO',null,'Landayan',
                'LG-SP-000069-2015','GERRY','Non-motorized',
                5.97,0.78,0.32,null,null,$GN,$NMB,
                Carbon::create(2025,1,19),Carbon::create(2025,1,21)),

            // CN-144  RASID U. JACARIA  LAG-14-345  N/A gear
            $make('BOATR-2025-NM-144','RASID','U.','JACARIA',null,'Landayan',
                '2025-043425000-00679','SHAWY-AJ 3','Non-motorized',
                7.89,1.03,0.42,null,null,$NA,$NMB,
                Carbon::create(2025,1,22),Carbon::create(2025,1,24)),

            $make('BOATR-2025-NM-147','ORLANDO','V.','FRANCIA',null,'Landayan',
                'LG-SP-000331-2017','ORLY','Non-motorized',
                5.40,0.61,0.27,null,null,$GN,$NMB,
                Carbon::create(2025,1,25),Carbon::create(2025,1,27)),

            $make('BOATR-2025-NM-155','RONALD','A.','VIERNEZA',null,'Landayan',
                'LG-SP-000101-2015','TOTO','Non-motorized',
                5.76,0.61,0.54,null,null,$GN,$NMB,
                Carbon::create(2025,1,28),Carbon::create(2025,1,30)),

            $make('BOATR-2025-NM-156','ALVIN','P.','YABUT',null,'Landayan',
                'LG-SP-000102-2015','ALVIN','Non-motorized',
                5.75,0.64,0.27,null,null,$GN,$NMB,
                Carbon::create(2025,1,31),Carbon::create(2025,2,2)),

            $make('BOATR-2025-NM-159','WILLIAM','P.','IÑOSA',null,'Landayan',
                'LG-SP-000093-2015','WILLY','Non-motorized',
                5.76,0.61,0.31,null,null,$GN,$NMB,
                Carbon::create(2025,2,3),Carbon::create(2025,2,5)),

            $make('BOATR-2025-NM-160','ELMER','S.','GAVIOLA',null,'Landayan',
                '27-043425000-00340','ELMER 1','Non-motorized',
                5.20,0.85,0.30,null,null,$GN,$NMB,
                Carbon::create(2025,2,6),Carbon::create(2025,2,8)),

            $make('BOATR-2025-NM-164','ERNIE','M.','BOLA',null,'Landayan',
                '03-043425000-00493','BARBERO','Non-motorized',
                6.10,0.76,0.40,null,null,$GN,$NMB,
                Carbon::create(2025,2,9),Carbon::create(2025,2,11)),

            $make('BOATR-2025-NM-165','ROBERTO','S.','PAULIN',null,'Landayan',
                'LG-SP-000082-2015','VINCENT','Non-motorized',
                5.76,0.54,0.51,null,null,$GN,$NMB,
                Carbon::create(2025,2,12),Carbon::create(2025,2,14)),

            $make('BOATR-2025-NM-166','ROMAN','V.','VIBAR',null,'Landayan',
                'LG-SP-000088-2015','ROMY','Non-motorized',
                6.67,0.67,0.20,null,null,$GN,$NMB,
                Carbon::create(2025,2,15),Carbon::create(2025,2,17)),

            $make('BOATR-2025-NM-168','RENATO','D.','DELA CRUZ',null,'Landayan',
                'LG-SP-000345-2018','AKI','Non-motorized',
                5.76,0.64,0.34,null,null,$GN,$NMB,
                Carbon::create(2025,2,18),Carbon::create(2025,2,20)),

            $make('BOATR-2025-NM-169','FRANCISCO','L.','CANILLIAS',null,'Landayan',
                '2025-043425000-00712','COWBOY','Non-motorized',
                5.15,0.57,0.27,null,null,$GN,$NMB,
                Carbon::create(2025,2,21),Carbon::create(2025,2,23)),

            $make('BOATR-2025-NM-170','ALBERTO','V.','FRANCISCO',null,'Landayan',
                '2025-043425000-00719','MACHETE','Non-motorized',
                3.02,0.58,0.43,null,null,$GN,$NMB,
                Carbon::create(2025,2,24),Carbon::create(2025,2,26)),

            $make('BOATR-2025-NM-171','ROBERTO','R.','NOTA',null,'Landayan',
                '28-043425000-00442','THUNDER','Non-motorized',
                5.76,0.48,0.39,null,null,$GN,$NMB,
                Carbon::create(2025,2,27),Carbon::create(2025,3,1)),

            // ==================================================================
            // 2025 MOTORIZED — source: "2025 motorized" (no CN numbers given)
            // Owner names not provided in spreadsheet → null
            // All LAG-14-xxx → Landayan
            // N/A gear → Not Applicable
            // ==================================================================

            // Row 1  TOTO  LAG-14-094  with maritime clearance  Gillnet
            $make('BOATR-2025-M-001',null,null,null,null,'Landayan',
                'LG-SP-000189-2015','TOTO','Motorized',
                5.98,0.31,0.34,'MITSUBISHI',8,$GN,$WMC,
                Carbon::create(2025,1,5),Carbon::create(2025,1,7)),

            // Row 2  CRISTOPHER  LAG-14-079  no maritime clearance  N/A
            $make('BOATR-2025-M-002',null,null,null,null,'Landayan',
                'LG-SP-000276-2015','CRISTOPHER','Motorized',
                7.30,1.25,0.30,'BRIGGS STRATON',10,$NA,$NMC,
                Carbon::create(2025,1,8),Carbon::create(2025,1,10)),

            // Row 3  BLAN  LAG-14-350  no maritime clearance  Gillnet
            $make('BOATR-2025-M-003',null,null,null,null,'Landayan',
                'LG-SP-000338-2017','BLAN','Motorized',
                5.45,0.88,0.42,'SHINMAX',8,$GN,$NMC,
                Carbon::create(2025,1,11),Carbon::create(2025,1,13)),

            // Row 4  SWR  LAG-14-341  no maritime clearance  N/A
            $make('BOATR-2025-M-004',null,null,null,null,'Landayan',
                '2025-043425000-00681','SWR','Motorized',
                8.50,1.18,0.57,'YAMAHA',16,$NA,$NMC,
                Carbon::create(2025,2,10),Carbon::create(2025,2,15)),

            // Row 5  BUSHRA 1  LAG-14-346  no maritime clearance  N/A
            $make('BOATR-2025-M-005',null,null,null,null,'Landayan',
                '2025-043425000-00682','BUSHRA 1','Motorized',
                7.74,1.03,0.57,'MEGA',16,$NA,$NMC,
                Carbon::create(2025,2,13),Carbon::create(2025,2,18)),

            // Row 6  BUSHRA 2  LAG-14-344  no maritime clearance  N/A
            $make('BOATR-2025-M-006',null,null,null,null,'Landayan',
                '2025-043425000-00682','BUSHRA 2','Motorized',
                7.89,1.18,0.57,'SUMO',16,$NA,$NMC,
                Carbon::create(2025,2,16),Carbon::create(2025,2,21)),

            // Row 7  SHAWY-AJ 1  LAG-14-342  no maritime clearance  N/A
            $make('BOATR-2025-M-007',null,null,null,null,'Landayan',
                '2025-043425000-00679','SHAWY-AJ 1','Motorized',
                8.50,1.79,0.73,'ISUZU',95,$NA,$NMC,
                Carbon::create(2025,2,19),Carbon::create(2025,2,24)),

            // Row 8  SHAWY-AJ 2  LAG-14-343  no maritime clearance  N/A
            $make('BOATR-2025-M-008',null,null,null,null,'Landayan',
                '2025-043425000-00679','SHAWY-AJ 2','Motorized',
                7.89,1.03,0.42,'YAMMA',16,$NA,$NMC,
                Carbon::create(2025,2,22),Carbon::create(2025,2,27)),

            // Row 9  TOTOH  LAG-14-340  no maritime clearance  N/A
            $make('BOATR-2025-M-009',null,null,null,null,'Landayan',
                '2025-043425000-00680','TOTOH','Motorized',
                8.50,1.18,0.57,'SUMO',15,$NA,$NMC,
                Carbon::create(2025,3,1),Carbon::create(2025,3,5)),

            // Row 10  BHIJAY  LAG-14-148  no maritime clearance  N/A
            $make('BOATR-2025-M-010',null,null,null,null,'Landayan',
                '11-043425000-00637','BHIJAY','Motorized',
                7.89,1.18,0.42,'YAMMA',18,$NA,$NMC,
                Carbon::create(2025,3,6),Carbon::create(2025,3,10)),

            // Row 11  MANNY  LAG-14-054  no maritime clearance  Gillnet
            $make('BOATR-2025-M-011',null,null,null,null,'Landayan',
                'LG-SP-000219-2015','MANNY','Motorized',
                5.90,0.60,0.42,'SUMO',7,$GN,$NMC,
                Carbon::create(2025,3,11),Carbon::create(2025,3,15)),

            // Row 12  ATAN  LAG-14-179  no maritime clearance  Gillnet
            $make('BOATR-2025-M-012',null,null,null,null,'Landayan',
                'LG-SP-000348-2018','ATAN','Motorized',
                6.06,0.63,0.63,'YAMADA',16,$GN,$NMC,
                Carbon::create(2025,3,16),Carbon::create(2025,3,20)),

            // Row 13  TOPHER  LAG-14-339  no maritime clearance  N/A
            $make('BOATR-2025-M-013',null,null,null,null,'Landayan',
                'LG-SP-000276-2015','TOPHER','Motorized',
                6.06,1.03,0.57,'YAMMA',10,$NA,$NMC,
                Carbon::create(2025,3,21),Carbon::create(2025,3,25)),

            // Row 14  SHELVIE ANN  LAG-14-073  with maritime clearance  Gillnet
            $make('BOATR-2025-M-014',null,null,null,null,'Landayan',
                'LG-SP-000265-2015','SHELVIE ANN','Motorized',
                4.80,0.80,0.45,'MARPRO',7,$GN,$WMC,
                Carbon::create(2025,3,26),Carbon::create(2025,3,30)),

            // Row 15  ELBRANDO  LAG-14-146  with maritime clearance  Gillnet
            $make('BOATR-2025-M-015',null,null,null,null,'Landayan',
                '11-043425000-00638','ELBRANDO','Motorized',
                6.68,1.16,0.49,'NITTO',8,$GN,$WMC,
                Carbon::create(2025,4,1),Carbon::create(2025,4,5)),

            // Row 16  AMI  LAG-14-351  no maritime clearance  Gillnet
            // Note: spreadsheet TB=2.5 looks like a data entry error; using TL=6.4/TB=0.73/TD=0.33 (TL col value)
            $make('BOATR-2025-M-016',null,null,null,null,'Landayan',
                'LG-SP-000090-2015','AMI','Motorized',
                6.40,0.73,0.33,'SHINMAX',8,$GN,$NMC,
                Carbon::create(2025,4,6),Carbon::create(2025,4,10)),

            // Row 17  MB JER-LETH  LAG-14-353  no maritime clearance  Gillnet
            $make('BOATR-2025-M-017',null,null,null,null,'Landayan',
                '2025-043425000-00688','MB JER-LETH','Motorized',
                8.20,0.50,0.47,'YAMADA',18,$GN,$NMC,
                Carbon::create(2025,4,11),Carbon::create(2025,4,15)),

            // Row 18  CALIX  LAG-14-173  no maritime clearance  Gillnet
            $make('BOATR-2025-M-018',null,null,null,null,'Landayan',
                '11-043425000-00617','CALIX','Motorized',
                5.45,0.30,0.57,'JAPAN TECHNOLOGY',16,$GN,$NMC,
                Carbon::create(2025,4,16),Carbon::create(2025,4,20)),

            // Row 19  ELISON 2  LAG-14-354  no maritime clearance  Gillnet
            $make('BOATR-2025-M-019',null,null,null,null,'Landayan',
                'LG-SP-000336-2017','ELISON 2','Motorized',
                7.59,0.87,0.57,'YAMADA',16,$GN,$NMC,
                Carbon::create(2025,4,21),Carbon::create(2025,4,25)),

            // Row 20  ARTUR  LAG-14-132  no maritime clearance  Gillnet
            $make('BOATR-2025-M-020',null,null,null,null,'Landayan',
                'LG-SP-000342-2018','ARTUR','Motorized',
                5.18,0.37,0.37,'YAMADA',8,$GN,$NMC,
                Carbon::create(2025,4,26),Carbon::create(2025,4,30)),

            // Row 21  NORMAN  LAG-14-355  no maritime clearance  Baklad → Fish Coral
            $make('BOATR-2025-M-021',null,null,null,null,'Landayan',
                '2025-043425000-00714','NORMAN','Motorized',
                9.41,0.87,0.69,'ISUZU C240',300,$FC,$NMC,
                Carbon::create(2025,5,1),Carbon::create(2025,5,5)),

            // Row 22  MARK ANGELO  LAG-14-358  no maritime clearance  Gillnet
            $make('BOATR-2025-M-022',null,null,null,null,'Landayan',
                '28-043425000-00449','MARK ANGELO','Motorized',
                5.76,0.73,0.50,'KTEC',16,$GN,$NMC,
                Carbon::create(2025,5,6),Carbon::create(2025,5,10)),

            // ==================================================================
            // 2026 NON-MOTORIZED  — source: "REGISTERED NON-MOTORIZED FISHING BOAT 2026"
            // ==================================================================

            // CN-176  JERNIE C. TOLDANES  LAG-14-363 → Landayan  Gillnet
            $make('BOATR-2026-NM-176','JERNIE','C.','TOLDANES',null,'Landayan',
                '2026-043425000-00724','CALINOG','Non-motorized',
                5.45,0.77,0.32,null,null,$GN,$NMB,
                Carbon::create(2026,2,8),Carbon::create(2026,2,10)),

            // ==================================================================
            // 2026 MOTORIZED  — source: "2026 motorized"
            // Owner names not in spreadsheet → null
            // All LAG-14-xxx → Landayan ; no LAG-SP prefix present
            // ==================================================================

            // Row 1  3R  LAG-14-361  no maritime clearance  Gillnet
            $make('BOATR-2026-M-001',null,null,null,null,'Landayan',
                '21-043425000-00389','3R','Motorized',
                7.59,0.88,0.73,'BRIGGS STRATON',18,$GN,$NMC,
                Carbon::create(2026,1,5),Carbon::create(2026,1,8)),

            // Row 2  MV EDDIE  LAG-14-053  no maritime clearance  Gillnet
            $make('BOATR-2026-M-002',null,null,null,null,'Landayan',
                '11-043425000-00597','MV EDDIE','Motorized',
                6.85,0.70,0.40,'ROBIN',5,$GN,$NMC,
                Carbon::create(2026,1,10),Carbon::create(2026,1,13)),

            // Row 3  TOTENG  LAG-14-362  no maritime clearance  Gillnet
            $make('BOATR-2026-M-003',null,null,null,null,'Landayan',
                '2026-043425000-00723','TOTENG','Motorized',
                5.41,0.70,0.43,'BRIGGS STRATON',16,$GN,$NMC,
                Carbon::create(2026,1,15),Carbon::create(2026,1,18)),

            // Row 4  FORTE  LAG-14-188  no maritime clearance  Gillnet
            $make('BOATR-2026-M-004',null,null,null,null,'Landayan',
                'LG-SP-000320-2017','FORTE','Motorized',
                6.10,0.69,0.30,'MOTORSTAR',7,$GN,$NMC,
                Carbon::create(2026,1,20),Carbon::create(2026,1,23)),

            // Row 5  ZALDY  LAG-14-175  no maritime clearance  Gillnet
            $make('BOATR-2026-M-005',null,null,null,null,'Landayan',
                '03-043425000-00519','ZALDY','Motorized',
                7.28,0.72,0.64,'KEMBO',8,$GN,$NMC,
                Carbon::create(2026,1,25),Carbon::create(2026,1,28)),

            // Row 6  LEO VER  LAG-14-177  with maritime clearance  Gillnet
            $make('BOATR-2026-M-006',null,null,null,null,'Landayan',
                '28-043425000-00445','LEO VER','Motorized',
                6.24,0.85,0.51,'KAWASAKI',8,$GN,$WMC,
                Carbon::create(2026,1,30),Carbon::create(2026,2,2)),

            // Row 7  MACOY 2  LAG-14-364  no maritime clearance  Gillnet
            $make('BOATR-2026-M-007',null,null,null,null,'Landayan',
                '2026-043425000-00725','MACOY 2','Motorized',
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
            if (!empty($data['fishr_number'])) {
                if ($fishrExists($data['fishr_number'])) {
                    $linkedCount++;
                    $this->command->info("✓ Linked FishR {$data['fishr_number']} → {$data['application_number']}");
                } else {
                    $this->command->warn("⚠ FishR {$data['fishr_number']} not found in fishr_applications — stored as-is for {$data['application_number']}");
                    $unlinkedCount++;
                }
            } else {
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
        $this->command->info("BoatrRegisteredSeeder completed successfully!");
        $this->command->info("Records created  : {$createdCount}");
        $this->command->info("Records updated  : {$updatedCount}");
        $this->command->info("Total seeded     : {$total}");
        $this->command->info("FishR linked     : {$linkedCount}");
        $this->command->info("FishR unlinked   : {$unlinkedCount}");
        $this->command->info("Total in DB      : " . BoatrApplication::count());
    }
}