<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @group Products
 *
 * APIs for managing perfume products.
 * Supports CRUD operations with image upload, pagination, search, and category filtering.
 * All product responses include nested category data via Eager Loading.
 */
class ProductController extends Controller
{
    /**
     * List Products
     *
     * Retrieve a paginated list of perfume products with their categories.
     * Supports search by name/description and filtering by category.
     *
     * @queryParam search string Search products by name or description. Example: Midnight
     * @queryParam category_id integer Filter products by category ID. Example: 1
     * @queryParam per_page integer Number of items per page (default: 10). Example: 10
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 1,
     *         "name": "Midnight Oud",
     *         "slug": "midnight-oud",
     *         "description": "A luxurious fragrance...",
     *         "price": "1500000.00",
     *         "stock": 25,
     *         "image": "products/midnight-oud.jpg",
     *         "category": {
     *           "id": 1,
     *           "name": "Eau de Parfum",
     *           "slug": "eau-de-parfum"
     *         }
     *       }
     *     ],
     *     "per_page": 10,
     *     "total": 30,
     *     "last_page": 3
     *   }
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('category');

        // Search by name or description
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        $perPage = $request->get('per_page', 10);
        $products = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Show Product Detail
     *
     * Retrieve a specific product with its category details (eager loaded).
     *
     * @urlParam product integer required The ID of the product. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "Midnight Oud",
     *     "slug": "midnight-oud",
     *     "description": "A luxurious fragrance...",
     *     "price": "1500000.00",
     *     "stock": 25,
     *     "image": "products/midnight-oud.jpg",
     *     "image_url": "http://localhost:8000/storage/products/midnight-oud.jpg",
     *     "category": {
     *       "id": 1,
     *       "name": "Eau de Parfum",
     *       "slug": "eau-de-parfum",
     *       "description": "Concentrated fragrance..."
     *     }
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Product not found"
     * }
     */
    public function show(string $id): JsonResponse
    {
        $product = Product::with('category')->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $data = $product->toArray();
        $data['image_url'] = $product->image
            ? asset('storage/' . $product->image)
            : null;

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Create Product
     *
     * Store a new perfume product with optional image upload.
     *
     * @authenticated
     *
     * @bodyParam category_id integer required The ID of the category. Example: 1
     * @bodyParam name string required The name of the product. Example: Midnight Oud
     * @bodyParam description string required The product description. Example: A luxurious Arabian oud fragrance with notes of sandalwood and amber.
     * @bodyParam price number required The price in IDR. Example: 1500000
     * @bodyParam stock integer required The stock quantity. Example: 25
     * @bodyParam image file optional Product image (jpeg, png, jpg, gif). Max 2MB.
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Product created successfully",
     *   "data": {
     *     "id": 31,
     *     "name": "Midnight Oud",
     *     "slug": "midnight-oud",
     *     "price": "1500000.00",
     *     "category": {
     *       "id": 1,
     *       "name": "Eau de Parfum"
     *     }
     *   }
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $validated['slug'] . '-' . time() . '.' . $image->getClientOriginalExtension();
            $validated['image'] = $image->storeAs('products', $imageName, 'public');
        }

        $product = Product::create($validated);
        $product->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }

    /**
     * Update Product
     *
     * Update an existing perfume product. Supports image replacement.
     * Use POST method with `_method=PUT` for file uploads.
     *
     * @authenticated
     *
     * @urlParam product integer required The ID of the product. Example: 1
     * @bodyParam category_id integer optional The ID of the category. Example: 2
     * @bodyParam name string optional The name of the product. Example: Rose Imperiale
     * @bodyParam description string optional The product description. Example: An elegant floral fragrance.
     * @bodyParam price number optional The price in IDR. Example: 1800000
     * @bodyParam stock integer optional The stock quantity. Example: 30
     * @bodyParam image file optional New product image (jpeg, png, jpg, gif). Max 2MB. Old image will be deleted.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Product updated successfully",
     *   "data": {
     *     "id": 1,
     *     "name": "Rose Imperiale",
     *     "slug": "rose-imperiale",
     *     "price": "1800000.00"
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Product not found"
     * }
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $validated = $request->validate([
            'category_id' => 'sometimes|required|exists:categories,id',
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle image upload (delete old image first)
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $image = $request->file('image');
            $slug = $validated['slug'] ?? $product->slug;
            $imageName = $slug . '-' . time() . '.' . $image->getClientOriginalExtension();
            $validated['image'] = $image->storeAs('products', $imageName, 'public');
        }

        $product->update($validated);
        $product->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product,
        ]);
    }

    /**
     * Delete Product
     *
     * Remove a perfume product and its associated image from storage.
     *
     * @authenticated
     *
     * @urlParam product integer required The ID of the product. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Product deleted successfully"
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Product not found"
     * }
     */
    public function destroy(string $id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        // Delete image from storage
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }
}
