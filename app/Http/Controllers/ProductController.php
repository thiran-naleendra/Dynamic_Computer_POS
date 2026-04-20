<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('stock')->latest()->get();
        return view('products.index', compact('products'));
    }

    private function warrantyOptions(): array
    {
        return [
            0    => 'No Warranty',
            7    => '1 Week',
            14   => '2 Weeks',
            30   => '1 Month',
            90   => '3 Months',
            180  => '6 Months',
            365  => '1 Year',
            730  => '2 Years',
            1095 => '3 Years',
            1825 => '5 Years',
        ];
    }

    public function create()
    {
        $warrantyOptions = $this->warrantyOptions();
        return view('products.create', compact('warrantyOptions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name'),
            ],
            'dealer_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'warranty_days' => 'required|integer|min:0',
        ], [
            'name.unique' => 'This product name already exists.',
        ]);

        // normalize name
        $data['name'] = trim($data['name']);

        $product = Product::create($data);

        Stock::firstOrCreate(['product_id' => $product->id], ['quantity' => 0]);

        return redirect()->route('products.index')->with('success', 'Product added');
    }

    public function edit(Product $product)
    {
        $warrantyOptions = $this->warrantyOptions();
        return view('products.edit', compact('product', 'warrantyOptions'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name')->ignore($product->id),
            ],
            'dealer_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'warranty_days' => 'required|integer|min:0',
        ], [
            'name.unique' => 'This product name already exists.',
        ]);

        $data['name'] = trim($data['name']);

        $product->update($data);

        Stock::firstOrCreate(['product_id' => $product->id], ['quantity' => 0]);

        return redirect()->route('products.index')->with('success', 'Product updated');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted');
    }
}
