@extends('layouts.app')
@section('content')
@php
    $totalNotes = $notes->count();
    $todayNotes = $notes->filter(fn ($note) => $note->service_date === now()->toDateString())->count();
@endphp
<style>
    .service-wrap { display:grid; gap:1.5rem; }
    .service-hero, .service-panel, .summary-card { border:1px solid rgba(24,50,77,.08); border-radius:20px; background:rgba(255,255,255,.92); box-shadow:0 14px 34px rgba(15,23,42,.06); }
    .service-hero { padding:1.25rem 1.35rem; display:flex; justify-content:space-between; align-items:center; gap:1rem; background: radial-gradient(circle at top right, rgba(253,126,20,.12), transparent 24%), linear-gradient(135deg, rgba(255,255,255,.96), rgba(255,251,245,.95)); }
    .service-hero h1 { margin:0 0 .3rem; font-size:clamp(1.75rem,2.8vw,2.3rem); font-weight:800; }
    .service-hero p { margin:0; color:var(--text-muted,#6c8098); max-width:620px; }
    .service-hero .btn { min-width:160px; border-radius:14px; font-weight:700; }
    .summary-grid { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:1rem; }
    .summary-card .card-body { padding:1.05rem 1.15rem; }
    .summary-card.primary { background: linear-gradient(180deg, rgba(253,126,20,.1), rgba(255,255,255,.95)); }
    .summary-card.info { background: linear-gradient(180deg, rgba(13,202,240,.12), rgba(255,255,255,.95)); }
    .summary-label { display:block; margin-bottom:.5rem; font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--text-muted,#6c8098); }
    .summary-value { display:block; font-size:1.9rem; line-height:1; font-weight:800; margin-bottom:.35rem; }
    .summary-note { margin:0; color:var(--text-muted,#6c8098); font-size:.92rem; }
    .service-panel .card-body { padding:1.15rem; }
    .panel-head { display:flex; justify-content:space-between; align-items:end; gap:1rem; margin-bottom:1rem; }
    .panel-head h2 { margin:0 0 .15rem; font-size:1.3rem; font-weight:800; }
    .panel-head p { margin:0; color:var(--text-muted,#6c8098); }
    .table-shell { border:1px solid rgba(24,50,77,.08); border-radius:16px; overflow:hidden; }
    .service-table thead th { background: rgba(253,126,20,.08); border-bottom:0; color:var(--text-muted,#6c8098); font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; }
    .action-group { display:inline-flex; gap:.45rem; flex-wrap:wrap; justify-content:flex-end; }
    .action-group .btn { border-radius:12px; font-weight:700; }
    html[data-theme="dark"] .service-hero, html[data-theme="dark"] .service-panel, html[data-theme="dark"] .summary-card, html[data-theme="dark"] .table-shell { background: var(--surface-strong,#162338) !important; border-color: var(--border-soft,rgba(255,255,255,.08)) !important; }
    @media (max-width: 767.98px) { .service-hero, .panel-head { flex-direction:column; align-items:flex-start; } .summary-grid { grid-template-columns:1fr; } .service-hero .btn { width:100%; } .action-group { justify-content:flex-start; } }
</style>
<div class="service-wrap">
    <div class="service-hero">
        <div><h1>Service Notes</h1><p>Track repair jobs, customer complaints, and service return documents in one organized list.</p></div>
        <a href="{{ route('service-notes.create') }}" class="btn btn-primary">New Service Note</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-0">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    <div class="summary-grid">
        <div class="card summary-card primary"><div class="card-body"><span class="summary-label">Total Notes</span><span class="summary-value">{{ $totalNotes }}</span><p class="summary-note">Service notes in the system.</p></div></div>
        <div class="card summary-card info"><div class="card-body"><span class="summary-label">Today</span><span class="summary-value">{{ $todayNotes }}</span><p class="summary-note">Notes created for today.</p></div></div>
    </div>
    <div class="card service-panel">
        <div class="card-body">
            <div class="panel-head"><div><h2>Service Note List</h2><p>Open, print, edit, or remove service notes from one table.</p></div></div>
            <div class="table-shell"><div class="table-responsive">
                <table class="table table-hover align-middle mb-0 service-table" id="snTable">
                    <thead><tr><th>Service No</th><th>Date</th><th>Customer</th><th>Item</th><th class="text-end" style="width: 250px;">Actions</th></tr></thead>
                    <tbody>
                        @foreach($notes as $note)
                        <tr>
                            <td class="fw-semibold">{{ $note->service_no }}</td>
                            <td>{{ $note->service_date ?: '-' }}</td>
                            <td>{{ $note->customer_name ?? '-' }}</td>
                            <td>{{ $note->item ?? '-' }}</td>
                            <td class="text-end">
                                <div class="action-group">
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('service-notes.show', $note) }}">View</a>
                                    <a class="btn btn-sm btn-outline-secondary" target="_blank" href="{{ route('service-notes.print', $note) }}">Print</a>
                                    <a class="btn btn-sm btn-outline-warning" href="{{ route('service-notes.edit', $note) }}">Edit</a>
                                    <form class="d-inline" method="POST" action="{{ route('service-notes.destroy', $note) }}" onsubmit="return confirm('Delete this note?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div></div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof DataTable === 'undefined') return;
    const table = document.querySelector('#snTable');
    if (table) new DataTable(table, { pageLength: 10, ordering: true, info: false, columnDefs: [{ orderable: false, searchable: false, targets: -1 }] });
});
</script>
@endsection
