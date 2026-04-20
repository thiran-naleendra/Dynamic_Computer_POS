<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;

// ✅ Add these models (create if not yet)
use App\Models\ReturnNote;
use App\Models\ReturnItem;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    // ✅ Sales report by invoice (date range)
    // Optional: subtract returns from sales total
    public function sales(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $invoices = Invoice::whereBetween('invoice_date', [$from, $to])
            ->latest('invoice_date')
            ->get();

        $totalSales = $invoices->sum('grand_total');

        // ✅ Returns total (if you have return_items.sell_price)
        $totalReturns = 0;
        try {
            $totalReturns = ReturnItem::whereHas('return', function ($q) use ($from, $to) {
                    $q->whereBetween('return_date', [$from, $to]);
                })
                ->selectRaw('SUM(qty * sell_price) as total')
                ->value('total') ?? 0;
        } catch (\Throwable $e) {
            $totalReturns = 0;
        }

        // ✅ Net sales = sales - returns
        $netSales = $totalSales - $totalReturns;

        return view('reports.sales', compact(
            'invoices',
            'from',
            'to',
            'totalSales',
            'totalReturns',
            'netSales'
        ));
    }

    // ✅ Profit report (sell - dealer) by invoice items
    // Optional: subtract return profit impact
    public function profit(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $items = InvoiceItem::with(['invoice', 'product'])
            ->whereHas('invoice', fn($q) => $q->whereBetween('invoice_date', [$from, $to]))
            ->latest()
            ->get();

        // Profit = (unit_price - dealer_price) * qty
        $totalProfit = $items->sum(function ($it) {
            $dealer = $it->product?->dealer_price ?? 0;
            return (($it->unit_price - $dealer) * $it->qty);
        });

        $totalSales = $items->sum('line_total');

        // ✅ Return totals + return profit impact
        $returnSales = 0;
        $returnProfitImpact = 0;

        try {
            $returnItems = ReturnItem::with('product')
                ->whereHas('return', function ($q) use ($from, $to) {
                    $q->whereBetween('return_date', [$from, $to]);
                })
                ->get();

            $returnSales = $returnItems->sum(function ($it) {
                $sell = (float)($it->sell_price ?? 0);
                return $sell * (int)$it->qty;
            });

            // Return profit impact = (sell - dealer) * qty   (this should be SUBTRACTED from profit)
            $returnProfitImpact = $returnItems->sum(function ($it) {
                $sell = (float)($it->sell_price ?? 0);
                $dealer = (float)($it->dealer_price ?? ($it->product?->dealer_price ?? 0));
                return ($sell - $dealer) * (int)$it->qty;
            });
        } catch (\Throwable $e) {
            $returnSales = 0;
            $returnProfitImpact = 0;
        }

        $netSales  = $totalSales - $returnSales;
        $netProfit = $totalProfit - $returnProfitImpact;

        return view('reports.profit', compact(
            'items',
            'from',
            'to',
            'totalProfit',
            'totalSales',
            'returnSales',
            'returnProfitImpact',
            'netSales',
            'netProfit'
        ));
    }

    // ✅ Product sales summary (grouped)
    public function productSales(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $rows = InvoiceItem::selectRaw('product_id, item_name, SUM(qty) as total_qty, SUM(line_total) as total_amount')
            ->whereHas('invoice', fn($q) => $q->whereBetween('invoice_date', [$from, $to]))
            ->groupBy('product_id', 'item_name')
            ->orderByDesc('total_qty')
            ->get();

        // Optional: you can subtract returns per product later if needed
        return view('reports.product_sales', compact('rows', 'from', 'to'));
    }

    // ✅ Stock report
    public function stock()
    {
        $products = Product::with('stock')->get();

        $totalQty = $products->sum(fn($p) => $p->stock?->quantity ?? 0);
        $stockValueSell = $products->sum(fn($p) => ($p->stock?->quantity ?? 0) * ((float)$p->sell_price ?? 0));
        $stockValueDealer = $products->sum(fn($p) => ($p->stock?->quantity ?? 0) * ((float)$p->dealer_price ?? 0));

        return view('reports.stock', compact('products', 'totalQty', 'stockValueSell', 'stockValueDealer'));
    }

    // ✅ Low stock
    public function lowStock()
    {
        $products = Product::with('stock')
            ->get()
            ->filter(fn($p) => ($p->stock?->quantity ?? 0) <= 5)
            ->sortBy(fn($p) => ($p->stock?->quantity ?? 0));

        return view('reports.low_stock', compact('products'));
    }

    // ✅ NEW: Return report
    public function returns(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $returns = ReturnNote::with('items')
            ->whereBetween('return_date', [$from, $to])
            ->latest('return_date')
            ->get();

        // if you store totals in return_notes, use them.
        // otherwise calculate from items sell_price
        $totalQty = $returns->sum('total_qty');

        $totalAmount = 0;
        try {
            $totalAmount = $returns->sum(function ($r) {
                // if grand_total exists in table
                if (isset($r->grand_total)) return (float)$r->grand_total;

                // else calculate from items
                return $r->items->sum(function ($it) {
                    return ((float)($it->sell_price ?? 0)) * (int)$it->qty;
                });
            });
        } catch (\Throwable $e) {
            $totalAmount = 0;
        }

        return view('reports.returns', compact('returns', 'from', 'to', 'totalQty', 'totalAmount'));
    }
}
