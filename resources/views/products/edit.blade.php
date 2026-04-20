@extends('layouts.app')
@section('content')
<style>
    .form-shell { max-width: 980px; margin: 0 auto; display: grid; gap: 1.25rem; }
    .form-hero, .form-panel { border: 1px solid rgba(24,50,77,.08); border-radius: 20px; background: rgba(255,255,255,.92); box-shadow: 0 14px 34px rgba(15,23,42,.06); }
    .form-hero { padding: 1.2rem 1.3rem; background: radial-gradient(circle at top right, rgba(255,193,7,.14), transparent 24%), linear-gradient(135deg, rgba(255,255,255,.96), rgba(255,252,245,.95)); }
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
        <h1>Edit Product</h1>
        <p>Adjust product pricing and warranty information while keeping existing stock records intact.</p>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-0"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <div class="card form-panel">
        <div class="card-body">
            <div class="section-title"><h2>Product Details</h2><p>Update the saved product information below.</p></div>
            <form method="POST" action="{{ route('products.update', $product) }}">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Product Name</label>
                        <input name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Dealer Price</label>
                        <input name="dealer_price" type="number" step="0.01" class="form-control" value="{{ old('dealer_price', $product->dealer_price) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Sell Price</label>
                        <input name="sell_price" type="number" step="0.01" class="form-control" value="{{ old('sell_price', $product->sell_price) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Warranty Period</label>
                        <select name="warranty_days" class="form-select" required>
                            @foreach($warrantyOptions as $days => $label)
                                <option value="{{ $days }}" @selected((int) old('warranty_days', (int) ($product->warranty_days ?? 0)) === (int) $days)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1">Saved in days inside the database.</small>
                    </div>
                </div>
                <div class="form-actions mt-4">
                    <button class="btn btn-primary" type="submit">Update Product</button>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Back</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
