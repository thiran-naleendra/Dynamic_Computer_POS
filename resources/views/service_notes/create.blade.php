@extends('layouts.app')
@section('content')

<div class="container">
  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h4 class="fw-bold mb-0">New Service Note</h4>
          <small class="text-muted">Service No will be: <b>{{ $serviceNo }}</b></small>
        </div>
        <a href="{{ route('service-notes.index') }}" class="btn btn-outline-secondary">Back</a>
      </div>

      <form method="POST" action="{{ route('service-notes.store') }}">
        @csrf

        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-semibold">Service Date</label>
            <input type="date" name="service_date" class="form-control" value="{{ old('service_date', date('Y-m-d')) }}">
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Customer Name</label>
            <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name') }}">
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Tel</label>
            <input type="text" name="customer_tel" class="form-control" value="{{ old('customer_tel') }}">
          </div>

          <div class="col-md-12">
            <label class="form-label fw-semibold">Address</label>
            <input type="text" name="customer_address" class="form-control" value="{{ old('customer_address') }}">
          </div>

          <hr class="my-2">

          <div class="col-md-3">
            <label class="form-label fw-semibold">Item</label>
            <input type="text" name="item" class="form-control" value="{{ old('item') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Serial No</label>
            <input type="text" name="serial_no" class="form-control" value="{{ old('serial_no') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Invoice No</label>
            <input type="text" name="invoice_no" class="form-control" value="{{ old('invoice_no') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Details</label>
            <input type="text" name="details" class="form-control" value="{{ old('details') }}">
          </div>

          <div class="col-md-12">
            <label class="form-label fw-semibold">Customer Complains</label>
            <textarea name="customer_complains" class="form-control" rows="4">{{ old('customer_complains') }}</textarea>
          </div>

          <hr class="my-2">

          <h6 class="fw-bold mt-2">Good Return Note</h6>

          <div class="col-md-6">
            <label class="form-label fw-semibold">Received Service Item</label>
            <input type="text" name="received_service_item" class="form-control" value="{{ old('received_service_item') }}">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Customer Name</label>
            <input type="text" name="grn_customer_name" class="form-control" value="{{ old('grn_customer_name') }}">
          </div>
          <div class="col-md-2">
            <label class="form-label fw-semibold">Date</label>
            <input type="date" name="grn_date" class="form-control" value="{{ old('grn_date') }}">
          </div>

        </div>

        <div class="mt-4">
          <button class="btn btn-primary btn-lg">Save Service Note</button>
        </div>

      </form>
    </div>
  </div>
</div>

@endsection
