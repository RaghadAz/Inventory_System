<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movements = StockMovement::all();
        $products = Product::all();

        return view('stock.index', compact('products', 'movements'));
    }
    public function productHistory(Product $product)
    {
        $movements = $product->stockMovements()->latest()->get();
        return view('stock.history', compact('product', 'movements'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        return view('stock.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'amount' => 'required|integer|min:1',
            'change_type' => 'required|in:increase,decrease',
            'date' => 'required|date'
        ]);
        $product = Product::findOrFail($request->product_id);
        if ($request->change_type === 'increase') {
            $product->quantity += $request->amount;
        } else {
            if ($request->amount > $product->quantity) {
                return back()->with('error', 'Not enough stock');
            }
            $product->quantity -= $request->amount;
        }
        $product->save();
        StockMovement::create([
            'product_id' => $request->product_id,
            'amount' => $request->amount,
            'change_type' => $request->change_type,
            'date' => $request->date,
        ]);
        return redirect()->route('stock.index')->with('success', 'Stock movement recorded');
    }

    /**
     * Display the specified resource.
     */
    public function show(StockMovement $movement)
    {
        return view('stock.show', compact('movement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockMovement $movement)
    {
        $products = Product::all();
        return view('stock.edit', compact('movement', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockMovement $movement)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'amount' => 'required|integer|min:1',
            'change_type' => 'required|in:increase,decrease',
            'date' => 'required|date'
        ]);
        $oldProduct = Product::findOrFail($movement->product_id);
        if ($movement->change_type === 'increase') {
            $oldProduct->quantity -= $movement->amount;
        } else {
            $oldProduct->quantity += $movement->amount;
        }
        $oldProduct->save();
        $newProduct = Product::findOrFail($request->product_id);
        if ($request->change_type === 'increase') {
            $newProduct->quantity += $request->amount;
        } else {
            if ($request->amount > $newProduct->quantity) {
                return back()->with('error', 'Not enough stock');
            }
            $newProduct->quantity -= $request->amount;
        }
        $newProduct->save();
        $movement->update([
            'product_id' => $request->product_id,
            'amount' => $request->amount,
            'change_type' => $request->change_type,
            'date' => $request->date,
        ]);
        return redirect()->route('stock.index')->with('success', 'Stock movement updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockMovement $movement)
    {
        $product = Product::findOrFail($movement->product_id);
        if ($movement->change_type === 'increase') {
            $product->quantity -= $movement->amount;
        } else {
            $product->quantity += $movement->amount;
        }
        $product->save();
        $movement->delete();
        return redirect()->route('stock.index')->with('success', 'Stock movement deleted');
    }
}
