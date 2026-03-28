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
        'category_1', 'item_1', 'quantity_1',
    ];

    const MAX_ITEMS = 5;

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

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $items     = $this->extractItemsFromRow($row);
            $rowErrors = $this->validatePersonRow($row, $rowNumber, $items);

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
                $this->createRequest($row, $items);
                $imported++;
            } catch (\Exception $e) {
                $skipped++;
                $errors[] = [
                    'row'    => $rowNumber,
                    'data'   => $row,
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

    private function extractItemsFromRow(array $row): array
    {
        $items = [];

        for ($i = 1; $i <= self::MAX_ITEMS; $i++) {
            $itemName = trim($row["item_{$i}"] ?? '');

            // Item name is the anchor — if it's empty, skip this slot entirely
            if ($itemName === '') {
                continue;
            }

            $category = trim($row["category_{$i}"] ?? '');
            $quantity  = trim($row["quantity_{$i}"] ?? '');

            $items[] = [
                'slot'      => $i,
                'item_name' => $itemName,
                'category'  => $category,
                'quantity'  => $quantity,
            ];
        }

        return $items;
    }

    private function validatePersonRow(array $row, int $rowNumber, array $items): array
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

        // Must have at least one item
        if (empty($items)) {
            $errors['items'] = 'At least one item (category_1, item_1, quantity_1) is required.';
        }

        // Validate each item slot
        $validCategories = ['seeds', 'seedlings'];
        foreach ($items as $itemData) {
            $i        = $itemData['slot'];
            $itemName = $itemData['item_name'];
            $category = $itemData['category'];
            $quantity  = $itemData['quantity'];

            if ($itemName === '') {
                $errors["item_{$i}"] = "Item {$i}: item name is required.";
            }

            if ($category === '') {
                $errors["category_{$i}"] = "Item {$i}: category is required.";
            } elseif (!in_array(strtolower($category), $validCategories)) {
                $errors["category_{$i}"] = "Item {$i}: invalid category \"{$category}\". Must be Seeds or Seedlings.";
            }

            if ($quantity === '') {
                $errors["quantity_{$i}"] = "Item {$i}: quantity is required.";
            } elseif (!is_numeric($quantity) || (int)$quantity < 1) {
                $errors["quantity_{$i}"] = "Item {$i}: quantity must be a positive number.";
            }
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
            $category = trim($itemData['category'] ?? '');
            $quantity = (int) $itemData['quantity'];

            // Match by name AND category
            $categoryItem = CategoryItem::with('category')
                ->whereRaw('LOWER(name) = ?', [strtolower($itemName)])
                ->whereHas('category', function($q) use ($category) {
                    $q->whereRaw('LOWER(name) = ?', [strtolower($category)]);
                })
                ->first();

            // Fallback: match by name only if category not found
            if (!$categoryItem && $category === '') {
                $categoryItem = CategoryItem::with('category')
                    ->whereRaw('LOWER(name) = ?', [strtolower($itemName)])
                    ->first();
            }

            $request->items()->create([
                'category_item_id'   => $categoryItem?->id,
                'category_id'        => $categoryItem?->category_id,
                'item_name'          => $categoryItem ? $categoryItem->name : $itemName,
                'item_unit'          => $categoryItem?->unit,
                'category_name'      => $categoryItem?->category?->display_name,
                'category_icon'      => $categoryItem?->category?->icon,
                'requested_quantity' => $quantity,
                'status'             => 'pending',
            ]);
        }

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

        return "REQ-{$year}-" . str_pad($nextSequence, 5, '0', STR_PAD_LEFT);
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