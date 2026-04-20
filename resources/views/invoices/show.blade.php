@extends('layouts.app')
@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Invoice: {{ $invoice->invoice_no }}</h4>
    <div class="d-flex gap-2">
      <a class="btn btn-secondary" target="_blank" href="{{ route('invoices.print',$invoice) }}">Print</a>
      <a class="btn btn-outline-dark" href="{{ route('invoices.index') }}">Back</a>
    </div>
  </div>

  @php
    // days => label (same mapping you use everywhere)
    $warrantyMap = [
      0    => 'No Warranty',
      7    => '1 Week',
      14   => '2 Weeks',
      30   => '1 Month',
      90   => '3 Months',
      180  => '6 Months',
      365  => '1 Year',
      730  => '2 Years',
      1095 => '3 Years',
      1825 => '5 Years',
    ];
  @endphp

  <div class="card mb-3">
    <div class="card-body">
      <div><strong>Shop:</strong> {{ $invoice->shop_name }}</div>
      <div><strong>Date:</strong> {{ $invoice->invoice_date }}</div>
      <div><strong>Customer:</strong> {{ $invoice->customer_name ?? '-' }}</div>
    </div>
  </div>

  <div class="card">
    <div class="card-body table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>Item</th>
            <th>Serial</th>
            <th>Warranty</th>
            <th class="text-center">Qty</th>
            <th class="text-end">Unit Price</th>
            <th class="text-end">Total</th>
          </tr>
        </thead>

        <tbody>
          @foreach($invoice->items as $it)
            @php
              // warranty_days column should exist in invoice_items table
              $days = (int)($it->warranty_days ?? 0);
              $label = $warrantyMap[$days] ?? ($days > 0 ? $days.' Days' : 'No Warranty');

              $endDate = null;
              if($days > 0){
                try{
                  $endDate = \Carbon\Carbon::parse($invoice->invoice_date)->addDays($days)->format('d/m/Y');
                } catch (\Exception $e){
                  $endDate = null;
                }
              }
            @endphp

            <tr>
              <td>{{ $it->item_name }}</td>
              <td>{{ $it->serial_no ?? '-' }}</td>

              <td>
                @if($days <= 0)
                  <span class="badge bg-secondary">No Warranty</span>
                @else
                  <span class="badge bg-info text-dark">{{ $label }}</span>
                  @if($endDate)
                    <div class="small text-muted">End: {{ $endDate }}</div>
                  @endif
                @endif
              </td>

              <td class="text-center">{{ $it->qty }}</td>
              <td class="text-end">{{ number_format($it->unit_price,2) }}</td>
              <td class="text-end">{{ number_format($it->line_total,2) }}</td>
            </tr>
          @endforeach
        </tbody>

        <tfoot>
          <tr>
            <th colspan="5" class="text-end">Grand Total</th>
            <th class="text-end">{{ number_format($invoice->grand_total,2) }}</th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
@endsection
