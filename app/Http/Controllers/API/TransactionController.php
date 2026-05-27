<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Transactions
 *
 * APIs for managing purchase transactions.
 * All endpoints require authentication.
 */
class TransactionController extends Controller
{
    /**
     * List User Transactions
     *
     * Retrieve all transactions for the authenticated user with product and category details.
     *
     * @authenticated
     *
     * @queryParam status string Filter by transaction status (pending, processing, completed, cancelled). Example: pending
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "quantity": 2,
     *       "total_price": "3000000.00",
     *       "status": "pending",
     *       "payment_method": "transfer_bank",
     *       "shipping_address": "Jl. Sudirman No. 1",
     *       "product": {
     *         "id": 1,
     *         "name": "Midnight Oud",
     *         "price": "1500000.00",
     *         "category": {
     *           "id": 1,
     *           "name": "Eau de Parfum"
     *         }
     *       }
     *     }
     *   ]
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $query = Transaction::with('product.category')
            ->where('user_id', $request->user()->id);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $transactions = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }

    /**
     * Show Transaction Detail
     *
     * Retrieve a specific transaction with product and category details.
     *
     * @authenticated
     *
     * @urlParam transaction integer required The ID of the transaction. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "quantity": 2,
     *     "total_price": "3000000.00",
     *     "status": "pending",
     *     "payment_method": "transfer_bank",
     *     "shipping_address": "Jl. Sudirman No. 1",
     *     "notes": null,
     *     "product": {
     *       "id": 1,
     *       "name": "Midnight Oud",
     *       "category": {
     *         "id": 1,
     *         "name": "Eau de Parfum"
     *       }
     *     },
     *     "user": {
     *       "id": 1,
     *       "name": "Admin Parfum Store"
     *     }
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Transaction not found"
     * }
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $transaction = Transaction::with(['product.category', 'user'])
            ->where('user_id', $request->user()->id)
            ->find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $transaction,
        ]);
    }

    /**
     * Create Transaction
     *
     * Create a new purchase transaction. Automatically calculates total price
     * based on product price × quantity. Validates stock availability.
     *
     * @authenticated
     *
     * @bodyParam product_id integer required The ID of the product to purchase. Example: 1
     * @bodyParam quantity integer required The quantity to purchase. Example: 2
     * @bodyParam payment_method string required The payment method (transfer_bank, e_wallet, cod). Example: transfer_bank
     * @bodyParam shipping_address string required The shipping address. Example: Jl. Sudirman No. 1, Jakarta Pusat
     * @bodyParam notes string optional Additional notes for the order. Example: Please wrap as gift.
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Transaction created successfully",
     *   "data": {
     *     "id": 6,
     *     "product_id": 1,
     *     "quantity": 2,
     *     "total_price": "3000000.00",
     *     "status": "pending",
     *     "payment_method": "transfer_bank"
     *   }
     * }
     * @response 422 {
     *   "success": false,
     *   "message": "Insufficient stock. Available: 10"
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string|in:transfer_bank,e_wallet,cod',
            'shipping_address' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Check stock availability
        if ($product->stock < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock. Available: ' . $product->stock,
            ], 422);
        }

        // Calculate total price
        $validated['user_id'] = $request->user()->id;
        $validated['total_price'] = $product->price * $validated['quantity'];
        $validated['status'] = 'pending';

        $transaction = Transaction::create($validated);

        // Reduce product stock
        $product->decrement('stock', $validated['quantity']);

        $transaction->load('product.category');

        return response()->json([
            'success' => true,
            'message' => 'Transaction created successfully',
            'data' => $transaction,
        ], 201);
    }

    /**
     * Update Transaction Status
     *
     * Update the status of an existing transaction.
     *
     * @authenticated
     *
     * @urlParam transaction integer required The ID of the transaction. Example: 1
     * @bodyParam status string required The new status (pending, processing, completed, cancelled). Example: processing
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Transaction updated successfully",
     *   "data": {
     *     "id": 1,
     *     "status": "processing"
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Transaction not found"
     * }
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $transaction = Transaction::where('user_id', $request->user()->id)->find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
            ], 404);
        }

        $validated = $request->validate([
            'status' => 'required|string|in:pending,processing,completed,cancelled',
        ]);

        // If cancelling, restore product stock
        if ($validated['status'] === 'cancelled' && $transaction->status !== 'cancelled') {
            $product = Product::find($transaction->product_id);
            if ($product) {
                $product->increment('stock', $transaction->quantity);
            }
        }

        $transaction->update($validated);
        $transaction->load('product.category');

        return response()->json([
            'success' => true,
            'message' => 'Transaction updated successfully',
            'data' => $transaction,
        ]);
    }

    /**
     * Delete Transaction
     *
     * Remove a transaction record. Only pending transactions can be deleted.
     * Stock will be restored when deleted.
     *
     * @authenticated
     *
     * @urlParam transaction integer required The ID of the transaction. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Transaction deleted successfully"
     * }
     * @response 400 {
     *   "success": false,
     *   "message": "Only pending transactions can be deleted"
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Transaction not found"
     * }
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $transaction = Transaction::where('user_id', $request->user()->id)->find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
            ], 404);
        }

        // Only allow deletion of pending transactions
        if ($transaction->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending transactions can be deleted',
            ], 400);
        }

        // Restore stock
        $product = Product::find($transaction->product_id);
        if ($product) {
            $product->increment('stock', $transaction->quantity);
        }

        $transaction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaction deleted successfully',
        ]);
    }
}
