@extends('layouts.app')

@section('content')
@php
    $totalProducts = $products->count();
    $lowStockProducts = $products->filter(fn ($product) => (($product->stock?->quantity ?? 0) > 0) && (($product->stock?->quantity ?? 0) <= 5))->count();
    $outOfStockProducts = $products->filter(fn ($product) => (($product->stock?->quantity ?? 0) <= 0))->count();
    $avgSellPrice = $totalProducts > 0 ? $products->avg('sell_price') : 0;
@endphp

<style>
    .products-wrap {
        display: grid;
        gap: 1.5rem;
    }

    .products-hero {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem 1.35rem;
        border: 1px solid rgba(24, 50, 77, 0.08);
        border-radius: 20px;
        background:
            radial-gradient(circle at top right, rgba(13, 110, 253, 0.12), transparent 24%),
            linear-gradient(135deg, rgba(255, 255, 255, 0.96), rgba(248, 251, 255, 0.95));
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.06);
    }

    .products-hero h1 {
        margin: 0 0 .3rem;
        font-size: clamp(1.75rem, 2.8vw, 2.3rem);
        font-weight: 800;
        color: var(--text-main, #18324d);
    }

    .products-hero p {
        margin: 0;
        color: var(--text-muted, #6c8098);
        max-width: 620px;
    }

    .hero-actions {
        display: flex;
        gap: .75rem;
        flex-wrap: wrap;
    }

    .hero-actions .btn {
        min-width: 150px;
        border-radius: 14px;
        font-weight: 700;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
    }

    .summary-card {
        border: 1px solid rgba(24, 50, 77, 0.08);
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.92);
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
    }

    .summary-card .card-body {
        padding: 1.05rem 1.15rem;
    }

    .summary-label {
        display: block;
        margin-bottom: .5rem;
        font-size: .8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--text-muted, #6c8098);
    }

    .summary-value {
        display: block;
        font-size: 1.9rem;
        line-height: 1;
        font-weight: 800;
        color: var(--text-main, #18324d);
        margin-bottom: .35rem;
    }

    .summary-note {
        margin: 0;
        color: var(--text-muted, #6c8098);
        font-size: .92rem;
    }

    .summary-card.primary {
        background: linear-gradient(180deg, rgba(13, 110, 253, 0.08), rgba(255, 255, 255, 0.95));
    }

    .summary-card.success {
        background: linear-gradient(180deg, rgba(25, 135, 84, 0.08), rgba(255, 255, 255, 0.95));
    }

    .summary-card.warning {
        background: linear-gradient(180deg, rgba(255, 193, 7, 0.14), rgba(255, 255, 255, 0.95));
    }

    .summary-card.info {
        background: linear-gradient(180deg, rgba(13, 202, 240, 0.12), rgba(255, 255, 255, 0.95));
    }

    .products-panel {
        border: 1px solid rgba(24, 50, 77, 0.08);
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.92);
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .products-panel .card-body {
        padding: 1.15rem;
    }

    .panel-head {
        display: flex;
        justify-content: space-between;
        align-items: end;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .panel-head h2 {
        margin: 0 0 .15rem;
        font-size: 1.3rem;
        font-weight: 800;
    }

    .panel-head p {
        margin: 0;
        color: var(--text-muted, #6c8098);
    }

    .table-shell {
        border: 1px solid rgba(24, 50, 77, 0.08);
        border-radius: 16px;
        overflow: hidden;
    }

    .products-table thead th {
        background: rgba(36, 87, 167, 0.06);
        border-bottom: 0;
        color: var(--text-muted, #6c8098);
        font-size: .8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .price-text {
        font-weight: 700;
        color: var(--text-main, #18324d);
    }

    .stock-badge,
    .warranty-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 88px;
        padding: .38rem .7rem;
        border-radius: 999px;
        font-size: .82rem;
        font-weight: 700;
    }

    .stock-badge.out {
        background: rgba(220, 53, 69, 0.14);
        color: #b02a37;
    }

    .stock-badge.low {
        background: rgba(255, 193, 7, 0.18);
        color: #8c6a00;
    }

    .stock-badge.ok {
        background: rgba(13, 110, 253, 0.14);
        color: #0b5ed7;
    }

    .warranty-badge.none {
        background: rgba(108, 117, 125, 0.14);
        color: #5c636a;
    }

    .warranty-badge.yes {
        background: rgba(13, 202, 240, 0.16);
        color: #087990;
    }

    .action-group {
        display: inline-flex;
        gap: .45rem;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .action-group .btn {
        border-radius: 12px;
        font-weight: 700;
    }

    html[data-theme="dark"] .products-hero,
    html[data-theme="dark"] .summary-card,
    html[data-theme="dark"] .products-panel,
    html[data-theme="dark"] .table-shell {
        background: var(--surface-strong, #162338) !important;
        border-color: var(--border-soft, rgba(255, 255, 255, 0.08)) !important;
    }

    html[data-theme="dark"] .products-table thead th {
        border-color: var(--border-soft, rgba(255, 255, 255, 0.08)) !important;
    }

    @media (max-width: 1199.98px) {
        .summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .products-hero,
        .panel-head {
            flex-direction: column;
            align-items: flex-start;
        }

        .summary-grid {
            grid-template-columns: 1fr;
        }

        .hero-actions {
            width: 100%;
        }

        .hero-actions .btn {
            width: 100%;
        }
    }
</style>

<div class="products-wrap">
    <div class="products-hero">
        <div>
            <h1>Products</h1>
            <p>Manage your product list, pricing, warranty settings, and keep a quick eye on items that are running low.</p>
        </div>

        <div class="hero-actions">
            <a href="{{ route('products.create') }}" class="btn btn-primary">Add Product</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-0">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="summary-grid">
        <div class="card summary-card primary">
            <div class="card-body">
                <span class="summary-label">Total Products</span>
                <span class="summary-value">{{ $totalProducts }}</span>
                <p class="summary-note">Items available in your catalog.</p>
            </div>
        </div>

        <div class="card summary-card success">
            <div class="card-body">
                <span class="summary-label">Low Stock</span>
                <span class="summary-value">{{ $lowStockProducts }}</span>
                <p class="summary-note">Products with only a few items left.</p>
            </div>
        </div>

        <div class="card summary-card warning">
            <div class="card-body">
                <span class="summary-label">Out Of Stock</span>
                <span class="summary-value">{{ $outOfStockProducts }}</span>
                <p class="summary-note">Products currently unavailable.</p>
            </div>
        </div>

        <div class="card summary-card info">
            <div class="card-body">
                <span class="summary-label">Average Sell Price</span>
                <span class="summary-value" style="font-size: 1.5rem;">Rs. {{ number_format($avgSellPrice, 2) }}</span>
                <p class="summary-note">Average selling price across products.</p>
            </div>
        </div>
    </div>

    <div class="card products-panel">
        <div class="card-body">
            <div class="panel-head">
                <div>
                    <h2>Product List</h2>
                    <p>Search, review pricing, check warranty, and edit products from one table.</p>
                </div>
            </div>

            <div class="table-shell">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 products-table" id="productsTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th class="text-end">Dealer Price</th>
                                <th class="text-end">Sell Price</th>
                                <th class="text-center">Warranty</th>
                                <th class="text-center">Stock</th>
                                <th class="text-end" style="width: 180px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                @php $qty = $product->stock?->quantity ?? 0; @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $product->name }}</div>
                                        <small class="text-muted">Product item</small>
                                    </td>

                                    <td class="text-end">
                                        <span class="price-text">Rs. {{ number_format($product->dealer_price, 2) }}</span>
                                    </td>

                                    <td class="text-end">
                                        <span class="price-text">Rs. {{ number_format($product->sell_price, 2) }}</span>
                                    </td>

                                    <td class="text-center">
                                        <span class="warranty-badge {{ $product->warranty_days == 0 ? 'none' : 'yes' }}">
                                            {{ $product->warranty_days == 0 ? 'No Warranty' : $product->warranty_text }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <span class="stock-badge {{ $qty <= 0 ? 'out' : ($qty <= 5 ? 'low' : 'ok') }}">
                                            {{ $qty <= 0 ? 'Out' : $qty }}
                                        </span>
                                    </td>

                                    <td class="text-end">
                                        <div class="action-group">
                                            <a class="btn btn-sm btn-outline-warning" href="{{ route('products.edit', $product) }}">
                                                Edit
                                            </a>

                                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('Delete this product?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" type="submit">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof $ === 'undefined' || !$.fn.DataTable) {
            return;
        }

        $('#productsTable').DataTable({
            pageLength: 10,
            lengthChange: true,
            searching: true,
            ordering: true,
            paging: true,
            responsive: false
        });
    });
</script>
@endsection
