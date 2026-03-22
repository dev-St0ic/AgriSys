<?php

namespace App\Services;

use App\Models\FishrApplication;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FishrImportService
{
    /**
     * Valid main/secondary livelihoods (slug form)
     */
    const LIVELIHOOD_MAP = [
        'capture fishing'   => 'capture',
        'capture'           => 'capture',
        'aquaculture'       => 'aquaculture',
        'fish vending'      => 'vending',
        'vending'           => 'vending',
        'fish processing'   => 'processing',
        'processing'        => 'processing',
        'others'            => 'others',
        'other'             => 'others',
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
     * Valid sex values
     */
    const SEX_MAP = [
        'male'                => 'Male',
        'female'              => 'Female',
        'preferred not to say'=> 'Preferred not to say',
        'n/a'                 => 'Preferred not to say',
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
     * Required CSV headers
     */
    const REQUIRED_HEADERS = [
        'first_name', 'last_name', 'sex', 'contact_number', 'barangay', 'main_livelihood',
    ];

    // ----------------------------------------------------------------
    // Public entry point
    // ----------------------------------------------------------------

    /**
     * Process uploaded import file (CSV or Excel).
     *
     * @param  string  $filePath
     * @param  string  $extension
     * @return array{imported: int, skipped: int, errors: array}
     */
    public function import(string $filePath, string $extension = ''): array
    {
        $extension = strtolower($extension ?: pathinfo($filePath, PATHINFO_EXTENSION));

        if (in_array($extension, ['csv', 'txt', ''])) {
            $rows = $this->parseCsv($filePath);
        } elseif (in_array($extension, ['xlsx', 'xls'])) {
            $rows = $this->parseExcel($filePath);
        } else {
            throw new \InvalidArgumentException(
                'Unsupported file type. Please upload a CSV or Excel file.'
            );
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
            // Skip blank lines
            if (count(array_filter($line, fn($v) => trim((string)$v) !== '')) === 0) {
                continue;
            }

            if ($headers === null) {
                $headers = array_map(fn($h) => $this->normaliseHeader($h), $line);
                continue;
            }

            // Pad short rows
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

        $headers = array_map(
            fn($h) => $this->normaliseHeader((string)$h),
            array_shift($data)
        );

        $rows = [];
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

    // ----------------------------------------------------------------
    // Normalisation helpers
    // ----------------------------------------------------------------

    private function normaliseHeader(string $header): string
    {
        $header = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $header);
        $header = trim($header);
        $header = strtolower($header);
        $header = preg_replace('/[\s\-]+/', '_', $header);
        return $header;
    }

    private function validateHeaders(array $headers): void
    {
        $missing = array_diff(self::REQUIRED_HEADERS, $headers);
        if (!empty($missing)) {
            throw new \InvalidArgumentException(
                'The file is missing required columns: ' . implode(', ', $missing) .
                '. Please use the provided template.'
            );
        }
    }

    // ----------------------------------------------------------------
    // Row processing
    // ----------------------------------------------------------------

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
            $rowNumber = $index + 2; // row 1 = headers
            $rowErrors = $this->validateRow($row, $rowNumber);

            if (!empty($rowErrors)) {
                $skipped++;
                $errors[] = ['row' => $rowNumber, 'data' => $row, 'errors' => $rowErrors];
                continue;
            }

            try {
                $this->createRegistration($row);
                $imported++;
            } catch (\Exception $e) {
                $skipped++;
                $errors[] = [
                    'row'    => $rowNumber,
                    'data'   => $row,
                    'errors' => ['system' => 'Failed to save: ' . $e->getMessage()],
                ];

                Log::error('FishR import row failed', [
                    'row'   => $rowNumber,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return compact('imported', 'skipped', 'errors');
    }

    // ----------------------------------------------------------------
    // Row validation
    // ----------------------------------------------------------------

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

        // Main livelihood
        $mainRaw = strtolower(trim($row['main_livelihood'] ?? ''));
        if ($mainRaw === '') {
            $errors['main_livelihood'] = 'Main livelihood is required.';
        } elseif (!isset(self::LIVELIHOOD_MAP[$mainRaw])) {
            $errors['main_livelihood'] = "Unrecognised livelihood: \"{$row['main_livelihood']}\".";
        }

        // Other livelihood required if main = others
        if (($mainRaw === 'others' || $mainRaw === 'other') && trim($row['other_livelihood'] ?? '') === '') {
            $errors['other_livelihood'] = 'Please specify the other livelihood.';
        }

        // Secondary livelihood (optional) — must differ from main
        $secondaryRaw = strtolower(trim($row['secondary_livelihood'] ?? ''));
        if ($secondaryRaw !== '' && $secondaryRaw !== 'n/a' && $secondaryRaw !== 'none') {
            if (!isset(self::LIVELIHOOD_MAP[$secondaryRaw])) {
                $errors['secondary_livelihood'] = "Unrecognised secondary livelihood: \"{$row['secondary_livelihood']}\".";
            } elseif (isset(self::LIVELIHOOD_MAP[$mainRaw]) &&
                      self::LIVELIHOOD_MAP[$secondaryRaw] === self::LIVELIHOOD_MAP[$mainRaw]) {
                $errors['secondary_livelihood'] = 'Secondary livelihood cannot be the same as main livelihood.';
            }

            // Other secondary livelihood required if secondary = others
            if (in_array($secondaryRaw, ['others', 'other']) && trim($row['other_secondary_livelihood'] ?? '') === '') {
                $errors['other_secondary_livelihood'] = 'Please specify the other secondary livelihood.';
            }
        }

        return $errors;
    }

    // ----------------------------------------------------------------
    // Record creation
    // ----------------------------------------------------------------

    private function createRegistration(array $row): FishrApplication
    {
        // Resolve main livelihood
        $mainLivelihood = self::LIVELIHOOD_MAP[strtolower(trim($row['main_livelihood']))];

        $livelihoodMap = [
            'capture'     => 'Capture Fishing',
            'aquaculture' => 'Aquaculture',
            'vending'     => 'Fish Vending',
            'processing'  => 'Fish Processing',
            'others'      => trim($row['other_livelihood'] ?? 'Others') ?: 'Others',
        ];
        $livelihoodDescription = $livelihoodMap[$mainLivelihood];

        // Resolve secondary livelihood (optional)
        $secondaryRaw        = strtolower(trim($row['secondary_livelihood'] ?? ''));
        $secondaryLivelihood = null;
        $secondaryDesc       = null;
        $otherSecondary      = null;

        if ($secondaryRaw !== '' && $secondaryRaw !== 'n/a' && $secondaryRaw !== 'none' &&
            isset(self::LIVELIHOOD_MAP[$secondaryRaw])) {
            $secondaryLivelihood = self::LIVELIHOOD_MAP[$secondaryRaw];
            $secondaryMap = [
                'capture'     => 'Capture Fishing',
                'aquaculture' => 'Aquaculture',
                'vending'     => 'Fish Vending',
                'processing'  => 'Fish Processing',
                'others'      => trim($row['other_secondary_livelihood'] ?? 'Others') ?: 'Others',
            ];
            $secondaryDesc  = $secondaryMap[$secondaryLivelihood];
            $otherSecondary = ($secondaryLivelihood === 'others')
                ? (trim($row['other_secondary_livelihood'] ?? '') ?: null)
                : null;
        }

        // Resolve sex
        $sex = self::SEX_MAP[strtolower(trim($row['sex']))] ?? 'Preferred not to say';

        // Resolve status (default: pending)
        $statusRaw = strtolower(trim($row['status'] ?? ''));
        $status    = self::STATUS_MAP[$statusRaw] ?? 'pending';

        // Resolve barangay (properly cased)
        $barangayInput = trim($row['barangay']);
        $barangay = collect(self::VALID_BARANGAYS)
            ->first(fn($b) => strcasecmp($b, $barangayInput) === 0, $barangayInput);

        // Sanitise contact number
        $contact = preg_replace('/\D/', '', trim($row['contact_number']));

        return FishrApplication::create([
            'registration_number'              => $this->generateRegistrationNumber(),
            'first_name'                       => $this->capitaliseName(trim($row['first_name'])),
            'middle_name'                      => $this->capitaliseName(trim($row['middle_name'] ?? '')),
            'last_name'                        => $this->capitaliseName(trim($row['last_name'])),
            'name_extension'                   => trim($row['name_extension'] ?? '') ?: null,
            'sex'                              => $sex,
            'contact_number'                   => $contact,
            'barangay'                         => $barangay,
            'main_livelihood'                  => $mainLivelihood,
            'other_livelihood'                 => ($mainLivelihood === 'others')
                ? (trim($row['other_livelihood'] ?? '') ?: null)
                : null,
            'livelihood_description'           => $livelihoodDescription,
            'secondary_livelihood'             => $secondaryLivelihood,
            'other_secondary_livelihood'       => $otherSecondary,
            'secondary_livelihood_description' => $secondaryDesc,
            'status'                           => $status,
            'remarks'                          => trim($row['remarks'] ?? '') ?: null,
            'document_path'                    => null,
        ]);
    }

    // ----------------------------------------------------------------
    // Utility helpers
    // ----------------------------------------------------------------

    private function generateRegistrationNumber(): string
    {
    $year = now()->year;

    $last = FishrApplication::where('registration_number', 'like', "FISHR-{$year}-%")
        ->orderByDesc('registration_number')
        ->value('registration_number');

    $nextSequence = $last
        ? (int) substr($last, strrpos($last, '-') + 1) + 1
        : 1;

    if ($nextSequence > 9999) {
        throw new \Exception("FishR registration number limit reached for year {$year}.");
    }

    return "FISHR-{$year}-" . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
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