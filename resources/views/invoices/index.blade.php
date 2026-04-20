@extends('layouts.app')

@section('content')
@php
    $totalInvoices = $invoices->count();
    $todayInvoices = $invoices->filter(fn ($invoice) => $invoice->invoice_date === now()->toDateString())->count();
    $totalSales = $invoices->sum('grand_total');
    $averageInvoice = $totalInvoices > 0 ? $invoices->avg('grand_total') : 0;
@endphp

<style>
    .invoices-wrap {
        display: grid;
        gap: 1.5rem;
    }

    .invoices-hero {
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

    .invoices-hero h1 {
        margin: 0 0 .3rem;
        font-size: clamp(1.75rem, 2.8vw, 2.3rem);
        font-weight: 800;
        color: var(--text-main, #18324d);
    }

    .invoices-hero p {
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

    .invoices-panel {
        border: 1px solid rgba(24, 50, 77, 0.08);
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.92);
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .invoices-panel .card-body {
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

    .invoice-table {
        width: 100% !important;
    }

    .invoice-table td,
    .invoice-table th {
        white-space: nowrap;
        vertical-align: middle;
    }

    .invoice-table thead th {
        background: rgba(13, 110, 253, 0.06);
        border-bottom: 0;
        color: var(--text-muted, #6c8098);
        font-size: .8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .serial-text {
        max-width: 240px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: inline-block;
        vertical-align: middle;
    }

    .total-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: .4rem .8rem;
        border-radius: 999px;
        background: rgba(25, 135, 84, 0.14);
        color: #146c43;
        font-size: .84rem;
        font-weight: 800;
    }

    .invoice-no {
        font-weight: 800;
        color: var(--text-main, #18324d);
    }

    .action-buttons {
        display: inline-flex;
        gap: .45rem;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .action-buttons .btn,
    .action-buttons form {
        margin: 0 !important;
    }

    .action-buttons .btn {
        border-radius: 12px;
        font-weight: 700;
    }

    .table-scroll {
        overflow-x: auto;
    }

    html[data-theme="dark"] .invoices-hero,
    html[data-theme="dark"] .summary-card,
    html[data-theme="dark"] .invoices-panel,
    html[data-theme="dark"] .table-shell {
        background: var(--surface-strong, #162338) !important;
        border-color: var(--border-soft, rgba(255, 255, 255, 0.08)) !important;
    }

    @media (max-width: 1199.98px) {
        .summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .invoices-hero,
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

        .action-buttons {
            justify-content: flex-start;
        }
    }
</style>

<div class="invoices-wrap">
    <div class="invoices-hero">
        <div>
            <h1>Invoices</h1>
            <p>Review sales documents, search invoice history, print copies, and manage invoices from one place.</p>
        </div>

        <div class="hero-actions">
            <a href="{{ route('invoices.create') }}" class="btn btn-success">New Invoice</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="summary-grid">
        <div class="card summary-card primary">
            <div class="card-body">
                <span class="summary-label">Total Invoices</span>
                <span class="summary-value">{{ $totalInvoices }}</span>
                <p class="summary-note">Invoices currently recorded in the system.</p>
            </div>
        </div>

        <div class="card summary-card success">
            <div class="card-body">
                <span class="summary-label">Today</span>
                <span class="summary-value">{{ $todayInvoices }}</span>
                <p class="summary-note">Invoices created on {{ now()->format('Y-m-d') }}.</p>
            </div>
        </div>

        <div class="card summary-card warning">
            <div class="card-body">
                <span class="summary-label">Total Sales</span>
                <span class="summary-value" style="font-size: 1.5rem;">Rs. {{ number_format($totalSales, 2) }}</span>
                <p class="summary-note">Combined invoice total from this list.</p>
            </div>
        </div>

        <div class="card summary-card info">
            <div class="card-body">
                <span class="summary-label">Average Invoice</span>
                <span class="summary-value" style="font-size: 1.5rem;">Rs. {{ number_format($averageInvoice, 2) }}</span>
                <p class="summary-note">Average invoice value across all records.</p>
            </div>
        </div>
    </div>

    <div class="card invoices-panel">
        <div class="card-body">
            <div class="panel-head">
                <div>
                    <h2>Invoice List</h2>
                    <p>Search by invoice, customer, serial number, or date and take action quickly.</p>
                </div>
            </div>

            <div class="table-shell">
                <div class="table-scroll">
                    <table class="table table-hover align-middle mb-0 invoice-table" id="invoiceTable">
                        <thead>
                            <tr>
                                <th style="min-width: 160px;">Invoice No</th>
                                <th style="min-width: 120px;">Date</th>
                                <th style="min-width: 180px;">Customer</th>
                                <th style="min-width: 200px;">Serial Numbers</th>
                                <th class="text-end" style="min-width: 130px;">Total</th>
                                <th class="text-end" style="min-width: 270px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoices as $invoice)
                                @php
                                    $serials = $invoice->items
                                        ->pluck('serial_no')
                                        ->filter(fn ($serial) => !empty($serial))
                                        ->unique()
                                        ->values();

                                    $serialText = $serials->implode(', ');
                                @endphp

                                <tr>
                                    <td>
                                        <div class="invoice-no">{{ $invoice->invoice_no }}</div>
                                    </td>

                                    <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') }}</td>

                                    <td>{{ $invoice->customer_name ?? '-' }}</td>

                                    <td>
                                        @if ($serials->count())
                                            <span class="serial-text small">{{ $serialText }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <td class="text-end">
                                        <span class="total-pill">Rs. {{ number_format($invoice->grand_total, 2) }}</span>
                                    </td>

                                    <td class="text-end">
                                        <div class="action-buttons">
                                            <a class="btn btn-sm btn-outline-primary" href="{{ route('invoices.show', $invoice) }}">
                                                View
                                            </a>

                                            <a class="btn btn-sm btn-outline-secondary" target="_blank" href="{{ route('invoices.print', $invoice) }}">
                                                Print
                                            </a>

                                            <a class="btn btn-sm btn-outline-warning" href="{{ route('invoices.edit', $invoice) }}">
                                                Edit
                                            </a>

                                            <form action="{{ route('invoices.delete', $invoice) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this invoice?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
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
        if (typeof DataTable === 'undefined') {
            return;
        }

        const table = document.querySelector('#invoiceTable');

        if (table) {
            new DataTable(table, {
                pageLength: 10,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: false,
                scrollX: true,
                order: [[1, 'desc']],
                columnDefs: [
                    {
                        orderable: false,
                        searchable: false,
                        targets: -1
                    }
                ]
            });
        }
    });
</script>
@endsection
