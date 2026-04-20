@extends('layouts.app')
@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Return Note: {{ $return->return_no }}</h4>
    <div>
      <a class="btn btn-secondary" target="_blank" href="{{ route('returns.print',$return) }}">Print</a>
      <a class="btn btn-outline-dark" href="{{ route('returns.index') }}">Back</a>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <div><strong>Date:</strong> {{ $return->return_date }}</div>
      <div><strong>Customer:</strong> {{ $return->customer_name ?? '-' }}</div>
      <div><strong>Tel:</strong> {{ $return->customer_tel ?? '-' }}</div>
      <div><strong>Reason:</strong> {{ $return->reason ?? '-' }}</div>
    </div>
  </div>

  <div class="card">
    <div class="card-body table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>Item</th>
            <th>Serial</th>
            <th class="text-center">Qty</th>
          </tr>
        </thead>
        <tbody>
          @foreach($return->items as $it)
          <tr>
            <td>{{ $it->item_name }}</td>
            <td>{{ $it->serial_no ?? '-' }}</td>
            <td class="text-center">{{ $it->qty }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
