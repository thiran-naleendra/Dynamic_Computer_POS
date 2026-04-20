@extends('layouts.app')
@section('content')

    {{-- ✅ Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #ced4da !important;
            border-radius: .375rem !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
    </style>

    <div class="container">
        <h4 class="mb-3">Edit Invoice</h4>

        {{-- ✅ Stock / custom error popup --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- ✅ Validation errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('invoices.update', $invoice) }}">
            @csrf
            @method('PUT')

            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Invoice Date</label>
                            <input type="date" name="invoice_date" class="form-control"
                                value="{{ old('invoice_date', \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d')) }}"
                                required>
                        </div>

                        <div class="col-md-8 mb-2">
                            <label class="form-label">Customer Name (optional)</label>
                            <input type="text" name="customer_name" class="form-control"
                                value="{{ old('customer_name', $invoice->customer_name) }}">
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
                                <th style="min-width:160px;">Serial No (optional)</th>
                                <th style="min-width:170px;">Warranty</th>
                                <th width="120">Qty</th>
                                <th width="160">Unit Price</th>
                                <th width="160">Line Total</th>
                                <th width="80"></th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $oldItems = old('items');
                                $formItems =
                                    is_array($oldItems) && count($oldItems)
                                        ? $oldItems
                                        : $invoice->items
                                            ->map(function ($item) {
                                                return [
                                                    'product_id' => $item->product_id,
                                                    'serial_no' => $item->serial_no,
                                                    'warranty_days' => $item->warranty_days ?? 0,
                                                    'qty' => $item->qty,
                                                    'unit_price' => $item->unit_price,
                                                ];
                                            })
                                            ->toArray();

                                if (empty($formItems)) {
                                    $formItems = [
                                        [
                                            'product_id' => '',
                                            'serial_no' => '',
                                            'warranty_days' => 0,
                                            'qty' => 1,
                                            'unit_price' => 0,
                                        ],
                                    ];
                                }
                            @endphp

                            @foreach ($formItems as $i => $row)
                                <tr>
                                    <td>
                                        <select class="form-select product-select product-select2"
                                            name="items[{{ $i }}][product_id]" required>
                                            <option value="">-- Select --</option>
                                            @foreach ($products as $p)
                                                <option value="{{ $p->id }}" data-price="{{ $p->sell_price }}"
                                                    data-warranty="{{ $p->warranty_days ?? 0 }}"
                                                    {{ (string) ($row['product_id'] ?? '') === (string) $p->id ? 'selected' : '' }}>
                                                    {{ $p->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td>
                                        <input class="form-control" name="items[{{ $i }}][serial_no]"
                                            placeholder="Serial" value="{{ $row['serial_no'] ?? '' }}">
                                    </td>

                                    <td>
                                        <select class="form-select warranty-select"
                                            name="items[{{ $i }}][warranty_days]"
                                            data-selected="{{ $row['warranty_days'] ?? 0 }}"></select>
                                        <small class="text-muted d-block mt-1">
                                            End: <span class="warranty-end">-</span>
                                        </small>
                                    </td>

                                    <td>
                                        <input type="number" class="form-control qty"
                                            name="items[{{ $i }}][qty]" value="{{ $row['qty'] ?? 1 }}"
                                            min="1" required>
                                    </td>

                                    <td>
                                        <input type="number" step="0.01" class="form-control price"
                                            name="items[{{ $i }}][unit_price]"
                                            value="{{ $row['unit_price'] ?? 0 }}" required>
                                    </td>

                                    <td>
                                        <input type="text" class="form-control line-total" value="0.00" readonly>
                                    </td>

                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-primary" id="addRow">+ Add Item</button>
                        <h5 class="mb-0">Grand Total: <span id="grandTotal">0.00</span></h5>
                    </div>

                    <hr>

                    <button class="btn btn-primary">Update Invoice</button>
                    <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </div>
        </form>
    </div>

    {{-- ✅ jQuery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- ✅ Select2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        (function() {
            let rowIndex = {{ count($formItems) }};

            const WARRANTY_OPTIONS = [{
                    days: 0,
                    label: 'No Warranty'
                },
                {
                    days: 7,
                    label: '1 Week'
                },
                {
                    days: 14,
                    label: '2 Weeks'
                },
                {
                    days: 30,
                    label: '1 Month'
                },
                {
                    days: 90,
                    label: '3 Months'
                },
                {
                    days: 180,
                    label: '6 Months'
                },
                {
                    days: 365,
                    label: '1 Year'
                },
                {
                    days: 730,
                    label: '2 Years'
                },
                {
                    days: 1095,
                    label: '3 Years'
                },
                {
                    days: 1825,
                    label: '5 Years'
                },
            ];

            function pad2(n) {
                return String(n).padStart(2, '0');
            }

            function calcWarrantyEnd(days) {
                const invoiceDateInput = document.querySelector('input[name="invoice_date"]');
                const raw = invoiceDateInput ? invoiceDateInput.value : '';
                if (!raw || !days || days <= 0) return '-';

                const dt = new Date(raw + 'T00:00:00');
                dt.setDate(dt.getDate() + parseInt(days, 10));

                return pad2(dt.getDate()) + '/' + pad2(dt.getMonth() + 1) + '/' + dt.getFullYear();
            }

            function fillWarrantySelect(selectEl, selectedDays) {
                selectEl.innerHTML = '';
                WARRANTY_OPTIONS.forEach(opt => {
                    const o = document.createElement('option');
                    o.value = opt.days;
                    o.textContent = opt.label;
                    if (String(opt.days) === String(selectedDays)) o.selected = true;
                    selectEl.appendChild(o);
                });
            }

            function calcRow(row) {
                const qty = parseFloat(row.querySelector('.qty').value || 0);
                const price = parseFloat(row.querySelector('.price').value || 0);
                const total = qty * price;
                row.querySelector('.line-total').value = total.toFixed(2);
            }

            function calcGrand() {
                let sum = 0;
                document.querySelectorAll('.line-total').forEach(x => {
                    sum += parseFloat(x.value || 0);
                });
                document.getElementById('grandTotal').innerText = sum.toFixed(2);
            }

            function refreshWarrantyEnd(row) {
                const wSel = row.querySelector('.warranty-select');
                const endEl = row.querySelector('.warranty-end');
                if (!wSel || !endEl) return;
                endEl.textContent = calcWarrantyEnd(parseInt(wSel.value || '0', 10));
            }

            function initProductSelect2(ctx) {
                $(ctx).find('.product-select2').select2({
                    width: '100%',
                    placeholder: '-- Select --',
                    allowClear: true
                });
            }

            function bindRow(row) {
                const productSelect = row.querySelector('.product-select');
                const warrantySelect = row.querySelector('.warranty-select');

                initProductSelect2(row);

                let selectedWarranty = warrantySelect.getAttribute('data-selected') || 0;
                fillWarrantySelect(warrantySelect, selectedWarranty);
                refreshWarrantyEnd(row);

                $(productSelect).on('change', function() {
                    const opt = this.options[this.selectedIndex];
                    const price = opt ? (opt.getAttribute('data-price') || 0) : 0;
                    const wdays = opt ? (opt.getAttribute('data-warranty') || 0) : 0;

                    row.querySelector('.price').value = parseFloat(price || 0).toFixed(2);

                    fillWarrantySelect(warrantySelect, parseInt(wdays, 10) || 0);
                    refreshWarrantyEnd(row);

                    calcRow(row);
                    calcGrand();
                });

                warrantySelect.addEventListener('change', function() {
                    refreshWarrantyEnd(row);
                });

                row.querySelector('.qty').addEventListener('input', () => {
                    calcRow(row);
                    calcGrand();
                });
                row.querySelector('.price').addEventListener('input', () => {
                    calcRow(row);
                    calcGrand();
                });

                row.querySelector('.remove-row').addEventListener('click', function() {
                    const tbody = document.querySelector('#itemsTable tbody');
                    if (tbody.querySelectorAll('tr').length > 1) {
                        $(productSelect).select2('destroy');
                        row.remove();
                        calcGrand();
                    }
                });

                calcRow(row);
                calcGrand();
            }

            const invoiceDateInput = document.querySelector('input[name="invoice_date"]');
            if (invoiceDateInput) {
                invoiceDateInput.addEventListener('change', function() {
                    document.querySelectorAll('#itemsTable tbody tr').forEach(tr => refreshWarrantyEnd(tr));
                });
            }

            document.querySelectorAll('#itemsTable tbody tr').forEach(bindRow);

            document.getElementById('addRow').addEventListener('click', function() {
                const tbody = document.querySelector('#itemsTable tbody');
                const tr = document.createElement('tr');

                tr.innerHTML = `
      <td>
        <select class="form-select product-select product-select2" name="items[${rowIndex}][product_id]" required>
          <option value="">-- Select --</option>
          @foreach ($products as $p)
            <option
              value="{{ $p->id }}"
              data-price="{{ $p->sell_price }}"
              data-warranty="{{ $p->warranty_days ?? 0 }}"
            >
              {{ $p->name }}
            </option>
          @endforeach
        </select>
      </td>

      <td>
        <input class="form-control" name="items[${rowIndex}][serial_no]" placeholder="Serial">
      </td>

      <td>
        <select class="form-select warranty-select" name="items[${rowIndex}][warranty_days]" data-selected="0"></select>
        <small class="text-muted d-block mt-1">
          End: <span class="warranty-end">-</span>
        </small>
      </td>

      <td>
        <input type="number" class="form-control qty" name="items[${rowIndex}][qty]" value="1" min="1" required>
      </td>

      <td>
        <input type="number" step="0.01" class="form-control price" name="items[${rowIndex}][unit_price]" value="0" required>
      </td>

      <td>
        <input type="text" class="form-control line-total" value="0.00" readonly>
      </td>

      <td>
        <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
      </td>
    `;

                tbody.appendChild(tr);
                bindRow(tr);
                rowIndex++;
            });

        })();
    </script>

@endsection
