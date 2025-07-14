<?php

namespace App\Http\Controllers;

use App\Models\SeedlingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ApplicationController extends Controller
{
    /**
     * Submit a new seedling request
     */
    public function submitSeedlings(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'last_name' => 'required|string|max:255',
                'mobile' => 'required|string|max:20',
                'barangay' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'selected_seedlings' => 'required|string',
                'supporting_documents' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240'
            ]);

            // Parse selected seedlings
            $selectedSeedlings = json_decode($validated['selected_seedlings'], true);

            if (!$selectedSeedlings) {
                throw new \Exception('Invalid seedlings selection data');
            }

            // Handle file upload
            $documentPath = null;
            if ($request->hasFile('supporting_documents')) {
                $documentPath = $request->file('supporting_documents')->store('seedling_documents', 'public');
            }

            // Create the seedling request
            $seedlingRequest = SeedlingRequest::create([
                'request_number' => 'SEED-' . strtoupper(Str::random(8)),
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'last_name' => $validated['last_name'],
                'contact_number' => $validated['mobile'],
                'address' => $validated['address'],
                'barangay' => $validated['barangay'],
                'seedling_type' => $this->formatSeedlingTypes($selectedSeedlings),
                'vegetables' => $selectedSeedlings['vegetables'] ?? [],
                'fruits' => $selectedSeedlings['fruits'] ?? [],
                'fertilizers' => $selectedSeedlings['fertilizers'] ?? [],
                'requested_quantity' => $selectedSeedlings['totalQuantity'] ?? 0,
                'total_quantity' => $selectedSeedlings['totalQuantity'] ?? 0,
                'document_path' => $documentPath,
                'status' => 'under_review'
            ]);

            Log::info('Seedling request created successfully', [
                'request_number' => $seedlingRequest->request_number,
                'total_quantity' => $seedlingRequest->total_quantity
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Your seedling request has been submitted successfully! Request Number: ' . $seedlingRequest->request_number . '. You will receive an SMS notification once your request is processed.',
                    'request_number' => $seedlingRequest->request_number
                ]);
            }

            return redirect()->route('landing.page')->with('success',
                'Your seedling request has been submitted successfully! Request Number: ' . $seedlingRequest->request_number .
                '. You will receive an SMS notification once your request is processed.'
            );

        } catch (\Exception $e) {
            Log::error('Seedling request error: ' . $e->getMessage(), [
                'request_data' => $request->all()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'There was an error submitting your request. Please try again.'
                ], 500);
            }

            return redirect()->back()->with('error',
                'There was an error submitting your request. Please try again.'
            )->withInput();
        }
    }

    /**
     * Format seedling types for display
     */
    private function formatSeedlingTypes($selectedSeedlings): string
    {
        $types = [];

        if (!empty($selectedSeedlings['vegetables'])) {
            $vegNames = collect($selectedSeedlings['vegetables'])->pluck('name')->toArray();
            $types[] = 'Vegetables: ' . implode(', ', $vegNames);
        }

        if (!empty($selectedSeedlings['fruits'])) {
            $fruitNames = collect($selectedSeedlings['fruits'])->pluck('name')->toArray();
            $types[] = 'Fruits: ' . implode(', ', $fruitNames);
        }

        if (!empty($selectedSeedlings['fertilizers'])) {
            $fertNames = collect($selectedSeedlings['fertilizers'])->pluck('name')->toArray();
            $types[] = 'Fertilizers: ' . implode(', ', $fertNames);
        }

        return implode(' | ', $types);
    }

    /**
     * Submit RSBSA request (placeholder)
     */
    public function submitRsbsa(Request $request)
    {
        // TODO: Implement RSBSA submission
        return redirect()->back()->with('success', 'RSBSA request submitted successfully!');
    }

    /**
     * Submit Fish Registration request (placeholder)
     */
    public function submitFishR(Request $request)
    {
        // TODO: Implement Fish Registration submission
        return redirect()->back()->with('success', 'Fish Registration submitted successfully!');
    }

    /**
     * Submit Boat Registration request (placeholder)
     */
    public function submitBoatR(Request $request)
    {
        // TODO: Implement Boat Registration submission
        return redirect()->back()->with('success', 'Boat Registration submitted successfully!');
    }
}
