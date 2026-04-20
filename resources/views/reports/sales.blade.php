@extends('layouts.app')
@section('content')
<style>
    .report-wrap { display:grid; gap:1.5rem; }
    .report-hero, .report-panel, .metric-card { border:1px solid rgba(24,50,77,.08); border-radius:20px; background:rgba(255,255,255,.92); box-shadow:0 14px 34px rgba(15,23,42,.06); }
    .report-hero { padding:1.2rem 1.3rem; display:flex; justify-content:space-between; align-items:center; gap:1rem; }
    .report-hero h1 { margin:0 0 .25rem; font-size:clamp(1.7rem,2.8vw,2.2rem); font-weight:800; }
    .report-hero p { margin:0; color:var(--text-muted,#6c8098); }
    .report-panel .card-body { padding:1.15rem; }
    .filter-form { display:grid; grid-template-columns:repeat(3, minmax(0,1fr)); gap:1rem; }
    .metric-grid { display:grid; grid-template-columns:repeat(3, minmax(0,1fr)); gap:1rem; }
    .metric-card .card-body { padding:1rem 1.1rem; }
    .metric-label { display:block; font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--text-muted,#6c8098); margin-bottom:.45rem; }
    .metric-value { display:block; font-size:1.6rem; font-weight:800; }
    .table-shell { border:1px solid rgba(24,50,77,.08); border-radius:16px; overflow:hidden; }
    .report-table thead th { background:rgba(13,110,253,.06); border-bottom:0; color:var(--text-muted,#6c8098); font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; }
    html[data-theme="dark"] .report-hero, html[data-theme="dark"] .report-panel, html[data-theme="dark"] .metric-card, html[data-theme="dark"] .table-shell { background:var(--surface-strong,#162338)!important; border-color:var(--border-soft,rgba(255,255,255,.08))!important; }
    @media (max-width: 991.98px) { .filter-form, .metric-grid { grid-template-columns:1fr; } }
</style>
<div class="report-wrap">
    <div class="report-hero"><div><h1>Sales Report</h1><p>Review invoices between dates and track total sales performance.</p></div><a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Back</a></div>
    <div class="card report-panel"><div class="card-body"><form class="filter-form" method="GET"><div><label class="form-label fw-semibold">From</label><input type="date" class="form-control" name="from" value="{{ $from }}"></div><div><label class="form-label fw-semibold">To</label><input type="date" class="form-control" name="to" value="{{ $to }}"></div><div class="d-flex align-items-end"><button class="btn btn-primary w-100">Filter</button></div></form></div></div>
    <div class="metric-grid">
        <div class="card metric-card"><div class="card-body"><span class="metric-label">Invoices</span><span class="metric-value">{{ $invoices->count() }}</span></div></div>
        <div class="card metric-card"><div class="card-body"><span class="metric-label">Total Sales</span><span class="metric-value">Rs. {{ number_format($totalSales,2) }}</span></div></div>
        <div class="card metric-card"><div class="card-body"><span class="metric-label">Net Sales</span><span class="metric-value">Rs. {{ number_format($netSales ?? $totalSales,2) }}</span></div></div>
    </div>
    <div class="card report-panel"><div class="card-body"><div class="table-shell"><div class="table-responsive"><table class="table table-hover align-middle mb-0 report-table" id="salesTable"><thead><tr><th>Invoice No</th><th>Date</th><th>Customer</th><th class="text-end">Total</th></tr></thead><tbody>@foreach($invoices as $invoice)<tr><td class="fw-semibold">{{ $invoice->invoice_no }}</td><td>{{ $invoice->invoice_date }}</td><td>{{ $invoice->customer_name ?? '-' }}</td><td class="text-end">Rs. {{ number_format($invoice->grand_total,2) }}</td></tr>@endforeach</tbody></table></div></div></div></div>
</div>
<script>document.addEventListener('DOMContentLoaded',function(){if(typeof DataTable==='undefined')return;const t=document.querySelector('#salesTable');if(t)new DataTable(t,{pageLength:10,ordering:true,info:false});});</script>
@endsection
