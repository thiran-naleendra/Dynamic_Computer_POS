@extends('layouts.app')
@section('content')
<style>
    .form-shell { max-width: 900px; margin: 0 auto; display: grid; gap: 1.25rem; }
    .form-hero, .form-panel { border: 1px solid rgba(24,50,77,.08); border-radius: 20px; background: rgba(255,255,255,.92); box-shadow: 0 14px 34px rgba(15,23,42,.06); }
    .form-hero { padding: 1.2rem 1.3rem; background: radial-gradient(circle at top right, rgba(25,135,84,.12), transparent 24%), linear-gradient(135deg, rgba(255,255,255,.96), rgba(247,252,249,.95)); }
    .form-hero h1 { margin: 0 0 .3rem; font-size: clamp(1.7rem, 2.8vw, 2.2rem); font-weight: 800; }
    .form-hero p { margin: 0; color: var(--text-muted,#6c8098); }
    .form-panel .card-body { padding: 1.2rem; }
    .section-title { margin-bottom: 1rem; }
    .section-title h2 { margin: 0 0 .2rem; font-size: 1.2rem; font-weight: 800; }
    .section-title p { margin: 0; color: var(--text-muted,#6c8098); }
    .form-actions { display:flex; gap:.75rem; flex-wrap:wrap; padding-top:1rem; border-top:1px solid rgba(24,50,77,.08); }
    .form-actions .btn { min-width: 140px; border-radius: 12px; font-weight: 700; }
    html[data-theme="dark"] .form-hero, html[data-theme="dark"] .form-panel { background: var(--surface-strong,#162338) !important; border-color: var(--border-soft,rgba(255,255,255,.08)) !important; }
</style>

<div class="form-shell">
    <div class="form-hero">
        <h1>Add Customer</h1>
        <p>Create a customer record so you can reuse their details in invoices and service workflows.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-0">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card form-panel">
        <div class="card-body">
            <div class="section-title">
                <h2>Customer Details</h2>
                <p>Keep the essential contact information complete and easy to find later.</p>
            </div>

            <form method="POST" action="{{ route('customers.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Telephone</label>
                        <input type="text" name="tel" class="form-control" value="{{ old('tel') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Address</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                    </div>
                </div>

                <div class="form-actions mt-4">
                    <button class="btn btn-success" type="submit">Save Customer</button>
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Back</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
