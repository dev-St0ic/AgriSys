<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TrainingApplication;
use Carbon\Carbon;

class TrainingSeeder extends Seeder
{
    public function run(): void
    {
        $trainingTypes = [
            'tilapia_hito',
            'hydroponics',
            'aquaponics',
            'mushrooms',
            'livestock_poultry',
            'high_value_crops',
            'sampaguita_propagation',
        ];

        $statuses = ['pending', 'under_review', 'approved', 'rejected'];

        $barangays = [
            'Bagong Silang', 'Calendola', 'Chrysanthemum', 'Cuyab', 'Estrella',
            'Fatima', 'G.S.I.S.', 'Landayan', 'Langgam', 'Laram', 'Magsaysay',
            'Maharlika', 'Narra', 'Nueva', 'Pacita 1', 'Pacita 2', 'Poblacion',
            'Riverside', 'Rosario', 'Sampaguita Village', 'San Antonio',
            'San Lorenzo Ruiz', 'San Roque', 'San Vicente', 'Santo Niño',
            'United Bayanihan', 'United Better Living',
        ];

        $applicants = [
            ['first_name' => 'Jose',       'middle_name' => 'Reyes',     'last_name' => 'Santos'],
            ['first_name' => 'Maria',      'middle_name' => 'Cruz',      'last_name' => 'Dela Torre'],
            ['first_name' => 'Roberto',    'middle_name' => 'Manalac',   'last_name' => 'Reyes'],
            ['first_name' => 'Ana',        'middle_name' => 'Bautista',  'last_name' => 'Garcia'],
            ['first_name' => 'Carlos',     'middle_name' => 'Domingo',   'last_name' => 'Villanueva'],
            ['first_name' => 'Liza',       'middle_name' => '',          'last_name' => 'Ramos'],
            ['first_name' => 'Eduardo',    'middle_name' => 'Santos',    'last_name' => 'Flores'],
            ['first_name' => 'Rosario',    'middle_name' => 'Mendoza',   'last_name' => 'Torres'],
            ['first_name' => 'Benito',     'middle_name' => '',          'last_name' => 'Castillo'],
            ['first_name' => 'Cynthia',    'middle_name' => 'Lagman',    'last_name' => 'Navarro'],
            ['first_name' => 'Fernando',   'middle_name' => 'Abad',      'last_name' => 'Mercado'],
            ['first_name' => 'Gloria',     'middle_name' => 'Pascual',   'last_name' => 'Aquino'],
            ['first_name' => 'Hernando',   'middle_name' => '',          'last_name' => 'Dizon'],
            ['first_name' => 'Isabel',     'middle_name' => 'Soriano',   'last_name' => 'Medina'],
            ['first_name' => 'Jaime',      'middle_name' => 'Tolentino', 'last_name' => 'Pangilinan'],
            ['first_name' => 'Karen',      'middle_name' => '',          'last_name' => 'Ocampo'],
            ['first_name' => 'Leonardo',   'middle_name' => 'Valdez',    'last_name' => 'Espiritu'],
            ['first_name' => 'Maricel',    'middle_name' => 'Lim',       'last_name' => 'Andrade'],
            ['first_name' => 'Nestor',     'middle_name' => 'Perez',     'last_name' => 'Ibarra'],
            ['first_name' => 'Ofelia',     'middle_name' => '',          'last_name' => 'Salazar'],
            ['first_name' => 'Pedro',      'middle_name' => 'Guevarra',  'last_name' => 'Canlas'],
            ['first_name' => 'Queenie',    'middle_name' => 'Sta. Ana',  'last_name' => 'Pineda'],
            ['first_name' => 'Renato',     'middle_name' => 'Macapagal', 'last_name' => 'Dela Cruz'],
            ['first_name' => 'Salvacion',  'middle_name' => '',          'last_name' => 'Bondoc'],
            ['first_name' => 'Tomas',      'middle_name' => 'Araneta',   'last_name' => 'Silverio'],
        ];

        // Spread dates from Jan 2024 to March 2026
        $dates = [
            Carbon::create(2024, 1, 15, 9, 30),
            Carbon::create(2024, 2, 20, 10, 0),
            Carbon::create(2024, 3, 5,  14, 15),
            Carbon::create(2024, 4, 18, 11, 45),
            Carbon::create(2024, 5, 22, 8,  30),
            Carbon::create(2024, 6, 10, 13, 0),
            Carbon::create(2024, 7, 3,  15, 30),
            Carbon::create(2024, 8, 27, 9,  0),
            Carbon::create(2024, 9, 14, 10, 30),
            Carbon::create(2024, 10, 8, 14, 0),
            Carbon::create(2024, 11, 19, 11, 0),
            Carbon::create(2024, 12, 2,  9,  45),
            Carbon::create(2024, 12, 28, 13, 30),
            Carbon::create(2025, 1, 10, 10, 0),
            Carbon::create(2025, 2, 14, 8,  30),
            Carbon::create(2025, 3, 21, 14, 15),
            Carbon::create(2025, 4, 7,  11, 0),
            Carbon::create(2025, 5, 30, 9,  30),
            Carbon::create(2025, 6, 16, 13, 45),
            Carbon::create(2025, 7, 25, 10, 0),
            Carbon::create(2025, 8, 11, 15, 0),
            Carbon::create(2025, 9, 4,  9,  15),
            Carbon::create(2025, 10, 20, 11, 30),
            Carbon::create(2026, 1, 8,  10, 0),
            Carbon::create(2026, 3, 10, 14, 0),
        ];

        $remarks = [
            'Walk-in applicant',
            'Referred by barangay captain',
            'Previously attended orientation',
            'Interested in flower farming',
            'Group application with neighbors',
            'Requested morning schedule',
            null,
            null,
            null,
            'Follow-up application',
            'Recommended by agricultural officer',
            null,
        ];

        foreach ($applicants as $index => $applicant) {
            $date = $dates[$index];

            TrainingApplication::create([
                'application_number' => $this->generateApplicationNumber(),
                'first_name'         => $applicant['first_name'],
                'middle_name'        => $applicant['middle_name'] ?: null,
                'last_name'          => $applicant['last_name'],
                'name_extension'     => null,
                'contact_number'     => '09' . str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT),
                'barangay'           => $barangays[$index % count($barangays)],
                'training_type'      => $trainingTypes[$index % count($trainingTypes)],
                'status'             => $statuses[$index % count($statuses)],
                'remarks'            => $remarks[$index % count($remarks)],
                'document_path'      => null,
                'created_at'         => $date,
                'updated_at'         => $date,
            ]);
        }
    }

    private function generateApplicationNumber(): string
    {
    $year = now()->year;

    $last = TrainingApplication::where('application_number', 'like', "TRAIN-{$year}-%")
        ->orderByDesc('application_number')
        ->value('application_number');

    $nextSequence = $last
        ? (int) substr($last, strrpos($last, '-') + 1) + 1
        : 1;

    if ($nextSequence > 9999) {
        throw new \Exception("Training application number limit reached for year {$year}.");
    }

    return "TRAIN-{$year}-" . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
    }
}