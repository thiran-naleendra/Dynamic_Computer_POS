<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('items:id,invoice_id,serial_no')
            ->latest()
            ->get();

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();

        return view('invoices.create', compact('products', 'customers'));
    }

    private function generateInvoiceNo(): string
    {
        $today = date('Ymd');
        $countToday = Invoice::where('invoice_no', 'like', "INV-$today-%")->count() + 1;
        return "INV-$today-" . str_pad((string) $countToday, 4, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'nullable|string|max:255',
            'invoice_date' => 'required|date',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.serial_no' => 'nullable|string|max:255',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.warranty_days' => 'nullable|integer|min:0',
        ]);

        return DB::transaction(function () use ($data) {
            foreach ($data['items'] as $it) {
                $stock = Stock::where('product_id', $it['product_id'])->lockForUpdate()->first();
                $available = $stock ? (int) $stock->quantity : 0;
                $need = (int) $it['qty'];

                if ($available < $need) {
                    $p = Product::find($it['product_id']);
                    $name = $p?->name ?? 'Product';

                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', "Not enough stock for: {$name} (Available: {$available}, Need: {$need})");
                }
            }

            $customer = null;
            $customerName = $data['customer_name'] ?? null;

            if (!empty($data['customer_id'])) {
                $customer = Customer::find($data['customer_id']);
                $customerName = $customer?->name;
            }

            $invoiceNo = $this->generateInvoiceNo();

            $invoice = Invoice::create([
                'invoice_no' => $invoiceNo,
                'shop_name' => 'Dynamic computer system',
                'customer_id' => $customer?->id,
                'customer_name' => $customerName,
                'invoice_date' => $data['invoice_date'],
                'sub_total' => 0,
                'grand_total' => 0,
                'user_id' => Auth::id(),
            ]);

            $subTotal = 0;
            $invoiceDate = Carbon::parse($invoice->invoice_date);

            foreach ($data['items'] as $it) {
                $product = Product::findOrFail($it['product_id']);

                $qty = (int) $it['qty'];
                $unitPrice = (float) $it['unit_price'];
                $lineTotal = $qty * $unitPrice;
                $subTotal += $lineTotal;

                $warrantyDays = isset($it['warranty_days']) && $it['warranty_days'] !== null
                    ? (int) $it['warranty_days']
                    : (int) ($product->warranty_days ?? 0);

                $warrantyEndDate = null;
                if ($warrantyDays > 0) {
                    $warrantyEndDate = $invoiceDate->copy()->addDays($warrantyDays)->toDateString();
                }

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $product->id,
                    'item_name' => $product->name,
                    'serial_no' => $it['serial_no'] ?? null,
                    'warranty_days' => $warrantyDays,
                    'warranty_end_date' => $warrantyEndDate,
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ]);

                $stock = Stock::where('product_id', $product->id)->lockForUpdate()->first();
                $stock->quantity -= $qty;
                $stock->save();
            }

            $invoice->sub_total = $subTotal;
            $invoice->grand_total = $subTotal;
            $invoice->save();

            return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice saved');
        });
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('items');
        return view('invoices.show', compact('invoice'));
    }

    public function print(Invoice $invoice)
    {
        $invoice->load('items');
        return view('invoices.print', compact('invoice'));
    }

    public function delete(Invoice $invoice)
    {
        DB::transaction(function () use ($invoice) {
            $invoice->load('items');

            foreach ($invoice->items as $item) {
                $stock = Stock::where('product_id', $item->product_id)
                    ->lockForUpdate()
                    ->first();

                if ($stock) {
                    $stock->quantity += (int) $item->qty;
                    $stock->save();
                } else {
                    Stock::create([
                        'product_id' => $item->product_id,
                        'quantity' => (int) $item->qty,
                    ]);
                }
            }

            InvoiceItem::where('invoice_id', $invoice->id)->delete();
            $invoice->delete();
        });

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully!');
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items');
        $products = Product::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();

        return view('invoices.edit', compact('invoice', 'products', 'customers'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'nullable|string|max:255',
            'invoice_date' => 'required|date',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.serial_no' => 'nullable|string|max:255',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.warranty_days' => 'nullable|integer|min:0',
        ]);

        try {
            DB::transaction(function () use ($data, $invoice) {
                $invoice->load('items');

                foreach ($invoice->items as $oldItem) {
                    $stock = Stock::where('product_id', $oldItem->product_id)
                        ->lockForUpdate()
                        ->first();

                    if ($stock) {
                        $stock->quantity += (int) $oldItem->qty;
                        $stock->save();
                    } else {
                        Stock::create([
                            'product_id' => $oldItem->product_id,
                            'quantity' => (int) $oldItem->qty,
                        ]);
                    }
                }

                foreach ($data['items'] as $it) {
                    $stock = Stock::where('product_id', $it['product_id'])
                        ->lockForUpdate()
                        ->first();

                    $available = $stock ? (int) $stock->quantity : 0;
                    $need = (int) $it['qty'];

                    $product = Product::find($it['product_id']);
                    $name = $product?->name ?? 'Product';

                    if ($available <= 0) {
                        throw new \Exception("Stock is empty for: {$name}");
                    }

                    if ($available < $need) {
                        throw new \Exception("Not enough stock for: {$name} (Available: {$available}, Need: {$need})");
                    }
                }

                $customer = null;
                $customerName = $data['customer_name'] ?? null;

                if (!empty($data['customer_id'])) {
                    $customer = Customer::find($data['customer_id']);
                    $customerName = $customer?->name;
                }

                $invoice->items()->delete();

                $invoice->update([
                    'customer_id' => $customer?->id,
                    'customer_name' => $customerName,
                    'invoice_date' => $data['invoice_date'],
                ]);

                $subTotal = 0;
                $invoiceDate = Carbon::parse($invoice->invoice_date);

                foreach ($data['items'] as $it) {
                    $product = Product::findOrFail($it['product_id']);

                    $qty = (int) $it['qty'];
                    $unitPrice = (float) $it['unit_price'];
                    $lineTotal = $qty * $unitPrice;
                    $subTotal += $lineTotal;

                    $warrantyDays = isset($it['warranty_days']) && $it['warranty_days'] !== null
                        ? (int) $it['warranty_days']
                        : (int) ($product->warranty_days ?? 0);

                    $warrantyEndDate = null;
                    if ($warrantyDays > 0) {
                        $warrantyEndDate = $invoiceDate->copy()->addDays($warrantyDays)->toDateString();
                    }

                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'product_id' => $product->id,
                        'item_name' => $product->name,
                        'serial_no' => $it['serial_no'] ?? null,
                        'warranty_days' => $warrantyDays,
                        'warranty_end_date' => $warrantyEndDate,
                        'qty' => $qty,
                        'unit_price' => $unitPrice,
                        'line_total' => $lineTotal,
                    ]);

                    $stock = Stock::where('product_id', $product->id)
                        ->lockForUpdate()
                        ->first();

                    if (!$stock) {
                        $stock = Stock::create([
                            'product_id' => $product->id,
                            'quantity' => 0,
                        ]);
                    }

                    $stock->quantity -= $qty;
                    $stock->save();
                }

                $invoice->update([
                    'sub_total' => $subTotal,
                    'grand_total' => $subTotal,
                ]);
            });

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function productStock(Product $product)
    {
        $stock = Stock::where('product_id', $product->id)->first();

        return response()->json([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'available_stock' => $stock ? (int) $stock->quantity : 0,
            'sell_price' => (float) $product->sell_price,
            'warranty_days' => (int) ($product->warranty_days ?? 0),
        ]);
    }
}