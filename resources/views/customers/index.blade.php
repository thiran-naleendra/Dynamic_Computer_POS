@extends('layouts.app')
@section('content')
@php
    $totalCustomers = $customers->count();
    $withEmail = $customers->filter(fn ($customer) => filled($customer->email))->count();
    $withTel = $customers->filter(fn ($customer) => filled($customer->tel))->count();
@endphp

<style>
    .customers-wrap { display: grid; gap: 1.5rem; }
    .customers-hero, .customers-panel, .summary-card {
        border: 1px solid rgba(24, 50, 77, 0.08);
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.92);
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.06);
    }
    .customers-hero {
        padding: 1.25rem 1.35rem;
        display: flex; justify-content: space-between; align-items: center; gap: 1rem;
        background: radial-gradient(circle at top right, rgba(13,110,253,.12), transparent 24%), linear-gradient(135deg, rgba(255,255,255,.96), rgba(248,251,255,.95));
    }
    .customers-hero h1 { margin: 0 0 .3rem; font-size: clamp(1.75rem, 2.8vw, 2.3rem); font-weight: 800; }
    .customers-hero p { margin: 0; color: var(--text-muted, #6c8098); max-width: 620px; }
    .customers-hero .btn { min-width: 150px; border-radius: 14px; font-weight: 700; }
    .summary-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; }
    .summary-card .card-body { padding: 1.05rem 1.15rem; }
    .summary-card.primary { background: linear-gradient(180deg, rgba(13,110,253,.08), rgba(255,255,255,.95)); }
    .summary-card.success { background: linear-gradient(180deg, rgba(25,135,84,.08), rgba(255,255,255,.95)); }
    .summary-card.info { background: linear-gradient(180deg, rgba(13,202,240,.12), rgba(255,255,255,.95)); }
    .summary-label { display:block; margin-bottom:.5rem; font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--text-muted,#6c8098); }
    .summary-value { display:block; font-size:1.9rem; line-height:1; font-weight:800; margin-bottom:.35rem; }
    .summary-note { margin:0; color:var(--text-muted,#6c8098); font-size:.92rem; }
    .customers-panel .card-body { padding: 1.15rem; }
    .panel-head { display:flex; justify-content:space-between; align-items:end; gap:1rem; margin-bottom:1rem; }
    .panel-head h2 { margin:0 0 .15rem; font-size:1.3rem; font-weight:800; }
    .panel-head p { margin:0; color:var(--text-muted,#6c8098); }
    .table-shell { border:1px solid rgba(24,50,77,.08); border-radius:16px; overflow:hidden; }
    .customers-table thead th { background: rgba(13,110,253,.06); border-bottom:0; color:var(--text-muted,#6c8098); font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; }
    .contact-chip { display:inline-flex; align-items:center; padding:.36rem .7rem; border-radius:999px; background:rgba(13,110,253,.08); font-size:.82rem; font-weight:700; }
    .action-group { display:inline-flex; gap:.45rem; flex-wrap:wrap; justify-content:flex-end; }
    .action-group .btn { border-radius:12px; font-weight:700; }
    html[data-theme="dark"] .customers-hero, html[data-theme="dark"] .customers-panel, html[data-theme="dark"] .summary-card, html[data-theme="dark"] .table-shell { background: var(--surface-strong, #162338) !important; border-color: var(--border-soft, rgba(255,255,255,.08)) !important; }
    @media (max-width: 991.98px) { .summary-grid { grid-template-columns: 1fr; } }
    @media (max-width: 767.98px) { .customers-hero, .panel-head { flex-direction: column; align-items: flex-start; } .customers-hero .btn { width:100%; } }
</style>

<div class="customers-wrap">
    <div class="customers-hero">
        <div>
            <h1>Customers</h1>
            <p>Maintain customer contact details so invoices, returns, and service records stay organized.</p>
        </div>
        <a href="{{ route('customers.create') }}" class="btn btn-success">Add Customer</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-0">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="summary-grid">
        <div class="card summary-card primary"><div class="card-body"><span class="summary-label">Total Customers</span><span class="summary-value">{{ $totalCustomers }}</span><p class="summary-note">Saved customer records.</p></div></div>
        <div class="card summary-card success"><div class="card-body"><span class="summary-label">With Phone</span><span class="summary-value">{{ $withTel }}</span><p class="summary-note">Customers with contact numbers.</p></div></div>
        <div class="card summary-card info"><div class="card-body"><span class="summary-label">With Email</span><span class="summary-value">{{ $withEmail }}</span><p class="summary-note">Customers with email addresses.</p></div></div>
    </div>

    <div class="card customers-panel">
        <div class="card-body">
            <div class="panel-head">
                <div>
                    <h2>Customer List</h2>
                    <p>Review customer details and keep records ready for sales and service work.</p>
                </div>
            </div>

            <div class="table-shell">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 customers-table" id="customersTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Tel</th>
                                <th>Address</th>
                                <th>Email</th>
                                <th class="text-end" style="width: 180px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                                <tr>
                                    <td><div class="fw-semibold">{{ $customer->name }}</div></td>
                                    <td>{!! $customer->tel ? '<span class="contact-chip">'.$customer->tel.'</span>' : '<span class="text-muted">-</span>' !!}</td>
                                    <td>{{ $customer->address ?: '-' }}</td>
                                    <td>{{ $customer->email ?: '-' }}</td>
                                    <td class="text-end">
                                        <div class="action-group">
                                            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                                            <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this customer?')">
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
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof DataTable === 'undefined') return;
    const table = document.querySelector('#customersTable');
    if (table) new DataTable(table, { pageLength: 10, searching: true, ordering: true, info: false, columnDefs: [{ orderable: false, searchable: false, targets: -1 }] });
});
</script>
@endsection
