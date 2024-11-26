<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Requests\Admin\CategoryStoreRequest;
use App\Http\Requests\Admin\CategoryUpdateRequest;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = Category::withCount(['posts' => function($query) {
            $query->published();
        }])
        ->orderBy('name')
        ->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        $icons = $this->getAvailableIcons();
        return view('admin.categories.create', compact('icons'));
    }

    public function show(Category $category)
    {
        $posts = $category->posts()
            ->with(['author', 'categories'])
            ->published()
            ->latest('published_date')
            ->paginate(12);

        return view('categories.show', compact('category', 'posts'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(CategoryStoreRequest $request)
    {
        try {
            $validated = $request->validated();
            
            // Handle image upload if present
            if ($request->hasFile('image')) {
                $validated['image'] = $this->handleImageUpload($request->file('image'));
            }

            $category = Category::create($validated);

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Category created successfully');

        } catch (\Exception $e) {
            Log::error('Error creating category: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'An error occurred while creating the category');
        }
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        $icons = $this->getAvailableIcons();
        return view('admin.categories.edit', compact('category', 'icons'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(CategoryUpdateRequest $request, Category $category)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            
            $data['image'] = $this->handleImageUpload($request->file('image'));
        }

        $category->update($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category updated successfully');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        try {
            if ($category->posts()->exists()) {
                return back()->with('error', 'Cannot delete category that contains posts');
            }

            // Use the helper method to delete the image
            $imageDeleted = $category->deleteImage();
            
            if (!$imageDeleted && $category->image) {
                \Log::warning('Failed to delete image for category: ' . $category->id);
            }

            $category->delete();

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Category deleted successfully');

        } catch (\Exception $e) {
            \Log::error('Error deleting category: ' . $e->getMessage());
            
            return back()->with('error', 'An error occurred while deleting the category');
        }
    }

    /**
     * Toggle the featured status of the category.
     */
    public function toggleFeatured(Category $category)
    {
        $category->update([
            'is_featured' => !$category->is_featured
        ]);

        return back()->with('success', 'Category featured status updated successfully');
    }

    /**
     * Handle the image upload process.
     */
    protected function handleImageUpload($file)
    {
        try {
            $filename = uniqid('category_') . '.webp';
            $path = 'categories/' . $filename;

            $manager = new ImageManager(new Driver());
            $image = $manager->read($file);
            
            $saved = Storage::disk('public')->put($path, $image->toWebp(80)->toString());

            return $saved ? $path : null;

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get available icons for categories.
     */
    protected function getAvailableIcons(): array
    {
        return [
            'book' => 'Book',
            'camera' => 'Camera',
            'code' => 'Code',
            'coffee' => 'Coffee',
            'globe' => 'Globe',
            'heart' => 'Heart',
            'lab' => 'Lab',
            'music' => 'Music',
            'pen' => 'Pen',
            'photo' => 'Photo',
            'rocket' => 'Rocket',
            'star' => 'Star',
        ];
    }

    /**
     * Reorder categories.
     */
    public function reorder(Request $request)
    {
        $positions = $request->validate([
            'positions' => 'required|array',
            'positions.*' => 'integer|exists:categories,id'
        ]);

        foreach ($positions['positions'] as $order => $id) {
            Category::where('id', $id)->update(['order' => $order]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Mass action on categories.
     */
    public function massAction(Request $request)
    {
        $validated = $request->validate([
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'action' => 'required|in:delete,feature,unfeature'
        ]);

        $categories = Category::whereIn('id', $validated['categories']);

        switch ($validated['action']) {
            case 'delete':
                // Only delete categories without posts
                $deletable = $categories->whereDoesntHave('posts')->get();
                foreach ($deletable as $category) {
                    if ($category->image) {
                        Storage::delete($category->image);
                    }
                    $category->delete();
                }
                $message = count($deletable) . ' categories deleted successfully';
                break;

            case 'feature':
                $categories->update(['is_featured' => true]);
                $message = 'Selected categories are now featured';
                break;

            case 'unfeature':
                $categories->update(['is_featured' => false]);
                $message = 'Selected categories are no longer featured';
                break;
        }

        return back()->with('success', $message);
    }

    public function uploadImages(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('uploads', 'public');
            return response()->json([
                'location' => asset('storage/' . $path)
            ]);
        }
        return response()->json(['error' => 'No file uploaded'], 400);
    }
}