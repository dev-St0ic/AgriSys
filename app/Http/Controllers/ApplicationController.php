<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    /**
     * Handle RSBSA registration application
     */
    public function submitRsbsa(Request $request)
    {
        $request->validate([
            'applicant_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'barangay' => 'required|string|max:255',
            'address' => 'required|string',
            'registration_type' => 'required|in:new,renewal',
        ]);

        // Here you would typically save to database
        // For now, we'll just redirect with a success message

        return redirect()->back()->with('success', 'RSBSA application submitted successfully! We will contact you soon.');
    }

    /**
     * Handle Seedlings request application
     */
    public function submitSeedlings(Request $request)
    {
        $request->validate([
            'applicant_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'barangay' => 'required|string|max:255',
            'address' => 'required|string',
        ]);

        // Here you would typically save to database
        // For now, we'll just redirect with a success message

        return redirect()->back()->with('success', 'Seedlings request submitted successfully! We will process your request soon.');
    }

    /**
     * Handle FishR registration application
     */
    public function submitFishR(Request $request)
    {
        $request->validate([
            'applicant_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'barangay' => 'required|string|max:255',
            'address' => 'required|string',
            'livelihood' => 'required|string',
        ]);

        // Here you would typically save to database
        // For now, we'll just redirect with a success message

        return redirect()->back()->with('success', 'FishR registration submitted successfully! We will contact you soon.');
    }

    /**
     * Handle BoatR registration application
     */
    public function submitBoatR(Request $request)
    {
        $request->validate([
            'applicant_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'barangay' => 'required|string|max:255',
            'address' => 'required|string',
            'boat_type' => 'required|string',
        ]);

        // Here you would typically save to database
        // For now, we'll just redirect with a success message

        return redirect()->back()->with('success', 'BoatR registration submitted successfully! We will contact you soon.');
    }
}
