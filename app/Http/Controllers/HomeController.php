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

        // Get user session for compatibility with existing functionality
        $user = session('user', null);

        return view('landingPage.landing', compact('slides', 'user'));
    }

    /**
     * Display the user dashboard (same as landing page with user session)
     */
    public function dashboard()
    {
        $user = session('user', null);

        if (!$user) {
            // Let JavaScript handle the redirect - just return 401
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'authenticated' => false,
                    'session_expired' => true
                ], 401);
            }
            
            // For direct access, redirect with session_expired flag
            return redirect('/?session_expired=true');
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

        return view('landingPage.landing', compact('slides', 'user'));
    }
}
