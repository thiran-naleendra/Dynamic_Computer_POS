@extends('layouts.app')
@section('content')

    {{-- Select2 CSS --}}
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
        <h4 class="mb-3">New Invoice</h4>

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('invoices.store') }}">
            @csrf

            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Invoice Date</label>
                            <input type="date" name="invoice_date" class="form-control"
                                value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label">Select Customer</label>
                            <select name="customer_id" id="customerSelect" class="form-select customer-select2">
                                <option value="">-- Walk-in / Type Customer Name --</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        data-name="{{ $customer->name }}"
                                        {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}{{ !empty($customer->tel) ? ' - ' . $customer->tel : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label">Customer Name</label>
                            <input type="text" name="customer_name" id="customerNameInput" class="form-control"
                                value="{{ old('customer_name') }}"
                                placeholder="Type customer name or edit selected customer name">
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
                                <th width="140">Available Stock</th>
                                <th style="min-width:160px;">Serial No (optional)</th>
                                <th style="min-width:170px;">Warranty</th>
                                <th width="120">Qty</th>
                                <th width="160">Unit Price</th>
                                <th width="160">Line Total</th>
                                <th width="80"></th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    <select class="form-select product-select product-select2" name="items[0][product_id]"
                                        required>
                                        <option value="">-- Select --</option>
                                        @foreach ($products as $p)
                                            <option value="{{ $p->id }}">
                                                {{ $p->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td>
                                    <span class="stock-badge badge bg-secondary">-</span>
                                    <small class="text-danger d-block stock-error mt-1"></small>
                                </td>

                                <td>
                                    <input class="form-control" name="items[0][serial_no]" placeholder="Serial"
                                        value="{{ old('items.0.serial_no') }}">
                                </td>

                                <td>
                                    <select class="form-select warranty-select" name="items[0][warranty_days]"></select>
                                    <small class="text-muted d-block mt-1">
                                        End: <span class="warranty-end">-</span>
                                    </small>
                                </td>

                                <td>
                                    <input type="number" class="form-control qty" name="items[0][qty]"
                                        value="{{ old('items.0.qty', 1) }}" min="1" required>
                                </td>

                                <td>
                                    <input type="number" step="0.01" class="form-control price"
                                        name="items[0][unit_price]" value="{{ old('items.0.unit_price', 0) }}" required>
                                </td>

                                <td><input type="text" class="form-control line-total" value="0.00" readonly></td>
                                <td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-primary" id="addRow">+ Add Item</button>
                        <h5 class="mb-0">Grand Total: <span id="grandTotal">0.00</span></h5>
                    </div>

                    <hr>

                    <button class="btn btn-success">Save Invoice</button>
                    <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </div>
        </form>
    </div>

    {{-- Select2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        (function() {
            let rowIndex = 1;

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
                document.querySelectorAll('.line-total').forEach(x => sum += parseFloat(x.value || 0));
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

            function initCustomerSelect2() {
                $('.customer-select2').select2({
                    width: '100%',
                    placeholder: 'Search customer...',
                    allowClear: true
                });
            }

            function setStockUI(row, stock) {
                row.dataset.availableStock = stock;

                const badge = row.querySelector('.stock-badge');
                if (badge) {
                    badge.textContent = stock;
                    badge.className = 'stock-badge badge ' + (stock > 0 ? 'bg-success' : 'bg-danger');
                }
            }

            function validateRowStock(row) {
                const qtyInput = row.querySelector('.qty');
                const errorEl = row.querySelector('.stock-error');
                const productSelect = row.querySelector('.product-select');

                const productId = productSelect ? productSelect.value : '';
                const availableRaw = row.dataset.availableStock;

                if (!productId) {
                    qtyInput.classList.remove('is-invalid');
                    if (errorEl) errorEl.textContent = '';
                    return true;
                }

                if (availableRaw === undefined || availableRaw === null || availableRaw === '') {
                    qtyInput.classList.remove('is-invalid');
                    if (errorEl) errorEl.textContent = '';
                    return true;
                }

                const available = parseInt(availableRaw, 10);
                const qty = parseInt(qtyInput.value || '0', 10);

                if (qty > available) {
                    qtyInput.classList.add('is-invalid');
                    if (errorEl) {
                        errorEl.textContent = `Out of stock. Available: ${available}, Requested: ${qty}`;
                    }
                    return false;
                }

                qtyInput.classList.remove('is-invalid');
                if (errorEl) {
                    errorEl.textContent = '';
                }
                return true;
            }

            async function fetchProductStock(productId, row) {
                if (!productId) {
                    row.dataset.availableStock = '';
                    const badge = row.querySelector('.stock-badge');
                    const errorEl = row.querySelector('.stock-error');

                    if (badge) {
                        badge.textContent = '-';
                        badge.className = 'stock-badge badge bg-secondary';
                    }

                    if (errorEl) errorEl.textContent = '';
                    return;
                }

                try {
                    const url = `{{ url('/invoices/product-stock') }}/${productId}`;
                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('HTTP error ' + response.status);
                    }

                    const data = await response.json();

                    row.querySelector('.price').value = parseFloat(data.sell_price || 0).toFixed(2);
                    fillWarrantySelect(row.querySelector('.warranty-select'), parseInt(data.warranty_days || 0, 10));
                    refreshWarrantyEnd(row);
                    setStockUI(row, parseInt(data.available_stock || 0, 10));

                    calcRow(row);
                    calcGrand();
                    validateRowStock(row);
                } catch (e) {
                    console.error('Stock fetch failed:', e);

                    row.dataset.availableStock = '';
                    const badge = row.querySelector('.stock-badge');
                    const errorEl = row.querySelector('.stock-error');

                    if (badge) {
                        badge.textContent = 'Error';
                        badge.className = 'stock-badge badge bg-warning';
                    }

                    if (errorEl) {
                        errorEl.textContent = 'Unable to load stock';
                    }
                }
            }

            function bindRow(row) {
                const productSelect = row.querySelector('.product-select');
                const warrantySelect = row.querySelector('.warranty-select');

                initProductSelect2(row);

                fillWarrantySelect(warrantySelect, 0);
                refreshWarrantyEnd(row);
                row.dataset.availableStock = '';

                $(productSelect).on('change', function() {
                    const productId = this.value;
                    fetchProductStock(productId, row);
                });

                warrantySelect.addEventListener('change', function() {
                    refreshWarrantyEnd(row);
                });

                row.querySelector('.qty').addEventListener('input', () => {
                    calcRow(row);
                    calcGrand();
                    validateRowStock(row);
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
            initCustomerSelect2();

            document.getElementById('addRow').addEventListener('click', function() {
                const tbody = document.querySelector('#itemsTable tbody');
                const tr = document.createElement('tr');

                tr.innerHTML = `
                    <td>
                        <select class="form-select product-select product-select2" name="items[${rowIndex}][product_id]" required>
                            <option value="">-- Select --</option>
                            @foreach ($products as $p)
                                <option value="{{ $p->id }}">
                                    {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>

                    <td>
                        <span class="stock-badge badge bg-secondary">-</span>
                        <small class="text-danger d-block stock-error mt-1"></small>
                    </td>

                    <td>
                        <input class="form-control" name="items[${rowIndex}][serial_no]" placeholder="Serial">
                    </td>

                    <td>
                        <select class="form-select warranty-select" name="items[${rowIndex}][warranty_days]"></select>
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

                    <td><input type="text" class="form-control line-total" value="0.00" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>
                `;

                tbody.appendChild(tr);
                bindRow(tr);
                rowIndex++;
            });

            const customerSelect = document.getElementById('customerSelect');
            const customerNameInput = document.getElementById('customerNameInput');

            function syncCustomerName() {
                if (!customerSelect || !customerNameInput) return;

                const selected = customerSelect.options[customerSelect.selectedIndex];
                if (customerSelect.value) {
                    customerNameInput.value = selected.getAttribute('data-name') || '';
                }
            }

            if (customerSelect) {
                $(customerSelect).on('change', function() {
                    syncCustomerName();
                });

                syncCustomerName();
            }

            const form = document.querySelector('form[action="{{ route('invoices.store') }}"]');
            form.addEventListener('submit', function(e) {
                let hasError = false;

                document.querySelectorAll('#itemsTable tbody tr').forEach(row => {
                    if (!validateRowStock(row)) {
                        hasError = true;
                    }
                });

                if (hasError) {
                    e.preventDefault();
                    alert('Some items are out of stock. Please fix them before saving invoice.');
                    return;
                }

                if (!customerSelect.value && !customerNameInput.value.trim()) {
                    e.preventDefault();
                    alert('Please select a customer or type customer name.');
                }
            });

        })();
    </script>

@endsection