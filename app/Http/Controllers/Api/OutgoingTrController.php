<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OutgoingTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OutgoingTrController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = OutgoingTransaction::all();

        return response()->json([
            'status' => true,
            'message' => 'Get Products Success',
            'data' => $products
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'outgoing_discount' => 'nullable|numeric',
            'quantity' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($validated['product_id']);

            // Cek apakah stok cukup
            if ($product->stock < $validated['quantity']) {
                return response()->json([
                    'status' => false,
                    'message' => 'Insufficient stock.'
                ], 400);
            }

            // Simpan transaksi keluar
            $outgoingTransaction = OutgoingTransaction::create($validated);

            // Kurangi stok
            $product->decrement('stock', $validated['quantity']);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Store Outgoing Product Success',
                'data' => $outgoingTransaction
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaction = OutgoingTransaction::with('product')->find($id);

        if (!$transaction) {
            return response()->json([
                'status' => false,
                'message' => 'Transaction not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $transaction
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $transaction = OutgoingTransaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $validated = $request->validate([
            'outgoing_discount' => 'nullable|numeric',
            'quantity' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();

        try {
            $product = $transaction->product;
            $diffQty = $validated['quantity'] - $transaction->quantity;

            if ($diffQty > 0 && $product->stock < $diffQty) {
                return response()->json([
                    'status' => false,
                    'message' => 'Insufficient stock for update.'
                ], 400);
            }

            // Update stock
            $product->decrement('stock', $diffQty);

            $transaction->update($validated);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Transaction updated',
                'data' => $transaction
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaction = OutgoingTransaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        DB::beginTransaction();

        try {
            $transaction->product->increment('stock', $transaction->quantity);
            $transaction->delete();

            DB::commit();

            return response()->json(['message' => 'Transaction deleted']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
