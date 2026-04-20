<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceNoteController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserController;

use App\Models\Product;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Auth::routes(['register' => false]);

Route::middleware('auth')->group(function () {

    // ✅ Dashboard with Low Stock
    Route::get('/dashboard', function () {

        $lowStocks = Product::with('stock')
            ->get()
            ->filter(fn($p) => ($p->stock?->quantity ?? 0) <= 2)
            ->sortBy(fn($p) => ($p->stock?->quantity ?? 0))
            ->take(10);

        return view('dashboard', compact('lowStocks'));
    })->name('dashboard');

    // Invoices available for admins and cashiers
    Route::middleware('role:admin,cashier')->group(function () {
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
        Route::get('/invoices/product-stock/{product}', [InvoiceController::class, 'productStock'])
            ->name('invoices.product-stock');
    });

    // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);

        Route::resource('users', UserController::class)->except(['show']);

        Route::resource('products', ProductController::class)->except(['show']);

        Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
        Route::post('/stock/add', [StockController::class, 'add'])->name('stock.add');

        Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
        Route::patch('/invoices/{invoice}', [InvoiceController::class, 'update']);
        Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
        Route::delete('/invoices/{invoice}/delete', [InvoiceController::class, 'delete'])
            ->name('invoices.delete');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/profit', [ReportController::class, 'profit'])->name('reports.profit');
        Route::get('/reports/product-sales', [ReportController::class, 'productSales'])->name('reports.product_sales');
        Route::get('/reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
        Route::get('/reports/low-stock', [ReportController::class, 'lowStock'])->name('reports.low_stock');
        Route::get('/reports/returns', [ReportController::class, 'returns'])->name('reports.returns');

        Route::resource('service-notes', ServiceNoteController::class);
        Route::get('service-notes/{service_note}/print', [ServiceNoteController::class, 'print'])->name('service-notes.print');

        Route::get('/returns', [ReturnController::class, 'index'])->name('returns.index');
        Route::get('/returns/create', [ReturnController::class, 'create'])->name('returns.create');
        Route::post('/returns', [ReturnController::class, 'store'])->name('returns.store');
        Route::get('/returns/{return}', [ReturnController::class, 'show'])->name('returns.show');
        Route::get('/returns/{return}/print', [ReturnController::class, 'print'])->name('returns.print');

        Route::resource('customers', CustomerController::class)->except(['show']);
    });
});
