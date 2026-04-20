@extends('layouts.app')
@section('content')

<div class="container">

  {{-- Header --}}
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body d-flex justify-content-between align-items-center">
      <div>
        <h4 class="fw-bold mb-0">Edit Service Note</h4>
        <small class="text-muted">
          <b>{{ $note->service_no }}</b> • {{ $note->service_date ?? '-' }}
        </small>
      </div>

      <div class="d-flex gap-2">
        <a href="{{ route('service-notes.index') }}" class="btn btn-outline-secondary">Back</a>
        <a href="{{ route('service-notes.show', $note) }}" class="btn btn-outline-primary">View</a>
        <a target="_blank" href="{{ route('service-notes.print', $note) }}" class="btn btn-primary">Print</a>
      </div>
    </div>
  </div>

  {{-- Errors --}}
  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <ul class="mb-0">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- Form --}}
  <div class="card border-0 shadow-sm">
    <div class="card-body">

      <form method="POST" action="{{ route('service-notes.update', $note) }}">
        @csrf
        @method('PUT')

        <div class="row g-3">

          {{-- Service info --}}
          <div class="col-md-4">
            <label class="form-label fw-semibold">Service Date</label>
            <input type="date" name="service_date"
                   class="form-control"
                   value="{{ old('service_date', $note->service_date) }}">
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Invoice No (optional)</label>
            <input type="text" name="invoice_no"
                   class="form-control"
                   value="{{ old('invoice_no', $note->invoice_no) }}"
                   placeholder="Invoice No">
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Service No</label>
            <input type="text" class="form-control"
                   value="{{ $note->service_no }}" readonly>
            <small class="text-muted">Service number cannot be changed</small>
          </div>

          <hr class="my-1">

          {{-- Customer --}}
          <div class="col-md-4">
            <label class="form-label fw-semibold">Customer Name</label>
            <input type="text" name="customer_name"
                   class="form-control"
                   value="{{ old('customer_name', $note->customer_name) }}"
                   placeholder="Customer Name">
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Telephone</label>
            <input type="text" name="customer_tel"
                   class="form-control"
                   value="{{ old('customer_tel', $note->customer_tel) }}"
                   placeholder="07XXXXXXXX">
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Address</label>
            <input type="text" name="customer_address"
                   class="form-control"
                   value="{{ old('customer_address', $note->customer_address) }}"
                   placeholder="Address">
          </div>

          <hr class="my-1">

          {{-- Item --}}
          <div class="col-md-3">
            <label class="form-label fw-semibold">Item</label>
            <input type="text" name="item"
                   class="form-control"
                   value="{{ old('item', $note->item) }}"
                   placeholder="Laptop / PC / Printer">
          </div>

          <div class="col-md-3">
            <label class="form-label fw-semibold">Serial No</label>
            <input type="text" name="serial_no"
                   class="form-control"
                   value="{{ old('serial_no', $note->serial_no) }}"
                   placeholder="Serial No">
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">Details</label>
            <input type="text" name="details"
                   class="form-control"
                   value="{{ old('details', $note->details) }}"
                   placeholder="Extra details">
          </div>

          {{-- Complains --}}
          <div class="col-md-12">
            <label class="form-label fw-semibold">Customer Complains</label>
            <textarea name="customer_complains" class="form-control" rows="4"
                      placeholder="Describe problem...">{{ old('customer_complains', $note->customer_complains) }}</textarea>
          </div>

          <hr class="my-1">

          {{-- Good Return Note --}}
          <div class="col-12">
            <h6 class="fw-bold mb-0">Good Return Note</h6>
            <small class="text-muted">Fill when returning the item (optional)</small>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">Received Service Item</label>
            <input type="text" name="received_service_item"
                   class="form-control"
                   value="{{ old('received_service_item', $note->received_service_item) }}"
                   placeholder="Received item name">
          </div>

          <div class="col-md-3">
            <label class="form-label fw-semibold">Customer Name</label>
            <input type="text" name="grn_customer_name"
                   class="form-control"
                   value="{{ old('grn_customer_name', $note->grn_customer_name) }}"
                   placeholder="Customer name">
          </div>

          <div class="col-md-3">
            <label class="form-label fw-semibold">Return Date</label>
            <input type="date" name="grn_date"
                   class="form-control"
                   value="{{ old('grn_date', $note->grn_date) }}">
          </div>

        </div>

        <div class="d-flex gap-2 mt-4">
          <button class="btn btn-warning btn-lg">Update Service Note</button>
          <a href="{{ route('service-notes.show', $note) }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
        </div>

      </form>

    </div>
  </div>

</div>
@endsection
