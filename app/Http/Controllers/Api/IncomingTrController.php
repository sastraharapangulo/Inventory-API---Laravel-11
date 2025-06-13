<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IncomingTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncomingTrController extends Controller
{
    public function index()
    {
        $transactions = IncomingTransaction::with('product')->get();
        return response()->json([
            'status' => true,
            'data' => $transactions
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'incoming_discount' => 'nullable|numeric',
            'quantity' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();

        try {
            $transaction = IncomingTransaction::create($validated);

            $product = Product::findOrFail($validated['product_id']);
            $product->increment('stock', $validated['quantity']);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Incoming transaction created',
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

    public function show(string $id)
    {
        $transaction = IncomingTransaction::with('product')->find($id);
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }
        return response()->json([
            'status' => true,
            'data' => $transaction
        ]);
    }

    public function update(Request $request, string $id)
    {
        $transaction = IncomingTransaction::find($id);
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $validated = $request->validate([
            'incoming_discount' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:1'
        ]);

        // Update stock (recalculate stock adjustment)
        $diffQty = $validated['quantity'] - $transaction->quantity;

        DB::beginTransaction();

        try {
            $transaction->update($validated);
            $transaction->product->increment('stock', $diffQty);

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

    public function destroy(string $id)
    {
        $transaction = IncomingTransaction::find($id);
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        DB::beginTransaction();

        try {
            $transaction->product->decrement('stock', $transaction->quantity);
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
