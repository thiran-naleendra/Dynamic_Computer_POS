@extends('layouts.app')
@section('content')
<style>
    .report-wrap { display:grid; gap:1.5rem; }
    .report-hero, .report-panel, .metric-card { border:1px solid rgba(24,50,77,.08); border-radius:20px; background:rgba(255,255,255,.92); box-shadow:0 14px 34px rgba(15,23,42,.06); }
    .report-hero { padding:1.2rem 1.3rem; display:flex; justify-content:space-between; align-items:center; gap:1rem; }
    .report-hero h1 { margin:0 0 .25rem; font-size:clamp(1.7rem,2.8vw,2.2rem); font-weight:800; }
    .report-hero p { margin:0; color:var(--text-muted,#6c8098); }
    .metric-grid { display:grid; grid-template-columns:repeat(3, minmax(0,1fr)); gap:1rem; }
    .metric-card .card-body, .report-panel .card-body { padding:1.1rem; }
    .metric-label { display:block; font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--text-muted,#6c8098); margin-bottom:.45rem; }
    .metric-value { display:block; font-size:1.45rem; font-weight:800; }
    .table-shell { border:1px solid rgba(24,50,77,.08); border-radius:16px; overflow:hidden; }
    .report-table thead th { background:rgba(25,135,84,.08); border-bottom:0; color:var(--text-muted,#6c8098); font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; }
    html[data-theme="dark"] .report-hero, html[data-theme="dark"] .report-panel, html[data-theme="dark"] .metric-card, html[data-theme="dark"] .table-shell { background:var(--surface-strong,#162338)!important; border-color:var(--border-soft,rgba(255,255,255,.08))!important; }
    @media (max-width: 991.98px) { .metric-grid { grid-template-columns:1fr; } }
</style>
<div class="report-wrap">
    <div class="report-hero"><div><h1>Stock Report</h1><p>Measure inventory quantity and stock value based on current product stock.</p></div><a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Back</a></div>
    <div class="metric-grid">
        <div class="card metric-card"><div class="card-body"><span class="metric-label">Total Qty</span><span class="metric-value">{{ $totalQty }}</span></div></div>
        <div class="card metric-card"><div class="card-body"><span class="metric-label">Stock Value Sell</span><span class="metric-value">Rs. {{ number_format($stockValueSell,2) }}</span></div></div>
        <div class="card metric-card"><div class="card-body"><span class="metric-label">Stock Value Dealer</span><span class="metric-value">Rs. {{ number_format($stockValueDealer,2) }}</span></div></div>
    </div>
    <div class="card report-panel"><div class="card-body"><div class="table-shell"><div class="table-responsive"><table class="table table-hover align-middle mb-0 report-table" id="stockReportTable"><thead><tr><th>Product</th><th class="text-center">Qty</th><th class="text-end">Dealer Price</th><th class="text-end">Sell Price</th><th class="text-end">Value (Sell)</th></tr></thead><tbody>@foreach($products as $product) @php($qty = $product->stock?->quantity ?? 0) @php($val = $qty * ($product->sell_price ?? 0))<tr><td class="fw-semibold">{{ $product->name }}</td><td class="text-center">{{ $qty <= 0 ? 'Out' : $qty }}</td><td class="text-end">Rs. {{ number_format($product->dealer_price,2) }}</td><td class="text-end">Rs. {{ number_format($product->sell_price,2) }}</td><td class="text-end fw-bold">Rs. {{ number_format($val,2) }}</td></tr>@endforeach</tbody></table></div></div></div></div>
</div>
<script>document.addEventListener('DOMContentLoaded',function(){if(typeof DataTable==='undefined')return;const t=document.querySelector('#stockReportTable');if(t)new DataTable(t,{pageLength:10,info:false});});</script>
@endsection
