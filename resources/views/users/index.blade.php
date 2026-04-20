@extends('layouts.app')
@section('content')
@php
    $adminCount = $users->filter(fn ($user) => $user->isAdmin())->count();
    $cashierCount = $users->filter(fn ($user) => $user->isCashier())->count();
@endphp

<style>
    .users-wrap { display:grid; gap:1.5rem; }
    .users-hero, .users-panel, .summary-card { border:1px solid rgba(24,50,77,.08); border-radius:20px; background:rgba(255,255,255,.92); box-shadow:0 14px 34px rgba(15,23,42,.06); }
    .users-hero { padding:1.25rem 1.35rem; display:flex; justify-content:space-between; align-items:center; gap:1rem; background: radial-gradient(circle at top right, rgba(13,110,253,.12), transparent 24%), linear-gradient(135deg, rgba(255,255,255,.96), rgba(248,251,255,.95)); }
    .users-hero h1 { margin:0 0 .3rem; font-size:clamp(1.75rem,2.8vw,2.3rem); font-weight:800; }
    .users-hero p { margin:0; color:var(--text-muted,#6c8098); max-width:620px; }
    .users-hero .btn { min-width:150px; border-radius:14px; font-weight:700; }
    .summary-grid { display:grid; grid-template-columns:repeat(3, minmax(0,1fr)); gap:1rem; }
    .summary-card .card-body { padding:1.05rem 1.15rem; }
    .summary-label { display:block; margin-bottom:.5rem; font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--text-muted,#6c8098); }
    .summary-value { display:block; font-size:1.9rem; line-height:1; font-weight:800; margin-bottom:.35rem; }
    .summary-note { margin:0; color:var(--text-muted,#6c8098); font-size:.92rem; }
    .users-panel .card-body { padding:1.15rem; }
    .panel-head { margin-bottom:1rem; }
    .panel-head h2 { margin:0 0 .15rem; font-size:1.3rem; font-weight:800; }
    .panel-head p { margin:0; color:var(--text-muted,#6c8098); }
    .table-shell { border:1px solid rgba(24,50,77,.08); border-radius:16px; overflow:hidden; }
    .users-table thead th { background: rgba(13,110,253,.06); border-bottom:0; color:var(--text-muted,#6c8098); font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; }
    .role-pill { display:inline-flex; align-items:center; justify-content:center; min-width:90px; padding:.38rem .72rem; border-radius:999px; font-size:.82rem; font-weight:700; }
    .role-pill.admin { background:rgba(13,110,253,.12); color:#0b5ed7; }
    .role-pill.cashier { background:rgba(25,135,84,.14); color:#146c43; }
    .action-group { display:inline-flex; gap:.45rem; flex-wrap:wrap; justify-content:flex-end; }
    .action-group .btn { border-radius:12px; font-weight:700; }
    html[data-theme="dark"] .users-hero, html[data-theme="dark"] .users-panel, html[data-theme="dark"] .summary-card, html[data-theme="dark"] .table-shell { background:var(--surface-strong,#162338)!important; border-color:var(--border-soft,rgba(255,255,255,.08))!important; }
    @media (max-width: 991.98px) { .summary-grid { grid-template-columns:1fr; } }
    @media (max-width: 767.98px) { .users-hero { flex-direction:column; align-items:flex-start; } .users-hero .btn { width:100%; } }
</style>

<div class="users-wrap">
    <div class="users-hero">
        <div>
            <h1>Users & Roles</h1>
            <p>Admins can create users, assign roles, and control whether a user has full system access or cashier-only billing access.</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary">Add User</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-0">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="summary-grid">
        <div class="card summary-card"><div class="card-body"><span class="summary-label">Total Users</span><span class="summary-value">{{ $users->count() }}</span><p class="summary-note">All active accounts in the system.</p></div></div>
        <div class="card summary-card"><div class="card-body"><span class="summary-label">Admins</span><span class="summary-value">{{ $adminCount }}</span><p class="summary-note">Full-access management users.</p></div></div>
        <div class="card summary-card"><div class="card-body"><span class="summary-label">Cashiers</span><span class="summary-value">{{ $cashierCount }}</span><p class="summary-note">Billing-only users.</p></div></div>
    </div>

    <div class="card users-panel">
        <div class="card-body">
            <div class="panel-head">
                <h2>User List</h2>
                <p>Assign and update roles as your staff changes.</p>
            </div>

            <div class="table-shell">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 users-table" id="usersTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th class="text-center">Role</th>
                                <th class="text-end" style="width: 190px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td class="fw-semibold">{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td class="text-center">
                                        <span class="role-pill {{ $user->isAdmin() ? 'admin' : 'cashier' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="action-group">
                                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                                            @if(auth()->id() !== $user->id)
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this user?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                                </form>
                                            @endif
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
    const table = document.querySelector('#usersTable');
    if (table) new DataTable(table, { pageLength: 10, searching: true, ordering: true, info: false, columnDefs: [{ orderable: false, searchable: false, targets: -1 }] });
});
</script>
@endsection
