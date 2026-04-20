@extends('layouts.app')
@section('content')
<style>
    .report-wrap { display:grid; gap:1.5rem; }
    .report-hero, .report-panel { border:1px solid rgba(24,50,77,.08); border-radius:20px; background:rgba(255,255,255,.92); box-shadow:0 14px 34px rgba(15,23,42,.06); }
    .report-hero { padding:1.2rem 1.3rem; display:flex; justify-content:space-between; align-items:center; gap:1rem; }
    .report-hero h1 { margin:0 0 .25rem; font-size:clamp(1.7rem,2.8vw,2.2rem); font-weight:800; }
    .report-hero p { margin:0; color:var(--text-muted,#6c8098); }
    .report-panel .card-body { padding:1.1rem; }
    .filter-form { display:grid; grid-template-columns:repeat(3, minmax(0,1fr)); gap:1rem; }
    .table-shell { border:1px solid rgba(24,50,77,.08); border-radius:16px; overflow:hidden; }
    .report-table thead th { background:rgba(13,110,253,.06); border-bottom:0; color:var(--text-muted,#6c8098); font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; }
    html[data-theme="dark"] .report-hero, html[data-theme="dark"] .report-panel, html[data-theme="dark"] .table-shell { background:var(--surface-strong,#162338)!important; border-color:var(--border-soft,rgba(255,255,255,.08))!important; }
    @media (max-width: 991.98px) { .filter-form { grid-template-columns:1fr; } }
</style>
<div class="report-wrap">
    <div class="report-hero"><div><h1>Product Sales Report</h1><p>See which products sell the most by quantity and value.</p></div><a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Back</a></div>
    <div class="card report-panel"><div class="card-body"><form class="filter-form" method="GET"><div><label class="form-label fw-semibold">From</label><input type="date" class="form-control" name="from" value="{{ $from }}"></div><div><label class="form-label fw-semibold">To</label><input type="date" class="form-control" name="to" value="{{ $to }}"></div><div class="d-flex align-items-end"><button class="btn btn-dark w-100">Filter</button></div></form></div></div>
    <div class="card report-panel"><div class="card-body"><div class="table-shell"><div class="table-responsive"><table class="table table-hover align-middle mb-0 report-table" id="prodSalesTable"><thead><tr><th>Product</th><th class="text-center">Total Qty</th><th class="text-end">Total Amount</th></tr></thead><tbody>@foreach($rows as $row)<tr><td class="fw-semibold">{{ $row->item_name }}</td><td class="text-center">{{ $row->total_qty }}</td><td class="text-end">Rs. {{ number_format($row->total_amount,2) }}</td></tr>@endforeach</tbody></table></div></div></div></div>
</div>
<script>document.addEventListener('DOMContentLoaded',function(){if(typeof DataTable==='undefined')return;const t=document.querySelector('#prodSalesTable');if(t)new DataTable(t,{pageLength:10,ordering:true,info:false});});</script>
@endsection
