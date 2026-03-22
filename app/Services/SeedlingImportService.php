<?php

namespace App\Services;

use App\Models\SeedlingRequest;
use App\Models\Category;
use App\Models\CategoryItem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SeedlingImportService
{
    /**
     * Valid statuses
     */
    const STATUS_MAP = [
        'pending'            => 'pending',
        'under review'       => 'under_review',
        'under_review'       => 'under_review',
        'approved'           => 'approved',
        'partially approved' => 'partially_approved',
        'partially_approved' => 'partially_approved',
        'rejected'           => 'rejected',
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
     * Required CSV column headers
     */
    const REQUIRED_HEADERS = [
        'first_name', 'last_name', 'contact_number', 'barangay',
        'item_name', 'quantity',
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
                'PhpSpreadsheet is not installed. Please use a CSV file instead.'
            );
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $sheet       = $spreadsheet->getActiveSheet();
        $data        = $sheet->toArray(null, true, true, false);

        if (empty($data)) return [];

        $headers = array_map(fn($h) => $this->normaliseHeader((string)$h), array_shift($data));
        $rows    = [];

        foreach ($data as $line) {
            if (count(array_filter($line, fn($v) => trim((string)$v) !== '')) === 0) continue;
            while (count($line) < count($headers)) $line[] = '';
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

        // Group rows by person (same first+last+contact = same request)
        $grouped = $this->groupRowsByPerson($rows);

        foreach ($grouped as $index => $group) {
            $rowNumber = $group['row_number'];
            $personRow = $group['person'];
            $items     = $group['items'];

            $rowErrors = $this->validatePersonRow($personRow, $rowNumber);

            if (!empty($rowErrors)) {
                $skipped++;
                $errors[] = [
                    'row'    => $rowNumber,
                    'data'   => $personRow,
                    'errors' => $rowErrors,
                ];
                continue;
            }

            try {
                $this->createRequest($personRow, $items);
                $imported++;
            } catch (\Exception $e) {
                $skipped++;
                $errors[] = [
                    'row'    => $rowNumber,
                    'data'   => $personRow,
                    'errors' => ['system' => 'Failed to save: ' . $e->getMessage()],
                ];

                Log::error('Seedling import row failed', [
                    'row'   => $rowNumber,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return compact('imported', 'skipped', 'errors');
    }

    /**
     * Group multiple item rows belonging to the same person into one request.
     * A person is identified by: first_name + last_name + contact_number.
     * If each row has a unique person, each row becomes its own request.
     */
    private function groupRowsByPerson(array $rows): array
    {
        $groups = [];
        $keyMap = []; // key => index in $groups

        foreach ($rows as $index => $row) {
            $key = strtolower(trim($row['first_name'] ?? '')) . '|' .
                   strtolower(trim($row['last_name']  ?? '')) . '|' .
                   preg_replace('/\D/', '', trim($row['contact_number'] ?? ''));

            $itemName = trim($row['item_name'] ?? '');
            $quantity = trim($row['quantity']  ?? '');

            if (!isset($keyMap[$key])) {
                $keyMap[$key]  = count($groups);
                $groups[]      = [
                    'row_number' => $index + 2,
                    'person'     => $row,
                    'items'      => [],
                ];
            }

            if ($itemName !== '') {
                $groups[$keyMap[$key]]['items'][] = [
                    'item_name' => $itemName,
                    'quantity'  => $quantity,
                ];
            }
        }

        return $groups;
    }

    private function validatePersonRow(array $row, int $rowNumber): array
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

        // Item name
        $itemName = trim($row['item_name'] ?? '');
        if ($itemName === '') {
            $errors['item_name'] = 'Item name is required.';
        }

        // Quantity
        $quantity = trim($row['quantity'] ?? '');
        if ($quantity === '') {
            $errors['quantity'] = 'Quantity is required.';
        } elseif (!is_numeric($quantity) || (int)$quantity < 1) {
            $errors['quantity'] = 'Quantity must be a positive number.';
        }

        return $errors;
    }

    /**
     * Create a SeedlingRequest with its items from validated row data
     */
    private function createRequest(array $row, array $itemsData): \App\Models\SeedlingRequest
    {
        // Resolve status
        $statusRaw = strtolower(trim($row['status'] ?? ''));
        $status    = self::STATUS_MAP[$statusRaw] ?? 'pending';

        // Resolve barangay
        $barangayInput = trim($row['barangay']);
        $barangay = collect(self::VALID_BARANGAYS)
            ->first(fn($b) => strcasecmp($b, $barangayInput) === 0, $barangayInput);

        // Sanitise contact
        $contact = preg_replace('/\D/', '', trim($row['contact_number']));

        // Resolve pickup date
        $pickupDate = null;
        if (!empty(trim($row['pickup_date'] ?? ''))) {
            try {
                $pickupDate = \Carbon\Carbon::parse(trim($row['pickup_date']))->format('Y-m-d');
            } catch (\Exception $e) {
                $pickupDate = null;
            }
        }

        // Create the seedling request
        $request = \App\Models\SeedlingRequest::create([
            'request_number' => $this->generateRequestNumber(),
            'first_name'     => $this->capitaliseName(trim($row['first_name'])),
            'middle_name'    => $this->capitaliseName(trim($row['middle_name'] ?? '')),
            'last_name'      => $this->capitaliseName(trim($row['last_name'])),
            'extension_name' => trim($row['extension_name'] ?? '') ?: null,
            'contact_number' => $contact,
            'barangay'       => $barangay,
            'status'         => $status,
            'remarks'        => trim($row['remarks'] ?? '') ?: null,
            'pickup_date'    => $pickupDate,
            'document_path'  => null,
        ]);

        // Create items — try to match each item to a CategoryItem
        foreach ($itemsData as $itemData) {
            $itemName = trim($itemData['item_name']);
            $quantity = (int) $itemData['quantity'];

            // Try to find matching CategoryItem by name (case-insensitive)
            $categoryItem = CategoryItem::whereRaw('LOWER(name) = ?', [strtolower($itemName)])->first();

            $request->items()->create([
                'category_item_id' => $categoryItem?->id,
                'category_id'      => $categoryItem?->category_id,
                'item_name'        => $categoryItem ? $categoryItem->name : $itemName,
                'category_name'    => $categoryItem?->category?->display_name,
                'category_icon'    => $categoryItem?->category?->icon,
                'requested_quantity' => $quantity,
                'status'           => 'pending',
            ]);
        }

        // Update total_quantity on the request
        $request->update([
            'total_quantity' => $request->items()->sum('requested_quantity'),
        ]);

        return $request;
    }

    private function generateRequestNumber(): string
    {
    $year = now()->year;

    $last = \App\Models\SeedlingRequest::where('request_number', 'like', "REQ-{$year}-%")
        ->orderByDesc('request_number')
        ->value('request_number');

    $nextSequence = $last
        ? (int) substr($last, strrpos($last, '-') + 1) + 1
        : 1;

    if ($nextSequence > 9999) {
        throw new \Exception("Seedling request number limit reached for year {$year}.");
    }

    return "REQ-{$year}-" . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
    }

    private function capitaliseName(string $name): string
    {
        if ($name === '') return '';

        return implode(' ', array_map(
            fn($word) => mb_strtoupper(mb_substr($word, 0, 1)) . mb_strtolower(mb_substr($word, 1)),
            explode(' ', $name)
        ));
    }
}