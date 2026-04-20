@extends('layouts.app')
@section('content')
<style>
    .reports-wrap { display:grid; gap:1.5rem; }
    .reports-hero { padding:1.25rem 1.35rem; border:1px solid rgba(24,50,77,.08); border-radius:20px; background: radial-gradient(circle at top right, rgba(13,110,253,.12), transparent 24%), linear-gradient(135deg, rgba(255,255,255,.96), rgba(248,251,255,.95)); box-shadow:0 14px 34px rgba(15,23,42,.06); display:flex; justify-content:space-between; gap:1rem; align-items:center; }
    .reports-hero h1 { margin:0 0 .3rem; font-size:clamp(1.75rem,2.8vw,2.3rem); font-weight:800; }
    .reports-hero p { margin:0; color:var(--text-muted,#6c8098); max-width:650px; }
    .reports-date { padding:.8rem 1rem; border-radius:16px; background:rgba(13,110,253,.08); font-weight:700; }
    .report-grid { display:grid; grid-template-columns:repeat(3, minmax(0,1fr)); gap:1rem; }
    .report-card { border:1px solid rgba(24,50,77,.08); border-radius:18px; background:rgba(255,255,255,.92); box-shadow:0 12px 30px rgba(15,23,42,.06); transition:transform .18s ease, box-shadow .18s ease; }
    .report-card:hover { transform: translateY(-2px); box-shadow:0 16px 32px rgba(15,23,42,.09); }
    .report-card .card-body { padding:1.15rem; }
    .report-card h5 { margin-bottom:.3rem; font-weight:800; color:var(--text-main,#18324d); }
    .report-card p { margin:0 0 1rem; color:var(--text-muted,#6c8098); }
    .report-tag { display:inline-flex; align-items:center; padding:.38rem .65rem; border-radius:999px; background:rgba(13,110,253,.08); font-size:.82rem; font-weight:700; }
    .report-card.low-stock .report-tag { background:rgba(220,53,69,.12); color:#b02a37; }
    html[data-theme="dark"] .reports-hero, html[data-theme="dark"] .report-card { background:var(--surface-strong,#162338) !important; border-color:var(--border-soft,rgba(255,255,255,.08)) !important; }
    @media (max-width: 991.98px) { .report-grid { grid-template-columns:repeat(2, minmax(0,1fr)); } }
    @media (max-width: 767.98px) { .reports-hero { flex-direction:column; align-items:flex-start; } .report-grid { grid-template-columns:1fr; } }
</style>
<div class="reports-wrap">
    <div class="reports-hero">
        <div><h1>Reports</h1><p>Open sales, profit, stock, product movement, return, and low-stock reports from one place.</p></div>
        <div class="reports-date">{{ now()->format('Y-m-d') }}</div>
    </div>
    <div class="report-grid">
        <a href="{{ route('reports.sales') }}" class="text-decoration-none"><div class="card report-card h-100"><div class="card-body"><h5>Sales Report</h5><p>Track invoice totals between dates and review sales history.</p><span class="report-tag">Sales</span></div></div></a>
        <a href="{{ route('reports.profit') }}" class="text-decoration-none"><div class="card report-card h-100"><div class="card-body"><h5>Profit Report</h5><p>Compare selling price and dealer price to measure profit.</p><span class="report-tag">Profit</span></div></div></a>
        <a href="{{ route('reports.product_sales') }}" class="text-decoration-none"><div class="card report-card h-100"><div class="card-body"><h5>Product Sales</h5><p>See the best-selling products by quantity and amount.</p><span class="report-tag">Products</span></div></div></a>
        <a href="{{ route('reports.stock') }}" class="text-decoration-none"><div class="card report-card h-100"><div class="card-body"><h5>Stock Report</h5><p>Review live inventory quantity and stock valuation.</p><span class="report-tag">Inventory</span></div></div></a>
        <a href="{{ route('reports.returns') }}" class="text-decoration-none"><div class="card report-card h-100"><div class="card-body"><h5>Return Report</h5><p>Monitor returned quantities and return values over time.</p><span class="report-tag">Returns</span></div></div></a>
        <a href="{{ route('reports.low_stock') }}" class="text-decoration-none"><div class="card report-card low-stock h-100"><div class="card-body"><h5>Low Stock</h5><p>Quickly identify products that are low or already out of stock.</p><span class="report-tag">Attention</span></div></div></a>
    </div>
</div>
@endsection
