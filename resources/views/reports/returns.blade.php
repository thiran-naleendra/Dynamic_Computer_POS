@extends('layouts.app')
@section('content')
<style>
    .report-wrap { display:grid; gap:1.5rem; }
    .report-hero, .report-panel, .metric-card { border:1px solid rgba(24,50,77,.08); border-radius:20px; background:rgba(255,255,255,.92); box-shadow:0 14px 34px rgba(15,23,42,.06); }
    .report-hero { padding:1.2rem 1.3rem; display:flex; justify-content:space-between; align-items:center; gap:1rem; }
    .report-hero h1 { margin:0 0 .25rem; font-size:clamp(1.7rem,2.8vw,2.2rem); font-weight:800; }
    .report-hero p { margin:0; color:var(--text-muted,#6c8098); }
    .report-panel .card-body, .metric-card .card-body { padding:1.1rem; }
    .filter-form, .metric-grid { display:grid; grid-template-columns:repeat(3, minmax(0,1fr)); gap:1rem; }
    .table-shell { border:1px solid rgba(24,50,77,.08); border-radius:16px; overflow:hidden; }
    .report-table thead th { background:rgba(220,53,69,.06); border-bottom:0; color:var(--text-muted,#6c8098); font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; }
    .metric-label { display:block; font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--text-muted,#6c8098); margin-bottom:.45rem; }
    .metric-value { display:block; font-size:1.45rem; font-weight:800; }
    html[data-theme="dark"] .report-hero, html[data-theme="dark"] .report-panel, html[data-theme="dark"] .metric-card, html[data-theme="dark"] .table-shell { background:var(--surface-strong,#162338)!important; border-color:var(--border-soft,rgba(255,255,255,.08))!important; }
    @media (max-width: 991.98px) { .filter-form, .metric-grid { grid-template-columns:1fr; } }
</style>
<div class="report-wrap">
    <div class="report-hero"><div><h1>Return Report</h1><p>Filter returns by date and review returned quantity and amounts.</p></div><a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Back</a></div>
    <div class="card report-panel"><div class="card-body"><form class="filter-form" method="GET"><div><label class="form-label fw-semibold">From</label><input type="date" name="from" value="{{ $from }}" class="form-control"></div><div><label class="form-label fw-semibold">To</label><input type="date" name="to" value="{{ $to }}" class="form-control"></div><div class="d-flex align-items-end gap-2"><button class="btn btn-warning w-100">Filter</button><a href="{{ route('reports.returns') }}" class="btn btn-outline-secondary w-100">Reset</a></div></form></div></div>
    <div class="metric-grid"><div class="card metric-card"><div class="card-body"><span class="metric-label">Total Qty</span><span class="metric-value">{{ $totalQty }}</span></div></div><div class="card metric-card"><div class="card-body"><span class="metric-label">Total Return Amount</span><span class="metric-value">Rs. {{ number_format($totalAmount ?? 0, 2) }}</span></div></div><div class="card metric-card"><div class="card-body"><span class="metric-label">Entries</span><span class="metric-value">{{ $returns->count() }}</span></div></div></div>
    <div class="card report-panel"><div class="card-body"><div class="table-shell"><div class="table-responsive"><table class="table table-hover align-middle mb-0 report-table" id="returnTable"><thead><tr><th>Return No</th><th>Date</th><th>Customer</th><th>Tel</th><th class="text-center">Qty</th><th class="text-end">Amount</th></tr></thead><tbody>@foreach($returns as $return)<tr><td class="fw-semibold">{{ $return->return_no }}</td><td>{{ \Carbon\Carbon::parse($return->return_date)->format('Y-m-d') }}</td><td>{{ $return->customer_name ?? '-' }}</td><td>{{ $return->customer_tel ?? '-' }}</td><td class="text-center">{{ $return->total_qty }}</td><td class="text-end">Rs. {{ number_format($return->grand_total ?? 0, 2) }}</td></tr>@endforeach</tbody></table></div></div></div></div>
</div>
<script>document.addEventListener('DOMContentLoaded',function(){if(typeof DataTable==='undefined')return;const t=document.querySelector('#returnTable');if(t)new DataTable(t,{pageLength:10,ordering:true,searching:true,info:false});});</script>
@endsection
