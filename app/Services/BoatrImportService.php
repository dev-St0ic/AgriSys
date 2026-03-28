<?php

namespace App\Services;

use App\Models\BoatrApplication;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BoatrImportService
{
    // -------------------------------------------------------
    // Lookup maps
    // -------------------------------------------------------

    const BOAT_TYPE_MAP = [
        'spoon'                                       => 'Spoon',
        'plumb'                                       => 'Plumb',
        'banca'                                       => 'Banca',
        'rake stem - rake stern'                      => 'Rake Stem - Rake Stern',
        'rake stem rake stern'                        => 'Rake Stem - Rake Stern',
        'rake stem - transom/spoon/plumb stern'       => 'Rake Stem - Transom/Spoon/Plumb Stern',
        'rake stem - transom'                         => 'Rake Stem - Transom/Spoon/Plumb Stern',
        'rake stem transom'                           => 'Rake Stem - Transom/Spoon/Plumb Stern',
        'skiff (typical design)'                      => 'Skiff (Typical Design)',
        'skiff'                                       => 'Skiff (Typical Design)',
    ];

    const BOAT_CLASSIFICATION_MAP = [
        'motorized'     => 'Motorized',
        'non-motorized' => 'Non-motorized',
        'non motorized' => 'Non-motorized',
    ];

    const FISHING_GEAR_MAP = [
        'hook and line'         => 'Hook and Line',
        'bottom set gill net'   => 'Bottom Set Gill Net',
        'fish trap'             => 'Fish Trap',
        'fish coral'            => 'Fish Coral',
        'not applicable'        => 'Not Applicable',
        'n/a'                   => 'Not Applicable',
        'na'                    => 'Not Applicable',
    ];

    const STATUS_MAP = [
        'pending'            => 'pending',
        'under review'       => 'under_review',
        'under_review'       => 'under_review',
        'inspection required'=> 'inspection_required',
        'inspection_required'=> 'inspection_required',
        'inspection scheduled'=> 'inspection_scheduled',
        'inspection_scheduled'=> 'inspection_scheduled',
        'documents pending'  => 'documents_pending',
        'documents_pending'  => 'documents_pending',
        'approved'           => 'approved',
        'rejected'           => 'rejected',
    ];

    const VALID_BARANGAYS = [
        'Bagong Silang', 'Calendola', 'Chrysanthemum', 'Cuyab', 'Estrella',
        'Fatima', 'G.S.I.S.', 'Landayan', 'Langgam', 'Laram', 'Magsaysay',
        'Maharlika', 'Narra', 'Nueva', 'Pacita 1', 'Pacita 2', 'Poblacion',
        'Riverside', 'Rosario', 'Sampaguita Village', 'San Antonio',
        'San Lorenzo Ruiz', 'San Roque', 'San Vicente', 'Santo Niño',
        'United Bayanihan', 'United Better Living',
    ];

    const REQUIRED_HEADERS = [
        'first_name', 'last_name', 'contact_number', 'barangay',
        'fishr_number', 'vessel_name', 'boat_type', 'boat_classification',
        'boat_length', 'boat_width', 'boat_depth', 'primary_fishing_gear',
    ];

    // -------------------------------------------------------
    // Public entry point
    // -------------------------------------------------------

    public function import(string $filePath, string $extension = ''): array
    {
        $extension = strtolower($extension ?: pathinfo($filePath, PATHINFO_EXTENSION));

        if (in_array($extension, ['csv', 'txt', ''])) {
            $rows = $this->parseCsv($filePath);
        } elseif (in_array($extension, ['xlsx', 'xls'])) {
            $rows = $this->parseExcel($filePath);
        } else {
            throw new \InvalidArgumentException('Unsupported file type. Please upload a CSV or Excel file.');
        }

        return $this->processRows($rows);
    }

    // -------------------------------------------------------
    // Parsers
    // -------------------------------------------------------

    private function parseCsv(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \RuntimeException('Could not open the uploaded file.');
        }

        $rows    = [];
        $headers = null;

        while (($line = fgetcsv($handle, 0, ',')) !== false) {
            if (count(array_filter($line, fn($v) => trim($v) !== '')) === 0) {
                continue;
            }

            if ($headers === null) {
                $headers = array_map(fn($h) => $this->normaliseHeader($h), $line);
                continue;
            }

            while (count($line) < count($headers)) {
                $line[] = '';
            }

            $rows[] = array_combine($headers, array_slice($line, 0, count($headers)));
        }

        fclose($handle);
        return $rows;
    }

    private function parseExcel(string $filePath): array
    {
        if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
            throw new \RuntimeException(
                'PhpSpreadsheet is not installed. Please use a CSV file instead, ' .
                'or run: composer require phpoffice/phpspreadsheet'
            );
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $sheet       = $spreadsheet->getActiveSheet();
        $data        = $sheet->toArray(null, true, true, false);

        if (empty($data)) {
            return [];
        }

        $headers = array_map(fn($h) => $this->normaliseHeader((string)$h), array_shift($data));
        $rows    = [];

        foreach ($data as $line) {
            if (count(array_filter($line, fn($v) => trim((string)$v) !== '')) === 0) {
                continue;
            }

            while (count($line) < count($headers)) {
                $line[] = '';
            }

            $rows[] = array_combine($headers, array_slice($line, 0, count($headers)));
        }

        return $rows;
    }

    private function normaliseHeader(string $header): string
    {
        $header = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $header);
        $header = trim($header);
        $header = strtolower($header);
        $header = preg_replace('/[\s\-]+/', '_', $header);
        return $header;
    }

    // -------------------------------------------------------
    // Processing
    // -------------------------------------------------------

    private function validateHeaders(array $headers): void
    {
        $missing = array_diff(self::REQUIRED_HEADERS, $headers);
        if (!empty($missing)) {
            throw new \InvalidArgumentException(
                'The file is missing required columns: ' . implode(', ', $missing) . '. ' .
                'Please use the provided template.'
            );
        }
    }

    private function processRows(array $rows): array
    {
        if (empty($rows)) {
            throw new \InvalidArgumentException('The file is empty or contains no data rows.');
        }

        $this->validateHeaders(array_keys($rows[0]));

        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $rowErrors = $this->validateRow($row, $rowNumber);

            if (!empty($rowErrors)) {
                $skipped++;
                $errors[] = ['row' => $rowNumber, 'data' => $row, 'errors' => $rowErrors];
                continue;
            }

            try {
                $this->createApplication($row);
                $imported++;
            } catch (\Exception $e) {
                $skipped++;
                $errors[] = [
                    'row'    => $rowNumber,
                    'data'   => $row,
                    'errors' => ['system' => 'Failed to save: ' . $e->getMessage()],
                ];

                Log::error('BoatR import row failed', [
                    'row'   => $rowNumber,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return compact('imported', 'skipped', 'errors');
    }

    private function validateRow(array $row, int $rowNumber): array
    {
        $errors = [];

        // First name
        $firstName = trim($row['first_name'] ?? '');
        if ($firstName === '') {
            $errors['first_name'] = 'First name is required.';
        } elseif (!preg_match("/^[a-zA-Z\s\-']+$/", $firstName)) {
            $errors['first_name'] = 'First name contains invalid characters.';
        }

        // Last name
        $lastName = trim($row['last_name'] ?? '');
        if ($lastName === '') {
            $errors['last_name'] = 'Last name is required.';
        } elseif (!preg_match("/^[a-zA-Z\s\-']+$/", $lastName)) {
            $errors['last_name'] = 'Last name contains invalid characters.';
        }

        // Contact number
        $contact = preg_replace('/\D/', '', trim($row['contact_number'] ?? ''));
        if ($contact === '') {
            $errors['contact_number'] = 'Contact number is required.';
        } elseif (!preg_match('/^09\d{9}$/', $contact)) {
            $errors['contact_number'] = 'Contact number must be 11 digits starting with 09.';
        }

        // Barangay
        $barangay = trim($row['barangay'] ?? '');
        if ($barangay === '') {
            $errors['barangay'] = 'Barangay is required.';
        } else {
            $matched = collect(self::VALID_BARANGAYS)
                ->first(fn($b) => strcasecmp($b, $barangay) === 0);
            if (!$matched) {
                $errors['barangay'] = "Invalid barangay: \"{$barangay}\".";
            }
        }

        // FishR number
        $fishrNumber = trim($row['fishr_number'] ?? '');
        if ($fishrNumber === '') {
            $errors['fishr_number'] = 'FishR number is required.';
        }

        // Vessel name
        $vesselName = trim($row['vessel_name'] ?? '');
        if ($vesselName === '') {
            $errors['vessel_name'] = 'Vessel name is required.';
        }

        // Boat type
        $boatTypeRaw = strtolower(trim($row['boat_type'] ?? ''));
        if ($boatTypeRaw === '') {
            $errors['boat_type'] = 'Boat type is required.';
        } elseif (!isset(self::BOAT_TYPE_MAP[$boatTypeRaw])) {
            $errors['boat_type'] = "Unrecognised boat type: \"{$row['boat_type']}\".";
        }

        // Boat classification
        $classRaw = strtolower(trim($row['boat_classification'] ?? ''));
        if ($classRaw === '') {
            $errors['boat_classification'] = 'Boat classification is required.';
        } elseif (!isset(self::BOAT_CLASSIFICATION_MAP[$classRaw])) {
            $errors['boat_classification'] = "Must be 'Motorized' or 'Non-motorized'.";
        }

        // Engine fields required for motorized boats
        if (isset(self::BOAT_CLASSIFICATION_MAP[$classRaw]) &&
            self::BOAT_CLASSIFICATION_MAP[$classRaw] === 'Motorized'
        ) {
            if (trim($row['engine_type'] ?? '') === '') {
                $errors['engine_type'] = 'Engine type is required for motorized boats.';
            }
            $hp = trim($row['engine_horsepower'] ?? '');
            if ($hp === '') {
                $errors['engine_horsepower'] = 'Engine horsepower is required for motorized boats.';
            } elseif (!is_numeric($hp) || (int)$hp < 1) {
                $errors['engine_horsepower'] = 'Engine horsepower must be a positive integer.';
            }
        }

        // Dimensions
        foreach (['boat_length', 'boat_width', 'boat_depth'] as $dim) {
            $val = trim($row[$dim] ?? '');
            if ($val === '') {
                $errors[$dim] = ucfirst(str_replace('_', ' ', $dim)) . ' is required.';
            } elseif (!is_numeric($val) || (float)$val <= 0) {
                $errors[$dim] = ucfirst(str_replace('_', ' ', $dim)) . ' must be a positive number.';
            }
        }

        // Primary fishing gear
        $gearRaw = strtolower(trim($row['primary_fishing_gear'] ?? ''));
        if ($gearRaw === '') {
            $errors['primary_fishing_gear'] = 'Primary fishing gear is required.';
        } elseif (!isset(self::FISHING_GEAR_MAP[$gearRaw])) {
            $errors['primary_fishing_gear'] = "Unrecognised fishing gear: \"{$row['primary_fishing_gear']}\".";
        }

        return $errors;
    }

    private function createApplication(array $row): BoatrApplication
    {
        $boatType       = self::BOAT_TYPE_MAP[strtolower(trim($row['boat_type']))];
        $classification = self::BOAT_CLASSIFICATION_MAP[strtolower(trim($row['boat_classification']))];
        $fishingGear    = self::FISHING_GEAR_MAP[strtolower(trim($row['primary_fishing_gear']))];

        $statusRaw = strtolower(trim($row['status'] ?? ''));
        $status    = self::STATUS_MAP[$statusRaw] ?? 'pending';

        $barangayInput = trim($row['barangay']);
        $barangay      = collect(self::VALID_BARANGAYS)
            ->first(fn($b) => strcasecmp($b, $barangayInput) === 0, $barangayInput);

        $contact = preg_replace('/\D/', '', trim($row['contact_number']));

        $engineType = null;
        $engineHp   = null;
        if ($classification === 'Motorized') {
            $engineType = trim($row['engine_type'] ?? '') ?: null;
            $engineHp   = (int)(trim($row['engine_horsepower'] ?? 0));
        }

        return BoatrApplication::create([
            'application_number'   => $this->generateApplicationNumber($classification),
            'first_name'           => $this->capitaliseName(trim($row['first_name'])),
            'middle_name'          => $this->capitaliseName(trim($row['middle_name'] ?? '')),
            'last_name'            => $this->capitaliseName(trim($row['last_name'])),
            'name_extension'       => trim($row['name_extension'] ?? '') ?: null,
            'contact_number'       => $contact,
            'barangay'             => $barangay,
            'fishr_number'         => strtoupper(trim($row['fishr_number'])),
            'vessel_name'          => trim($row['vessel_name']),
            'boat_type'            => $boatType,
            'boat_classification'  => $classification,
            'boat_length'          => (float)trim($row['boat_length']),
            'boat_width'           => (float)trim($row['boat_width']),
            'boat_depth'           => (float)trim($row['boat_depth']),
            'engine_type'          => $engineType,
            'engine_horsepower'    => $engineHp,
            'primary_fishing_gear' => $fishingGear,
            'status'               => $status,
            'remarks'              => trim($row['remarks'] ?? '') ?: null,
        ]);
    }

    private function generateApplicationNumber(string $boatClassification): string
    {
        $year = now()->year;
        $prefix = $boatClassification === 'Motorized' ? 'M' : 'NM';
        $pattern = "BOATR-{$year}-{$prefix}-%";

        $last = BoatrApplication::where('application_number', 'like', $pattern)
            ->orderByDesc('application_number')
            ->value('application_number');

        $nextSequence = $last
            ? (int) substr($last, strrpos($last, '-') + 1) + 1
            : 1;

        if ($nextSequence > 999) {
            throw new \Exception("BoatR application number limit reached for {$prefix} classification in year {$year}.");
        }

        return "BOATR-{$year}-{$prefix}-" . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
    }

    private function capitaliseName(string $name): string
    {
        if ($name === '') {
            return '';
        }

        return implode(' ', array_map(
            fn($word) => mb_strtoupper(mb_substr($word, 0, 1)) . mb_strtolower(mb_substr($word, 1)),
            explode(' ', $name)
        ));
    }
}