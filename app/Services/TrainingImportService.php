<?php

namespace App\Services;

use App\Models\TrainingApplication;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TrainingImportService
{
    /**
     * Valid training types mapped from human-readable labels
     */
    const TRAINING_TYPE_MAP = [
        'tilapia and hito'        => 'tilapia_hito',
        'tilapia_hito'            => 'tilapia_hito',
        'tilapia'                 => 'tilapia_hito',
        'hydroponics'             => 'hydroponics',
        'aquaponics'              => 'aquaponics',
        'mushrooms production'    => 'mushrooms',
        'mushrooms'               => 'mushrooms',
        'livestock and poultry'   => 'livestock_poultry',
        'livestock_poultry'       => 'livestock_poultry',
        'livestock'               => 'livestock_poultry',
        'high value crops'        => 'high_value_crops',
        'high_value_crops'        => 'high_value_crops',
        'sampaguita propagation'  => 'sampaguita_propagation',
        'sampaguita_propagation'  => 'sampaguita_propagation',
        'sampaguita'              => 'sampaguita_propagation',
    ];

    /**
     * Valid statuses mapped from human-readable labels
     */
    const STATUS_MAP = [
        'pending'      => 'pending',
        'under review' => 'under_review',
        'under_review' => 'under_review',
        'approved'     => 'approved',
        'rejected'     => 'rejected',
    ];

    /**
     * Valid barangays
     */
    const VALID_BARANGAYS = [
        'Bagong Silang', 'Calendola', 'Chrysanthemum', 'Cuyab', 'Estrella',
        'Fatima', 'G.S.I.S.', 'Landayan', 'Langgam', 'Laram', 'Magsaysay',
        'Maharlika', 'Narra', 'Nueva', 'Pacita 1', 'Pacita 2', 'Poblacion',
        'Riverside', 'Rosario', 'Sampaguita Village', 'San Antonio',
        'San Lorenzo Ruiz', 'San Roque', 'San Vicente', 'Santo Niño',
        'United Bayanihan', 'United Better Living',
    ];

    /**
     * Expected CSV column headers (case-insensitive)
     */
    const REQUIRED_HEADERS = [
        'first_name', 'last_name', 'contact_number', 'barangay', 'training_type'
    ];

    /**
     * Process uploaded import file (CSV or Excel via CSV export)
     *
     * @param  string  $filePath  Absolute path to the uploaded file
     * @return array{
     *   imported: int,
     *   skipped: int,
     *   errors: array,
     *   rows: array
     * }
     */
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

    /**
     * Parse CSV file into an array of associative rows
     */
    private function parseCsv(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \RuntimeException('Could not open the uploaded file.');
        }

        $rows    = [];
        $headers = null;

        while (($line = fgetcsv($handle, 0, ',')) !== false) {
            // Skip completely empty lines
            if (count(array_filter($line, fn($v) => trim($v) !== '')) === 0) {
                continue;
            }

            if ($headers === null) {
                // First non-empty line = headers; normalise to snake_case lowercase
                $headers = array_map(fn($h) => $this->normaliseHeader($h), $line);
                continue;
            }

            // Pad short lines to match header count
            while (count($line) < count($headers)) {
                $line[] = '';
            }

            $rows[] = array_combine($headers, array_slice($line, 0, count($headers)));
        }

        fclose($handle);
        return $rows;
    }

    /**
     * Parse Excel file using PhpSpreadsheet (if available) or fall back to CSV conversion
     */
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

    /**
     * Normalise a header string to snake_case lowercase
     */
    private function normaliseHeader(string $header): string
    {
        // Strip BOM, trim whitespace, lower-case, replace spaces/dashes with underscore
        $header = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $header); // strip non-printable
        $header = trim($header);
        $header = strtolower($header);
        $header = preg_replace('/[\s\-]+/', '_', $header);
        return $header;
    }

    /**
     * Validate headers; throws if required columns are missing
     */
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

    /**
     * Validate and import all rows
     */
    private function processRows(array $rows): array
    {
        if (empty($rows)) {
            throw new \InvalidArgumentException('The file is empty or contains no data rows.');
        }

        // Validate headers using the first row's keys
        $this->validateHeaders(array_keys($rows[0]));

        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because row 1 is headers, arrays are 0-indexed
            $rowErrors = $this->validateRow($row, $rowNumber);

            if (!empty($rowErrors)) {
                $skipped++;
                $errors[] = [
                    'row'    => $rowNumber,
                    'data'   => $row,
                    'errors' => $rowErrors,
                ];
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

                Log::error('Training import row failed', [
                    'row'   => $rowNumber,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return compact('imported', 'skipped', 'errors');
    }

    /**
     * Validate a single row; returns array of field => message errors
     */
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

        // Training type
        $typeRaw = strtolower(trim($row['training_type'] ?? ''));
        if ($typeRaw === '') {
            $errors['training_type'] = 'Training type is required.';
        } elseif (!isset(self::TRAINING_TYPE_MAP[$typeRaw])) {
            $errors['training_type'] = "Unrecognised training type: \"{$row['training_type']}\".";
        }

        return $errors;
    }

    /**
     * Create a TrainingApplication from a validated row
     */
    private function createApplication(array $row): TrainingApplication
    {
        // Resolve training type
        $trainingType = self::TRAINING_TYPE_MAP[strtolower(trim($row['training_type']))];

        // Resolve status (default: pending)
        $statusRaw = strtolower(trim($row['status'] ?? ''));
        $status    = self::STATUS_MAP[$statusRaw] ?? 'pending';

        // Resolve barangay (use properly-cased version from our list)
        $barangayInput = trim($row['barangay']);
        $barangay = collect(self::VALID_BARANGAYS)
            ->first(fn($b) => strcasecmp($b, $barangayInput) === 0, $barangayInput);

        // Sanitise contact number
        $contact = preg_replace('/\D/', '', trim($row['contact_number']));

        return TrainingApplication::create([
            'application_number' => $this->generateApplicationNumber(),
            'first_name'         => $this->capitaliseName(trim($row['first_name'])),
            'middle_name'        => $this->capitaliseName(trim($row['middle_name'] ?? '')),
            'last_name'          => $this->capitaliseName(trim($row['last_name'])),
            'name_extension'     => trim($row['name_extension'] ?? '') ?: null,
            'contact_number'     => $contact,
            'barangay'           => $barangay,
            'training_type'      => $trainingType,
            'status'             => $status,
            'remarks'            => trim($row['remarks'] ?? '') ?: null,
            'document_path'      => null,
        ]);
    }

    /**
     * Generate a unique application number
     */
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

    /**
     * Title-case a name string
     */
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