@extends('layouts.app')
@section('content')
@php
    $totalReturns = $returns->total();
    $pageQty = $returns->getCollection()->sum('total_qty');
@endphp
<style>
    .returns-wrap { display:grid; gap:1.5rem; }
    .returns-hero, .returns-panel, .summary-card { border:1px solid rgba(24,50,77,.08); border-radius:20px; background:rgba(255,255,255,.92); box-shadow:0 14px 34px rgba(15,23,42,.06); }
    .returns-hero { padding:1.25rem 1.35rem; display:flex; justify-content:space-between; align-items:center; gap:1rem; background: radial-gradient(circle at top right, rgba(220,53,69,.12), transparent 24%), linear-gradient(135deg, rgba(255,255,255,.96), rgba(255,249,249,.95)); }
    .returns-hero h1 { margin:0 0 .3rem; font-size: clamp(1.75rem,2.8vw,2.3rem); font-weight:800; }
    .returns-hero p { margin:0; color:var(--text-muted,#6c8098); max-width:620px; }
    .returns-hero .btn { min-width:150px; border-radius:14px; font-weight:700; }
    .summary-grid { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:1rem; }
    .summary-card .card-body { padding:1.05rem 1.15rem; }
    .summary-card.primary { background: linear-gradient(180deg, rgba(220,53,69,.1), rgba(255,255,255,.95)); }
    .summary-card.warning { background: linear-gradient(180deg, rgba(255,193,7,.14), rgba(255,255,255,.95)); }
    .summary-label { display:block; margin-bottom:.5rem; font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--text-muted,#6c8098); }
    .summary-value { display:block; font-size:1.9rem; line-height:1; font-weight:800; margin-bottom:.35rem; }
    .summary-note { margin:0; color:var(--text-muted,#6c8098); font-size:.92rem; }
    .returns-panel .card-body { padding:1.15rem; }
    .panel-head { display:flex; justify-content:space-between; align-items:end; gap:1rem; margin-bottom:1rem; }
    .panel-head h2 { margin:0 0 .15rem; font-size:1.3rem; font-weight:800; }
    .panel-head p { margin:0; color:var(--text-muted,#6c8098); }
    .table-shell { border:1px solid rgba(24,50,77,.08); border-radius:16px; overflow:hidden; }
    .returns-table thead th { background: rgba(220,53,69,.06); border-bottom:0; color:var(--text-muted,#6c8098); font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; }
    .qty-pill,.return-pill { display:inline-flex; align-items:center; justify-content:center; padding:.38rem .72rem; border-radius:999px; font-size:.82rem; font-weight:700; }
    .qty-pill { background: rgba(255,193,7,.18); color:#8c6a00; }
    .return-pill { background: rgba(220,53,69,.14); color:#b02a37; }
    .action-group { display:inline-flex; gap:.45rem; flex-wrap:wrap; justify-content:flex-end; }
    .action-group .btn { border-radius:12px; font-weight:700; }
    html[data-theme="dark"] .returns-hero, html[data-theme="dark"] .returns-panel, html[data-theme="dark"] .summary-card, html[data-theme="dark"] .table-shell { background: var(--surface-strong,#162338) !important; border-color: var(--border-soft,rgba(255,255,255,.08)) !important; }
    @media (max-width: 767.98px) { .returns-hero, .panel-head { flex-direction:column; align-items:flex-start; } .summary-grid { grid-template-columns:1fr; } .returns-hero .btn { width:100%; } }
</style>
<div class="returns-wrap">
    <div class="returns-hero">
        <div><h1>Item Returns</h1><p>Review return notes, check quantities coming back into stock, and print return documents quickly.</p></div>
        <a class="btn btn-success" href="{{ route('returns.create') }}">New Return</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-0">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    <div class="summary-grid">
        <div class="card summary-card primary"><div class="card-body"><span class="summary-label">Total Returns</span><span class="summary-value">{{ $totalReturns }}</span><p class="summary-note">Return notes across all pages.</p></div></div>
        <div class="card summary-card warning"><div class="card-body"><span class="summary-label">Qty On This Page</span><span class="summary-value">{{ $pageQty }}</span><p class="summary-note">Returned quantity shown in this list.</p></div></div>
    </div>
    <div class="card returns-panel">
        <div class="card-body">
            <div class="panel-head"><div><h2>Return List</h2><p>Open a return note or print a copy for records.</p></div></div>
            <div class="table-shell">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 returns-table">
                        <thead><tr><th>Return No</th><th>Date</th><th>Customer</th><th class="text-center">Total Qty</th><th class="text-end">Actions</th></tr></thead>
                        <tbody>
                            @foreach($returns as $return)
                            <tr>
                                <td class="fw-semibold">{{ $return->return_no }}</td>
                                <td>{{ $return->return_date }}</td>
                                <td>{{ $return->customer_name ?? '-' }}</td>
                                <td class="text-center"><span class="qty-pill">{{ $return->total_qty }}</span></td>
                                <td class="text-end"><div class="action-group"><a class="btn btn-sm btn-outline-primary" href="{{ route('returns.show', $return) }}">View</a><a class="btn btn-sm btn-outline-secondary" target="_blank" href="{{ route('returns.print', $return) }}">Print</a></div></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-3">{{ $returns->links() }}</div>
        </div>
    </div>
</div>
@endsection
