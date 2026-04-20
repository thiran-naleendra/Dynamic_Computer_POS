@extends('layouts.app')
@section('content')

<div class="container">

  {{-- Header --}}
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body d-flex justify-content-between align-items-center">
      <div>
        <h4 class="fw-bold mb-0">Service Note</h4>
        <small class="text-muted">
          <b>{{ $note->service_no }}</b> • {{ $note->service_date ?? '-' }}
        </small>
      </div>

      <div class="d-flex gap-2">
        <a href="{{ route('service-notes.index') }}" class="btn btn-outline-secondary">Back</a>
        <a href="{{ route('service-notes.edit', $note) }}" class="btn btn-warning">Edit</a>
        <a target="_blank" href="{{ route('service-notes.print', $note) }}" class="btn btn-primary">Print</a>
      </div>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
      {{ session('success') }}
      <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- Details --}}
  <div class="row g-3">

    <div class="col-md-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <h6 class="fw-bold mb-3">Customer Details</h6>

          <div class="mb-2"><b>Name:</b> {{ $note->customer_name ?? '-' }}</div>
          <div class="mb-2"><b>Address:</b> {{ $note->customer_address ?? '-' }}</div>
          <div class="mb-2"><b>Tel:</b> {{ $note->customer_tel ?? '-' }}</div>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <h6 class="fw-bold mb-3">Service Info</h6>

          <div class="mb-2"><b>Service No:</b> {{ $note->service_no }}</div>
          <div class="mb-2"><b>Service Date:</b> {{ $note->service_date ?? '-' }}</div>
          <div class="mb-2"><b>Invoice No:</b> {{ $note->invoice_no ?? '-' }}</div>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h6 class="fw-bold mb-3">Item Details</h6>

          <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Item</th>
                  <th>Serial No</th>
                  <th>Details</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>{{ $note->item ?? '-' }}</td>
                  <td>{{ $note->serial_no ?? '-' }}</td>
                  <td>{{ $note->details ?? '-' }}</td>
                </tr>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h6 class="fw-bold mb-2">Customer Complains</h6>
          <div class="p-3 bg-light rounded">
            {!! nl2br(e($note->customer_complains ?? '-')) !!}
          </div>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h6 class="fw-bold mb-3">Good Return Note</h6>

          <div class="row g-3">
            <div class="col-md-4">
              <div class="p-3 bg-light rounded">
                <b>Received Item</b><br>
                {{ $note->received_service_item ?? '-' }}
              </div>
            </div>

            <div class="col-md-4">
              <div class="p-3 bg-light rounded">
                <b>Customer Name</b><br>
                {{ $note->grn_customer_name ?? '-' }}
              </div>
            </div>

            <div class="col-md-4">
              <div class="p-3 bg-light rounded">
                <b>Date</b><br>
                {{ $note->grn_date ?? '-' }}
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

  </div>

</div>
@endsection
