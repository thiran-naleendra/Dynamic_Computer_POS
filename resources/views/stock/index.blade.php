@extends('layouts.app')

@section('content')
@php
    $totalProducts = $products->count();
    $totalUnits = $products->sum(fn ($product) => $product->stock?->quantity ?? 0);
    $lowStockProducts = $products->filter(fn ($product) => (($product->stock?->quantity ?? 0) > 0) && (($product->stock?->quantity ?? 0) <= 2))->count();
    $outOfStockProducts = $products->filter(fn ($product) => (($product->stock?->quantity ?? 0) <= 0))->count();
@endphp

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    .stock-wrap {
        display: grid;
        gap: 1.5rem;
    }

    .stock-hero {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem 1.35rem;
        border: 1px solid rgba(24, 50, 77, 0.08);
        border-radius: 20px;
        background:
            radial-gradient(circle at top right, rgba(25, 135, 84, 0.12), transparent 25%),
            linear-gradient(135deg, rgba(255, 255, 255, 0.96), rgba(247, 252, 249, 0.95));
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.06);
    }

    .stock-hero h1 {
        margin: 0 0 .3rem;
        font-size: clamp(1.75rem, 2.8vw, 2.3rem);
        font-weight: 800;
        color: var(--text-main, #18324d);
    }

    .stock-hero p {
        margin: 0;
        color: var(--text-muted, #6c8098);
        max-width: 640px;
    }

    .hero-date {
        padding: .8rem 1rem;
        border-radius: 16px;
        background: rgba(25, 135, 84, 0.08);
        color: var(--text-main, #18324d);
        font-weight: 700;
        white-space: nowrap;
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

    .summary-card.primary {
        background: linear-gradient(180deg, rgba(25, 135, 84, 0.08), rgba(255, 255, 255, 0.95));
    }

    .summary-card.warning {
        background: linear-gradient(180deg, rgba(255, 193, 7, 0.14), rgba(255, 255, 255, 0.95));
    }

    .summary-card.danger {
        background: linear-gradient(180deg, rgba(220, 53, 69, 0.1), rgba(255, 255, 255, 0.95));
    }

    .summary-card.info {
        background: linear-gradient(180deg, rgba(13, 202, 240, 0.12), rgba(255, 255, 255, 0.95));
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

    .stock-grid {
        display: grid;
        grid-template-columns: minmax(320px, 390px) minmax(0, 1fr);
        gap: 1rem;
    }

    .stock-panel {
        border: 1px solid rgba(24, 50, 77, 0.08);
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.92);
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .stock-panel .card-body {
        padding: 1.15rem;
    }

    .panel-head {
        margin-bottom: 1rem;
    }

    .panel-head h2 {
        margin: 0 0 .2rem;
        font-size: 1.25rem;
        font-weight: 800;
    }

    .panel-head p {
        margin: 0;
        color: var(--text-muted, #6c8098);
    }

    .stock-form {
        display: grid;
        gap: 1rem;
    }

    .form-card {
        padding: 1rem;
        border-radius: 16px;
        background: rgba(25, 135, 84, 0.05);
        border: 1px solid rgba(25, 135, 84, 0.08);
    }

    .form-card .form-label {
        font-weight: 700;
        margin-bottom: .45rem;
    }

    .quick-rules {
        display: grid;
        gap: .75rem;
    }

    .quick-rule {
        padding: .85rem .95rem;
        border-radius: 14px;
        background: rgba(36, 87, 167, 0.06);
    }

    .quick-rule strong {
        display: block;
        margin-bottom: .2rem;
        color: var(--text-main, #18324d);
    }

    .quick-rule span {
        color: var(--text-muted, #6c8098);
        font-size: .92rem;
    }

    .table-shell {
        border: 1px solid rgba(24, 50, 77, 0.08);
        border-radius: 16px;
        overflow: hidden;
    }

    .stock-table thead th {
        background: rgba(25, 135, 84, 0.08);
        border-bottom: 0;
        color: var(--text-muted, #6c8098);
        font-size: .8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .stock-pill,
    .price-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 88px;
        padding: .38rem .72rem;
        border-radius: 999px;
        font-size: .82rem;
        font-weight: 700;
    }

    .stock-pill.out {
        background: rgba(220, 53, 69, 0.14);
        color: #b02a37;
    }

    .stock-pill.low {
        background: rgba(255, 193, 7, 0.18);
        color: #8c6a00;
    }

    .stock-pill.ok {
        background: rgba(25, 135, 84, 0.16);
        color: #146c43;
    }

    .price-pill {
        background: rgba(13, 110, 253, 0.12);
        color: #0b5ed7;
    }

    .select2-container .select2-selection--single {
        height: calc(2.7rem + 2px);
        padding: .45rem .75rem;
        border: 1px solid rgba(24, 50, 77, 0.12);
        border-radius: .75rem;
        display: flex;
        align-items: center;
        background: #fff;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5rem;
        color: var(--text-main, #18324d);
        padding-left: 0;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(2.7rem + 2px);
        right: 10px;
    }

    .select2-container {
        width: 100% !important;
    }

    html[data-theme="dark"] .stock-hero,
    html[data-theme="dark"] .summary-card,
    html[data-theme="dark"] .stock-panel,
    html[data-theme="dark"] .table-shell,
    html[data-theme="dark"] .form-card,
    html[data-theme="dark"] .quick-rule,
    html[data-theme="dark"] .select2-container .select2-selection--single {
        background: var(--surface-strong, #162338) !important;
        border-color: var(--border-soft, rgba(255, 255, 255, 0.08)) !important;
        color: var(--text-main, #e6eef8) !important;
    }

    @media (max-width: 1199.98px) {
        .summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 991.98px) {
        .stock-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767.98px) {
        .stock-hero {
            flex-direction: column;
            align-items: flex-start;
        }

        .summary-grid {
            grid-template-columns: 1fr;
        }

        .hero-date {
            white-space: normal;
        }
    }
</style>

<div class="stock-wrap">
    <div class="stock-hero">
        <div>
            <h1>Stock</h1>
            <p>Update quantities quickly, keep inventory accurate, and monitor low-stock items before they affect billing.</p>
        </div>

        <div class="hero-date">
            {{ now()->timezone('Asia/Colombo')->format('l, d F Y') }}
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-0" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="summary-grid">
        <div class="card summary-card primary">
            <div class="card-body">
                <span class="summary-label">Total Products</span>
                <span class="summary-value">{{ $totalProducts }}</span>
                <p class="summary-note">Products being tracked in stock.</p>
            </div>
        </div>

        <div class="card summary-card info">
            <div class="card-body">
                <span class="summary-label">Total Units</span>
                <span class="summary-value">{{ $totalUnits }}</span>
                <p class="summary-note">Combined quantity across all products.</p>
            </div>
        </div>

        <div class="card summary-card warning">
            <div class="card-body">
                <span class="summary-label">Low Stock</span>
                <span class="summary-value">{{ $lowStockProducts }}</span>
                <p class="summary-note">Products with only a few units left.</p>
            </div>
        </div>

        <div class="card summary-card danger">
            <div class="card-body">
                <span class="summary-label">Out Of Stock</span>
                <span class="summary-value">{{ $outOfStockProducts }}</span>
                <p class="summary-note">Products currently unavailable.</p>
            </div>
        </div>
    </div>

    <div class="stock-grid">
        <div class="card stock-panel">
            <div class="card-body">
                <div class="panel-head">
                    <h2>Update Stock</h2>
                    <p>Add quantity, reduce stock, or set an exact amount for any product.</p>
                </div>

                <form method="POST" action="{{ route('stock.add') }}" class="stock-form">
                    @csrf

                    <div class="form-card">
                        <label class="form-label">Product</label>
                        <select name="product_id" class="form-select select-product" required>
                            <option value="">-- Select Product --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-card">
                        <label class="form-label">Action</label>
                        <select name="action" class="form-select" id="stockAction" required>
                            <option value="add" {{ old('action') == 'add' ? 'selected' : '' }}>Add quantity</option>
                            <option value="reduce" {{ old('action') == 'reduce' ? 'selected' : '' }}>Reduce quantity</option>
                            <option value="set" {{ old('action') == 'set' ? 'selected' : '' }}>Set exact quantity</option>
                        </select>
                    </div>

                    <div class="form-card">
                        <label class="form-label" id="qtyLabel">Quantity</label>
                        <input type="number" name="qty" class="form-control" min="0" value="{{ old('qty') }}" required>
                    </div>

                    <button class="btn btn-success btn-lg w-100" type="submit">Update Stock</button>
                </form>

                <hr class="my-4">

                <div class="quick-rules">
                    <div class="quick-rule">
                        <strong>Add</strong>
                        <span>Use when new stock arrives and you want to increase the current quantity.</span>
                    </div>

                    <div class="quick-rule">
                        <strong>Reduce</strong>
                        <span>Use for corrections or manual adjustments. Stock will never go below zero.</span>
                    </div>

                    <div class="quick-rule">
                        <strong>Set exact quantity</strong>
                        <span>Use after a stock count when you want the final quantity to match the physical count.</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card stock-panel">
            <div class="card-body">
                <div class="panel-head">
                    <h2>Current Stock</h2>
                    <p>Search products, review quantities, and spot low-stock items quickly.</p>
                </div>

                <div class="table-shell">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 stock-table" id="stockTable">
                            <thead>
                                <tr>
                                    <th style="min-width: 220px;">Product</th>
                                    <th class="text-center" style="min-width: 120px;">Stock Qty</th>
                                    <th class="text-end" style="min-width: 150px;">Sell Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                    @php $qty = $product->stock?->quantity ?? 0; @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $product->name }}</div>
                                            <small class="text-muted">
                                                {{ $qty <= 0 ? 'Needs restocking' : ($qty <= 2 ? 'Low stock alert' : 'In stock') }}
                                            </small>
                                        </td>

                                        <td class="text-center">
                                            <span class="stock-pill {{ $qty <= 0 ? 'out' : ($qty <= 2 ? 'low' : 'ok') }}">
                                                {{ $qty <= 0 ? 'Out' : $qty }}
                                            </span>
                                        </td>

                                        <td class="text-end">
                                            <span class="price-pill">Rs. {{ number_format($product->sell_price, 2) }}</span>
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
</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('.select-product').select2({
                placeholder: '-- Select Product --',
                allowClear: true,
                width: '100%'
            });
        }

        if (typeof DataTable !== 'undefined') {
            const table = document.querySelector('#stockTable');

            if (table) {
                new DataTable(table, {
                    pageLength: 10,
                    lengthChange: true,
                    searching: true,
                    ordering: true,
                    info: false
                });
            }
        }

        function updateQtyUI() {
            const actionSelect = document.getElementById('stockAction');
            const qtyLabel = document.getElementById('qtyLabel');
            const qtyInput = document.querySelector('input[name="qty"]');

            if (!actionSelect || !qtyLabel || !qtyInput) {
                return;
            }

            if (actionSelect.value === 'set') {
                qtyLabel.textContent = 'Exact Quantity';
                qtyInput.min = 0;
            } else {
                qtyLabel.textContent = 'Quantity';
                qtyInput.min = 1;
            }
        }

        const stockAction = document.getElementById('stockAction');
        if (stockAction) {
            stockAction.addEventListener('change', updateQtyUI);
            updateQtyUI();
        }
    });
</script>

@endsection
