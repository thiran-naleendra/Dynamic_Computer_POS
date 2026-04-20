<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use App\Models\ReturnNote;
use App\Models\ReturnItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function index()
    {
        $returns = ReturnNote::latest()->paginate(15);
        return view('returns.index', compact('returns'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        return view('returns.create', compact('products'));
    }

    private function generateReturnNo(): string
    {
        $today = date('Ymd');
        $countToday = ReturnNote::where('return_no', 'like', "RTN-$today-%")->count() + 1;

        return "RTN-$today-" . str_pad((string) $countToday, 4, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'return_date' => 'required|date',
            'customer_name' => 'nullable|string|max:255',
            'customer_tel' => 'nullable|string|max:50',
            'reason' => 'nullable|string',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.serial_no' => 'nullable|string|max:255',

            // ✅ prices for reports
            'items.*.sell_price' => 'required|numeric|min:0',
            'items.*.dealer_price' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($data) {

            $returnNo = $this->generateReturnNo();

            // Load all products once (for snapshot name)
            $productIds = collect($data['items'])->pluck('product_id')->unique()->values();
            $productsById = Product::whereIn('id', $productIds)->get()->keyBy('id');

            $return = ReturnNote::create([
                'return_no' => $returnNo,
                'return_date' => $data['return_date'],
                'customer_name' => $data['customer_name'] ?? null,
                'customer_tel' => $data['customer_tel'] ?? null,
                'reason' => $data['reason'] ?? null,

                'total_qty' => 0,
                // ✅ totals for report (if you have these columns)
                'sub_total' => 0,
                'grand_total' => 0,

                'created_by' => Auth::id(),
            ]);

            $totalQty = 0;
            $subTotal = 0;

            foreach ($data['items'] as $it) {
                $product = $productsById->get($it['product_id']);
                $qty = (int) $it['qty'];

                $sellPrice = (float) $it['sell_price'];
                $dealerPrice = (float) $it['dealer_price'];
                $lineTotal = $qty * $sellPrice;

                $totalQty += $qty;
                $subTotal += $lineTotal;

                ReturnItem::create([
                    'return_id' => $return->id,
                    'product_id' => $it['product_id'],
                    'item_name' => $product?->name ?? 'Product',
                    'serial_no' => $it['serial_no'] ?? null,
                    'qty' => $qty,

                    // ✅ save prices
                    'sell_price' => $sellPrice,
                    'dealer_price' => $dealerPrice,
                    'line_total' => $lineTotal,
                ]);

                // ✅ Add back stock safely
                $stock = Stock::where('product_id', $it['product_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$stock) {
                    $stock = Stock::create([
                        'product_id' => $it['product_id'],
                        'quantity' => 0,
                    ]);
                }

                $stock->quantity += $qty;
                $stock->save();
            }

            $return->total_qty = $totalQty;
            $return->sub_total = $subTotal;
            $return->grand_total = $subTotal; // change if you add discount etc.
            $return->save();

            return redirect()
                ->route('returns.show', $return)
                ->with('success', 'Return Note saved & stock updated');
        });
    }

    public function show(ReturnNote $return)
    {
        $return->load('items');
        return view('returns.show', compact('return'));
    }

    public function print(ReturnNote $return)
    {
        $return->load('items');
        return view('returns.print', compact('return'));
    }
}
