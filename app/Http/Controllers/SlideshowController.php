<?php

namespace App\Http\Controllers;

use App\Models\SlideshowImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SlideshowController extends Controller
{
    /**
     * Display a listing of slideshow images for admin management
     */
    public function index()
    {
        // $slides = SlideshowImage::ordered()->get();
        $slides = SlideshowImage::ordered()->paginate(10);
        return view('admin.slideshow.index', compact('slides'));
    }

    /**
     * Store a newly created slideshow image
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|in:0,1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Handle image upload
            $imagePath = $request->file('image')->store('slideshow', 'public');

            // Get the next order if not specified
            $order = $request->input('order', SlideshowImage::max('order') + 1);

            // Create the slideshow image record
            $slideshow = SlideshowImage::create([
                'image_path' => $imagePath,
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'order' => $order,
                'is_active' => (bool) $request->input('is_active', 0)
            ]);

            // Log activity
            $this->logActivity('created', 'SlideshowImage', $slideshow->id, [
                'title' => $slideshow->title,
                'order' => $slideshow->order,
                'is_active' => $slideshow->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Slideshow image added successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading slideshow image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified slideshow image
     */
    public function update(Request $request, $id)
    {
        $slideshow_image = SlideshowImage::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|in:0,1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = [
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'order' => $request->input('order', $slideshow_image->order),
                'is_active' => (bool) $request->input('is_active', 0)
            ];

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($slideshow_image->image_path && Storage::disk('public')->exists($slideshow_image->image_path)) {
                    Storage::disk('public')->delete($slideshow_image->image_path);
                }

                // Store new image
                $updateData['image_path'] = $request->file('image')->store('slideshow', 'public');
            }

            $slideshow_image->update($updateData);

            // Log activity
            $this->logActivity('updated', 'SlideshowImage', $slideshow_image->id, [
                'title' => $slideshow_image->title,
                'order' => $slideshow_image->order,
                'is_active' => $slideshow_image->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Slideshow image updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating slideshow image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified slideshow image
     */
    public function destroy($id)
    {
        $slideshow_image = SlideshowImage::findOrFail($id);
        try {
            // Delete the image file from storage
            if ($slideshow_image->image_path && Storage::disk('public')->exists($slideshow_image->image_path)) {
                Storage::disk('public')->delete($slideshow_image->image_path);
            }

            $title = $slideshow_image->title;
            $slideshow_image->delete();

            // Log activity
            $this->logActivity('deleted', 'SlideshowImage', $id, [
                'title' => $title
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Slideshow image deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting slideshow image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the order of slideshow images
     */
    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slides' => 'required|array',
            'slides.*.id' => 'required|exists:slideshow_images,id',
            'slides.*.order' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Invalid data provided']);
        }

        try {
            foreach ($request->input('slides') as $slideData) {
                SlideshowImage::where('id', $slideData['id'])
                    ->update(['order' => $slideData['order']]);
            }

            return response()->json(['success' => true, 'message' => 'Order updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating order: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle the active status of a slideshow image
     */
    public function toggleStatus($id)
    {
        $slideshow_image = SlideshowImage::findOrFail($id);
        try {
            $slideshow_image->update(['is_active' => !$slideshow_image->is_active]);
            $status = $slideshow_image->is_active ? 'activated' : 'deactivated';

            // Log activity
            $this->logActivity('updated', 'SlideshowImage', $slideshow_image->id, [
                'title' => $slideshow_image->title,
                'is_active' => $slideshow_image->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => "Slideshow image {$status} successfully",
                'is_active' => $slideshow_image->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating status: ' . $e->getMessage()]);
        }
    }

    /**
     * Get active slideshow images for the landing page (API endpoint)
     */
    public function getActiveSlides()
    {
        try {
            $slides = SlideshowImage::active()->ordered()->get(['id', 'image_path', 'title', 'description']);

            $slides = $slides->map(function ($slide) {
                return [
                    'id' => $slide->id,
                    'image_url' => $slide->image_url,
                    'title' => $slide->title,
                    'description' => $slide->description
                ];
            });

            return response()->json(['success' => true, 'slides' => $slides]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching slides: ' . $e->getMessage()]);
        }
    }
}