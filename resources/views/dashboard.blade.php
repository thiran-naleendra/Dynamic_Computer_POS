@extends('layouts.app')

@section('content')
@php
    $lowStockCount = isset($lowStocks) ? $lowStocks->count() : 0;
    $outOfStockCount = isset($lowStocks)
        ? $lowStocks->filter(fn ($product) => (($product->stock?->quantity ?? 0) <= 0))->count()
        : 0;
@endphp

<style>
    .dashboard-wrap {
        display: grid;
        gap: 1.5rem;
    }

    .dashboard-banner {
        border: 1px solid rgba(36, 87, 167, 0.08);
        border-radius: 22px;
        padding: 1.35rem 1.4rem;
        background:
            radial-gradient(circle at top right, rgba(40, 167, 69, 0.16), transparent 24%),
            linear-gradient(135deg, rgba(27, 64, 122, 0.96), rgba(36, 87, 167, 0.92));
        color: #fff;
        box-shadow: 0 18px 36px rgba(17, 37, 70, 0.16);
    }

    .dashboard-head {
        display: flex;
        justify-content: space-between;
        align-items: end;
        gap: 1rem;
        margin-bottom: .25rem;
    }

    .dashboard-head h1 {
        margin: 0 0 .3rem;
        font-size: clamp(1.8rem, 2.8vw, 2.4rem);
        font-weight: 800;
        letter-spacing: -.02em;
        color: #fff;
    }

    .dashboard-head p {
        margin: 0;
        color: rgba(255, 255, 255, 0.8);
        max-width: 640px;
    }

    .dashboard-date {
        padding: .8rem 1rem;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.16);
        font-weight: 700;
        color: #fff;
        white-space: nowrap;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
    }

    .stat-card {
        border: 1px solid rgba(24, 50, 77, 0.08);
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.88);
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
    }

    .stat-card .card-body {
        padding: 1.1rem 1.15rem;
    }

    .stat-label {
        display: block;
        font-size: .82rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--text-muted, #6c8098);
        margin-bottom: .55rem;
    }

    .stat-value {
        display: block;
        font-size: 2rem;
        line-height: 1;
        font-weight: 800;
        margin-bottom: .4rem;
        color: var(--text-main, #18324d);
    }

    .stat-note {
        margin: 0;
        color: var(--text-muted, #6c8098);
        font-size: .92rem;
    }

    .stat-card.primary {
        background: linear-gradient(135deg, rgba(36, 87, 167, 0.14), rgba(36, 87, 167, 0.04)), rgba(255, 255, 255, 0.92);
    }

    .stat-card.success {
        background: linear-gradient(135deg, rgba(25, 135, 84, 0.14), rgba(25, 135, 84, 0.04)), rgba(255, 255, 255, 0.92);
    }

    .stat-card.warning {
        background: linear-gradient(135deg, rgba(255, 193, 7, 0.18), rgba(255, 193, 7, 0.05)), rgba(255, 255, 255, 0.92);
    }

    .stat-card.info {
        background: linear-gradient(135deg, rgba(13, 202, 240, 0.16), rgba(13, 202, 240, 0.04)), rgba(255, 255, 255, 0.92);
    }

    .section-head {
        display: flex;
        justify-content: space-between;
        align-items: end;
        gap: 1rem;
        margin-bottom: .85rem;
    }

    .section-head h2 {
        margin: 0 0 .15rem;
        font-size: 1.35rem;
        font-weight: 800;
    }

    .section-head p {
        margin: 0;
        color: var(--text-muted, #6c8098);
    }

    .actions-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
    }

    .action-card {
        border: 1px solid rgba(24, 50, 77, 0.08);
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .action-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 32px rgba(15, 23, 42, 0.09);
    }

    .action-card.products {
        background: linear-gradient(180deg, rgba(36, 87, 167, 0.08), rgba(255, 255, 255, 0.94));
    }

    .action-card.stock {
        background: linear-gradient(180deg, rgba(25, 135, 84, 0.08), rgba(255, 255, 255, 0.94));
    }

    .action-card.invoices {
        background: linear-gradient(180deg, rgba(33, 37, 41, 0.06), rgba(255, 255, 255, 0.94));
    }

    .action-card.reports {
        background: linear-gradient(180deg, rgba(13, 110, 253, 0.06), rgba(255, 255, 255, 0.94));
    }

    .action-card.returns {
        background: linear-gradient(180deg, rgba(220, 53, 69, 0.08), rgba(255, 255, 255, 0.94));
    }

    .action-card.service {
        background: linear-gradient(180deg, rgba(255, 193, 7, 0.09), rgba(255, 255, 255, 0.94));
    }

    .action-card .card-body {
        padding: 1.15rem;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .action-title {
        font-size: 1.08rem;
        font-weight: 800;
        margin-bottom: .35rem;
        color: var(--text-main, #18324d);
    }

    .action-text {
        color: var(--text-muted, #6c8098);
        margin-bottom: 1rem;
        min-height: 48px;
    }

    .action-footer {
        margin-top: auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: .75rem;
        padding-top: .95rem;
        border-top: 1px solid rgba(24, 50, 77, 0.08);
    }

    .action-tag {
        display: inline-flex;
        align-items: center;
        padding: .38rem .65rem;
        border-radius: 999px;
        background: rgba(36, 87, 167, 0.08);
        color: var(--text-main, #18324d);
        font-size: .8rem;
        font-weight: 700;
    }

    .dashboard-panel {
        border: 1px solid rgba(24, 50, 77, 0.08);
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .dashboard-panel.stock-panel {
        background: linear-gradient(180deg, rgba(36, 87, 167, 0.05), rgba(255, 255, 255, 0.94));
    }

    .dashboard-panel.notes-panel {
        background: linear-gradient(180deg, rgba(25, 135, 84, 0.05), rgba(255, 255, 255, 0.94));
    }

    .dashboard-panel .card-body {
        padding: 1.15rem;
    }

    .dashboard-split {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(280px, 1fr);
        gap: 1rem;
    }

    .table-wrap {
        border: 1px solid rgba(24, 50, 77, 0.08);
        border-radius: 16px;
        overflow: hidden;
    }

    .clean-table thead th {
        background: rgba(36, 87, 167, 0.06);
        border-bottom: 0;
        color: var(--text-muted, #6c8098);
        font-size: .8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .stock-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 86px;
        padding: .38rem .72rem;
        border-radius: 999px;
        font-weight: 700;
        font-size: .82rem;
    }

    .stock-pill.warning {
        background: rgba(255, 193, 7, 0.18);
        color: #8c6a00;
    }

    .stock-pill.danger {
        background: rgba(220, 53, 69, 0.14);
        color: #b02a37;
    }

    .info-list {
        display: grid;
        gap: .8rem;
    }

    .info-item {
        padding: .9rem 1rem;
        border-radius: 14px;
        background: rgba(36, 87, 167, 0.06);
    }

    .info-item strong {
        display: block;
        margin-bottom: .25rem;
        color: var(--text-main, #18324d);
    }

    .info-item span {
        color: var(--text-muted, #6c8098);
        font-size: .92rem;
    }

    html[data-theme="dark"] .stat-card,
    html[data-theme="dark"] .action-card,
    html[data-theme="dark"] .dashboard-panel,
    html[data-theme="dark"] .info-item,
    html[data-theme="dark"] .table-wrap {
        background: var(--surface-strong, #162338) !important;
        border-color: var(--border-soft, rgba(255, 255, 255, 0.08)) !important;
    }

    html[data-theme="dark"] .action-footer,
    html[data-theme="dark"] .clean-table thead th {
        border-color: var(--border-soft, rgba(255, 255, 255, 0.08)) !important;
    }

    html[data-theme="dark"] .dashboard-banner {
        background:
            radial-gradient(circle at top right, rgba(45, 190, 115, 0.16), transparent 24%),
            linear-gradient(135deg, rgba(20, 35, 58, 0.98), rgba(28, 59, 107, 0.96));
        border-color: var(--border-soft, rgba(255, 255, 255, 0.08));
    }

    @media (max-width: 1199.98px) {
        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .actions-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 991.98px) {
        .dashboard-split {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767.98px) {
        .dashboard-head,
        .section-head {
            flex-direction: column;
            align-items: flex-start;
        }

        .dashboard-date {
            white-space: normal;
        }

        .stats-grid,
        .actions-grid {
            grid-template-columns: 1fr;
        }

        .action-footer {
            flex-direction: column;
            align-items: stretch;
        }

        .action-footer .btn {
            width: 100%;
        }
    }
</style>

<div class="dashboard-wrap">
    <div class="dashboard-banner">
        <div class="dashboard-head">
            <div>
                <h1>Dashboard</h1>
                <p>
                    @if(auth()->user()->isAdmin())
                        Track the most important activity in one place and move quickly between products, stock, invoices, returns, and reports.
                    @else
                        Create invoices quickly and keep daily billing moving smoothly from one place.
                    @endif
                </p>
            </div>
            <div class="dashboard-date">
                {{ now()->timezone('Asia/Colombo')->format('l, d F Y') }}
            </div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="card stat-card primary">
            <div class="card-body">
                <span class="stat-label">Low Stock Items</span>
                <span class="stat-value">{{ $lowStockCount }}</span>
                <p class="stat-note">Products that need attention soon.</p>
            </div>
        </div>

        <div class="card stat-card success">
            <div class="card-body">
                <span class="stat-label">Out Of Stock</span>
                <span class="stat-value">{{ $outOfStockCount }}</span>
                <p class="stat-note">Items currently unavailable for sale.</p>
            </div>
        </div>

        <div class="card stat-card warning">
            <div class="card-body">
                <span class="stat-label">Current User</span>
                <span class="stat-value" style="font-size: 1.45rem;">{{ Auth::user()->name ?? 'User' }}</span>
                <p class="stat-note">Signed in and ready to work.</p>
            </div>
        </div>

        <div class="card stat-card info">
            <div class="card-body">
                <span class="stat-label">Current Time</span>
                <span class="stat-value" style="font-size: 1.7rem;">{{ now()->timezone('Asia/Colombo')->format('h:i A') }}</span>
                <p class="stat-note">Colombo local time.</p>
            </div>
        </div>
    </div>

    <div>
        <div class="section-head">
            <div>
                <h2>Quick Actions</h2>
                <p>{{ auth()->user()->isAdmin() ? 'Open the main areas your team uses every day.' : 'Open billing pages used by the cashier role.' }}</p>
            </div>
        </div>

        <div class="actions-grid">
            @if(auth()->user()->isAdmin())
                <div class="card action-card products">
                    <div class="card-body">
                        <div class="action-title">Products</div>
                        <div class="action-text">Add new items, update prices, and maintain your product list.</div>
                        <div class="action-footer">
                            <span class="action-tag">Catalog</span>
                            <a href="{{ route('products.index') }}" class="btn btn-primary">Manage</a>
                        </div>
                    </div>
                </div>

                <div class="card action-card stock">
                    <div class="card-body">
                        <div class="action-title">Stock</div>
                        <div class="action-text">Increase, reduce, or set quantities so inventory stays accurate.</div>
                        <div class="action-footer">
                            <span class="action-tag">Inventory</span>
                            <a href="{{ route('stock.index') }}" class="btn btn-success">Open</a>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card action-card invoices">
                <div class="card-body">
                    <div class="action-title">Invoices</div>
                    <div class="action-text">Create bills and print invoices while stock updates automatically.</div>
                    <div class="action-footer">
                        <span class="action-tag">Sales</span>
                        <a href="{{ route('invoices.index') }}" class="btn btn-dark">Manage</a>
                    </div>
                </div>
            </div>

            @if(auth()->user()->isAdmin())
                <div class="card action-card reports">
                    <div class="card-body">
                        <div class="action-title">Reports</div>
                        <div class="action-text">Review sales, profit, stock value, and returns in one place.</div>
                        <div class="action-footer">
                            <span class="action-tag">Insights</span>
                            <a href="{{ route('reports.index') }}" class="btn btn-outline-dark">View</a>
                        </div>
                    </div>
                </div>

                <div class="card action-card returns">
                    <div class="card-body">
                        <div class="action-title">Returns</div>
                        <div class="action-text">Record returned items and place quantities back into stock safely.</div>
                        <div class="action-footer">
                            <span class="action-tag">Reverse Flow</span>
                            <a href="{{ route('returns.index') }}" class="btn btn-danger">Open</a>
                        </div>
                    </div>
                </div>

                <div class="card action-card service">
                    <div class="card-body">
                        <div class="action-title">Service Notes</div>
                        <div class="action-text">Track repair intake, complaints, and service return details.</div>
                        <div class="action-footer">
                            <span class="action-tag">After Sales</span>
                            <a href="{{ route('service-notes.index') }}" class="btn btn-outline-warning">Track</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="dashboard-split">
        <div class="card dashboard-panel stock-panel">
            <div class="card-body">
                <div class="section-head">
                    <div>
                        <h2>Low Stock</h2>
                        <p>Products that should be checked or replenished first.</p>
                    </div>
                    <a href="{{ route('stock.index') }}" class="btn btn-sm btn-outline-danger">View Stock</a>
                </div>

                @if($lowStockCount > 0)
                    <div class="table-wrap">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 clean-table" id="lowStockTable">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-center" style="width: 140px;">Quantity</th>
                                        <th class="text-end" style="width: 170px;">Sell Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStocks as $product)
                                        @php $qty = $product->stock?->quantity ?? 0; @endphp
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $product->name }}</div>
                                                <small class="text-muted">
                                                    {{ $qty <= 0 ? 'Out of stock' : 'Low stock warning' }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <span class="stock-pill {{ $qty <= 0 ? 'danger' : 'warning' }}">
                                                    {{ $qty <= 0 ? 'Out' : $qty . ' left' }}
                                                </span>
                                            </td>
                                            <td class="text-end fw-semibold">Rs. {{ number_format($product->sell_price, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="alert alert-success border-0 mb-0">
                        No low stock items right now. Inventory looks healthy.
                    </div>
                @endif
            </div>
        </div>

        <div class="card dashboard-panel notes-panel">
            <div class="card-body">
                <div class="section-head">
                    <div>
                        <h2>{{ auth()->user()->isAdmin() ? 'Quick Notes' : 'Cashier Access' }}</h2>
                        <p>{{ auth()->user()->isAdmin() ? 'Simple reminders for daily work.' : 'What the cashier role can do in this system.' }}</p>
                    </div>
                </div>

                <div class="info-list">
                    @if(auth()->user()->isAdmin())
                        <div class="info-item">
                            <strong>Add products first</strong>
                            <span>Create the product before updating stock or adding it to an invoice.</span>
                        </div>

                        <div class="info-item">
                            <strong>Stock updates matter</strong>
                            <span>Correct quantities help invoice validation and low-stock alerts stay reliable.</span>
                        </div>

                        <div class="info-item">
                            <strong>Invoices reduce stock</strong>
                            <span>Returns increase stock back, so both sales and returns affect inventory automatically.</span>
                        </div>

                        <div class="info-item">
                            <strong>Use reports regularly</strong>
                            <span>Check sales, profit, and stock reports to stay ahead of inventory issues.</span>
                        </div>
                    @else
                        <div class="info-item">
                            <strong>Create invoices</strong>
                            <span>Cashier users can create, view, and print customer bills.</span>
                        </div>
                        <div class="info-item">
                            <strong>Stock is checked automatically</strong>
                            <span>The invoice screen validates available stock before saving a bill.</span>
                        </div>
                        <div class="info-item">
                            <strong>Admin controls the full system</strong>
                            <span>Products, stock, returns, reports, service notes, and user roles stay under admin access.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const table = document.querySelector('#lowStockTable');

        if (table && typeof DataTable !== 'undefined') {
            new DataTable(table, {
                pageLength: 10,
                lengthChange: false,
                searching: true,
                ordering: true,
                info: false,
                order: [[1, 'asc']],
                columnDefs: [
                    { orderable: false, targets: [2] }
                ]
            });
        }
    });
</script>
@endsection
