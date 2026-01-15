<?php

namespace App\Http\Controllers;

use App\Models\SlideshowImage;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the landing page with slideshow images
     */
    public function index()
    {
        // Get active slideshow images ordered by their order field
        $slideshowImages = SlideshowImage::active()->ordered()->get();

        // If no slideshow images exist, provide fallback images
        $fallbackImages = [
            (object)['image_url' => asset('images/hero/bg1.jpg'), 'title' => 'Welcome to AgriSys', 'description' => 'Agricultural Excellence'],
            (object)['image_url' => asset('images/hero/bg2.jpg'), 'title' => 'Community Support', 'description' => 'Supporting Local Farmers'],
            (object)['image_url' => asset('images/hero/bg3.jpg'), 'title' => 'Modern Agriculture', 'description' => 'Innovation in Farming'],
        ];

        // Use database images if available, otherwise use fallback
        $slides = $slideshowImages->count() > 0 ? $slideshowImages : collect($fallbackImages);

        // Get user session - set to null if not available (don't pass errors)
        $user = session('user', null);
        
        // Clear any error messages from session to prevent notification display
        session()->forget('error');
        session()->forget('errors');

        return view('landingPage.landing', compact('slides', 'user'));
    }

    /**
     * Display the user dashboard (same as landing page with user session)
     */
    public function dashboard()
    {
        $user = session('user', null);

        if (!$user) {
            // Return JSON for AJAX requests without triggering notifications
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'authenticated' => false,
                    'message' => 'Session expired'
                ], 401);
            }
            
            // Redirect to home without the error flag to prevent notifications
            return redirect('/');
        }

        // Get active slideshow images ordered by their order field
        $slideshowImages = SlideshowImage::active()->ordered()->get();

        // If no slideshow images exist, provide fallback images
        $fallbackImages = [
            (object)['image_url' => asset('images/hero/bg1.jpg'), 'title' => 'Welcome to AgriSys', 'description' => 'Agricultural Excellence'],
            (object)['image_url' => asset('images/hero/bg2.jpg'), 'title' => 'Community Support', 'description' => 'Supporting Local Farmers'],
            (object)['image_url' => asset('images/hero/bg3.jpg'), 'title' => 'Modern Agriculture', 'description' => 'Innovation in Farming'],
        ];

        // Use database images if available, otherwise use fallback
        $slides = $slideshowImages->count() > 0 ? $slideshowImages : collect($fallbackImages);
        
        // Clear any error messages
        session()->forget('error');
        session()->forget('errors');

        return view('landingPage.landing', compact('slides', 'user'));
    }
}