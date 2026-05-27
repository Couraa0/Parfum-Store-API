<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * @group Categories
 *
 * APIs for managing perfume categories.
 */
class CategoryController extends Controller
{
    /**
     * List All Categories
     *
     * Retrieve a list of all perfume categories with product count.
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Eau de Parfum",
     *       "slug": "eau-de-parfum",
     *       "description": "Long-lasting fragrance...",
     *       "products_count": 8,
     *       "created_at": "2026-05-27T12:00:00.000000Z"
     *     }
     *   ]
     * }
     */
    public function index(): JsonResponse
    {
        $categories = Category::withCount('products')->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Show Category Detail
     *
     * Retrieve a specific category with its products (eager loaded).
     *
     * @urlParam category integer required The ID of the category. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "Eau de Parfum",
     *     "slug": "eau-de-parfum",
     *     "description": "Long-lasting fragrance...",
     *     "products": [
     *       {
     *         "id": 1,
     *         "name": "Midnight Oud",
     *         "price": "1500000.00"
     *       }
     *     ]
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Category not found"
     * }
     */
    public function show(string $id): JsonResponse
    {
        $category = Category::with('products')->find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category,
        ]);
    }

    /**
     * Create Category
     *
     * Store a new perfume category.
     *
     * @authenticated
     *
     * @bodyParam name string required The name of the category. Example: Eau de Parfum
     * @bodyParam description string optional A brief description of the category. Example: Concentrated fragrance with 15-20% perfume oil.
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Category created successfully",
     *   "data": {
     *     "id": 6,
     *     "name": "Eau de Parfum",
     *     "slug": "eau-de-parfum"
     *   }
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => $category,
        ], 201);
    }

    /**
     * Update Category
     *
     * Update an existing perfume category.
     *
     * @authenticated
     *
     * @urlParam category integer required The ID of the category. Example: 1
     * @bodyParam name string optional The name of the category. Example: Eau de Cologne
     * @bodyParam description string optional A brief description. Example: Light fragrance with 2-4% perfume oil.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Category updated successfully",
     *   "data": {
     *     "id": 1,
     *     "name": "Eau de Cologne",
     *     "slug": "eau-de-cologne"
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Category not found"
     * }
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => $category,
        ]);
    }

    /**
     * Delete Category
     *
     * Remove a perfume category from the database.
     *
     * @authenticated
     *
     * @urlParam category integer required The ID of the category. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Category deleted successfully"
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Category not found"
     * }
     */
    public function destroy(string $id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
        ]);
    }
}
