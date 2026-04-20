@extends('layouts.app')
@section('content')
    <div class="container">
        <h4 class="mb-3">New Return Note</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('returns.store') }}">
            @csrf

            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Return Date</label>
                            <input type="date" name="return_date" class="form-control"
                                value="{{ old('return_date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label">Customer Name</label>
                            <input name="customer_name" class="form-control" value="{{ old('customer_name') }}">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label">Customer Tel</label>
                            <input name="customer_tel" class="form-control" value="{{ old('customer_tel') }}">
                        </div>

                        <div class="col-12 mt-2">
                            <label class="form-label">Reason / Note</label>
                            <textarea name="reason" class="form-control" rows="2">{{ old('reason') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-bordered align-middle" id="itemsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width:240px;">Item</th>
                                <th style="min-width:160px;">Serial No</th>
                                <th width="120">Qty</th>
                                <th width="160">Sell Price</th>
                                <th width="160">Dealer Price</th>
                                <th width="160">Line Total</th>
                                <th width="80"></th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    <select class="form-select product-select" name="items[0][product_id]" required>
                                        <option value="">-- Select --</option>
                                        @foreach ($products as $p)
                                            <option value="{{ $p->id }}" data-sell="{{ $p->sell_price }}"
                                                data-dealer="{{ $p->dealer_price }}">
                                                {{ $p->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td>
                                    <input class="form-control" name="items[0][serial_no]" placeholder="Serial (optional)">
                                </td>

                                <td>
                                    <input type="number" class="form-control qty" name="items[0][qty]" value="1"
                                        min="1" required>
                                </td>

                                <td>
                                    <input type="number" step="0.01" class="form-control sell"
                                        name="items[0][sell_price]" value="0" required>
                                </td>

                                <td>
                                    <input type="number" step="0.01" class="form-control dealer"
                                        name="items[0][dealer_price]" value="0" required>
                                </td>

                                <td>
                                    <input type="text" class="form-control line-total" value="0.00" readonly>
                                </td>

                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
                                </td>
                            </tr>

                        </tbody>
                    </table>

                    <button type="button" class="btn btn-outline-primary" id="addRow">+ Add Item</button>

                    <hr>
                    <button class="btn btn-success">Save Return & Add Stock</button>
                    <a href="{{ route('returns.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </div>
        </form>
    </div>

    <script>
(function(){
  let rowIndex = 1;

  function calcRow(row){
    const qty = parseFloat(row.querySelector('.qty').value || 0);
    const sell = parseFloat(row.querySelector('.sell').value || 0);
    const total = qty * sell;
    row.querySelector('.line-total').value = total.toFixed(2);
  }

  function calcGrand(){
    let sum = 0;
    document.querySelectorAll('.line-total').forEach(x => sum += parseFloat(x.value || 0));
    const el = document.getElementById('grandTotal');
    if(el) el.innerText = sum.toFixed(2);
  }

  function bindRow(row){
    const productSelect = row.querySelector('.product-select');

    productSelect.addEventListener('change', function(){
      const opt = this.options[this.selectedIndex];
      const sell = opt.getAttribute('data-sell') || 0;
      const dealer = opt.getAttribute('data-dealer') || 0;

      row.querySelector('.sell').value = parseFloat(sell).toFixed(2);
      row.querySelector('.dealer').value = parseFloat(dealer).toFixed(2);

      calcRow(row); calcGrand();
    });

    row.querySelector('.qty').addEventListener('input', ()=>{ calcRow(row); calcGrand(); });
    row.querySelector('.sell').addEventListener('input', ()=>{ calcRow(row); calcGrand(); });

    row.querySelector('.remove-row').addEventListener('click', function(){
      const tbody = document.querySelector('#itemsTable tbody');
      if(tbody.querySelectorAll('tr').length > 1){
        row.remove();
        calcGrand();
      }
    });

    calcRow(row); calcGrand();
  }

  document.querySelectorAll('#itemsTable tbody tr').forEach(bindRow);

  document.getElementById('addRow').addEventListener('click', function(){
    const tbody = document.querySelector('#itemsTable tbody');
    const tr = document.createElement('tr');

    tr.innerHTML = `
      <td>
        <select class="form-select product-select" name="items[${rowIndex}][product_id]" required>
          <option value="">-- Select --</option>
          @foreach($products as $p)
            <option value="{{ $p->id }}"
                    data-sell="{{ $p->sell_price }}"
                    data-dealer="{{ $p->dealer_price }}">
              {{ $p->name }}
            </option>
          @endforeach
        </select>
      </td>

      <td><input class="form-control" name="items[${rowIndex}][serial_no]" placeholder="Serial (optional)"></td>

      <td><input type="number" class="form-control qty" name="items[${rowIndex}][qty]" value="1" min="1" required></td>

      <td><input type="number" step="0.01" class="form-control sell" name="items[${rowIndex}][sell_price]" value="0" required></td>

      <td><input type="number" step="0.01" class="form-control dealer" name="items[${rowIndex}][dealer_price]" value="0" required></td>

      <td><input type="text" class="form-control line-total" value="0.00" readonly></td>

      <td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>
    `;

    tbody.appendChild(tr);
    bindRow(tr);
    rowIndex++;
  });
})();
</script>

@endsection
