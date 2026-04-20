<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $products = Product::with('stock')->orderBy('name')->get();
        return view('stock.index', compact('products'));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'action'     => 'required|in:add,reduce,set',
            'qty'        => 'required|integer|min:0',
        ]);

        $stock = Stock::firstOrCreate(
            ['product_id' => $data['product_id']],
            ['quantity' => 0]
        );

        $qty = (int) $data['qty'];

        if ($data['action'] === 'add') {
            if ($qty < 1) {
                return back()->withErrors(['qty' => 'Add quantity must be at least 1'])->withInput();
            }
            $stock->quantity += $qty;
        }

        if ($data['action'] === 'reduce') {
            if ($qty < 1) {
                return back()->withErrors(['qty' => 'Reduce quantity must be at least 1'])->withInput();
            }
            $stock->quantity -= $qty;

            // prevent negative stock
            if ($stock->quantity < 0) {
                $stock->quantity = 0;
            }
        }

        if ($data['action'] === 'set') {
            // can be 0
            if ($qty < 0) $qty = 0;
            $stock->quantity = $qty;
        }

        $stock->save();

        return redirect()->route('stock.index')->with('success', 'Stock updated');
    }
}