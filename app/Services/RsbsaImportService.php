<?php

namespace App\Services;

use App\Models\RsbsaApplication;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RsbsaImportService
{
    /**
     * Valid main livelihoods (case-insensitive mapping)
     */
    const LIVELIHOOD_MAP = [
        'farmer'              => 'Farmer',
        'farmworker'          => 'Farmworker/Laborer',
        'farmworker/laborer'  => 'Farmworker/Laborer',
        'farmworker laborer'  => 'Farmworker/Laborer',
        'laborer'             => 'Farmworker/Laborer',
        'fisherfolk'          => 'Fisherfolk',
        'fisher'              => 'Fisherfolk',
        'agri-youth'          => 'Agri-youth',
        'agri youth'          => 'Agri-youth',
        'agriyouth'           => 'Agri-youth',
        'youth'               => 'Agri-youth',
    ];

    /**
     * Valid statuses
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
     * Valid sex values
     */
    const SEX_MAP = [
        'male'                 => 'Male',
        'female'               => 'Female',
        'preferred not to say' => 'Preferred not to say',
        'n/a'                  => 'Preferred not to say',
        'other'                => 'Preferred not to say',
    ];

    /**
     * Required CSV column headers
     */
    const REQUIRED_HEADERS = [
        'first_name', 'last_name', 'sex', 'contact_number',
        'barangay', 'address', 'main_livelihood',
    ];

    /**
     * Process uploaded import file (CSV or Excel)
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

    // ----------------------------------------------------------------
    // Parsing helpers
    // ----------------------------------------------------------------

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

        $headers = array_map(fn($h) => $this->normaliseHeader((string) $h), array_shift($data));
        $rows    = [];

        foreach ($data as $line) {
            if (count(array_filter($line, fn($v) => trim((string) $v) !== '')) === 0) {
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

    // ----------------------------------------------------------------
    // Validation helpers
    // ----------------------------------------------------------------

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

                Log::error('RSBSA import row failed', [
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

        // Sex
        $sexRaw = strtolower(trim($row['sex'] ?? ''));
        if ($sexRaw === '') {
            $errors['sex'] = 'Sex is required.';
        } elseif (!isset(self::SEX_MAP[$sexRaw])) {
            $errors['sex'] = "Invalid sex value: \"{$row['sex']}\". Use Male, Female, or Preferred not to say.";
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

        // Address
        $address = trim($row['address'] ?? '');
        if ($address === '') {
            $errors['address'] = 'Address is required.';
        }

        // Main livelihood
        $livelihoodRaw = strtolower(trim($row['main_livelihood'] ?? ''));
        if ($livelihoodRaw === '') {
            $errors['main_livelihood'] = 'Main livelihood is required.';
        } elseif (!isset(self::LIVELIHOOD_MAP[$livelihoodRaw])) {
            $errors['main_livelihood'] = "Unrecognised livelihood: \"{$row['main_livelihood']}\".";
        }

        return $errors;
    }

    // ----------------------------------------------------------------
    // Record creation
    // ----------------------------------------------------------------

    private function createApplication(array $row): RsbsaApplication
    {
        $livelihood = self::LIVELIHOOD_MAP[strtolower(trim($row['main_livelihood']))];
        $sex        = self::SEX_MAP[strtolower(trim($row['sex']))];
        $statusRaw  = strtolower(trim($row['status'] ?? ''));
        $status     = self::STATUS_MAP[$statusRaw] ?? 'pending';

        // Resolve properly-cased barangay
        $barangayInput = trim($row['barangay']);
        $barangay = collect(self::VALID_BARANGAYS)
            ->first(fn($b) => strcasecmp($b, $barangayInput) === 0, $barangayInput);

        // Sanitise contact number
        $contact = preg_replace('/\D/', '', trim($row['contact_number']));

        $data = [
            'application_number'       => $this->generateApplicationNumber(),
            'first_name'               => $this->capitaliseName(trim($row['first_name'])),
            'middle_name'              => $this->capitaliseName(trim($row['middle_name'] ?? '')),
            'last_name'                => $this->capitaliseName(trim($row['last_name'])),
            'name_extension'           => trim($row['name_extension'] ?? '') ?: null,
            'sex'                      => $sex,
            'contact_number'           => $contact,
            'barangay'                 => $barangay,
            'address'                  => trim($row['address']),
            'main_livelihood'          => $livelihood,
            'commodity'                => trim($row['commodity'] ?? '') ?: null,
            'status'                   => $status,
            'supporting_document_path' => null,
        ];

        // Farmer-specific fields
        if ($livelihood === 'Farmer') {
            $data['farmer_crops']         = trim($row['farmer_crops'] ?? '') ?: null;
            $data['farmer_land_area']     = is_numeric($row['farmer_land_area'] ?? '') ? (float) $row['farmer_land_area'] : null;
            $data['farmer_type_of_farm']  = trim($row['farmer_type_of_farm'] ?? '') ?: null;
            $data['farmer_land_ownership']= trim($row['farmer_land_ownership'] ?? '') ?: null;
            $data['farmer_special_status']= trim($row['farmer_special_status'] ?? '') ?: null;
            $data['farm_location']        = trim($row['farm_location'] ?? '') ?: null;
        }

        // Farmworker-specific fields
        if ($livelihood === 'Farmworker/Laborer') {
            $data['farmworker_type'] = trim($row['farmworker_type'] ?? '') ?: null;
        }

        // Fisherfolk-specific fields
        if ($livelihood === 'Fisherfolk') {
            $data['fisherfolk_activity'] = trim($row['fisherfolk_activity'] ?? '') ?: null;
        }

        // Agri-youth-specific fields
        if ($livelihood === 'Agri-youth') {
            $data['agriyouth_farming_household'] = trim($row['agriyouth_farming_household'] ?? '') ?: null;
            $data['agriyouth_training']          = trim($row['agriyouth_training'] ?? '') ?: null;
            $data['agriyouth_participation']     = trim($row['agriyouth_participation'] ?? '') ?: null;
        }

        return RsbsaApplication::create($data);
    }

    private function generateApplicationNumber(): string
    {
        $year = now()->year;

        $last = RsbsaApplication::where('application_number', 'like', "RSBSA-{$year}-%")
            ->orderByDesc('application_number')
            ->value('application_number');

        $nextSequence = $last
            ? (int) substr($last, strrpos($last, '-') + 1) + 1
            : 1;

        if ($nextSequence > 9999) {
            throw new \Exception("RSBSA application number limit reached for year {$year}.");
        }

        return "RSBSA-{$year}-" . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
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