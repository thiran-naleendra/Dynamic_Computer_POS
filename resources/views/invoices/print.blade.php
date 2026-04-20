<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_no }} - Invoice</title>

    @php
        $size = strtolower(request('size', 'a4'));
        $isA5 = $size === 'a5';

        $pageW = '210mm';
        $pageH = $isA5 ? '148mm' : '297mm';
        $pageSize = $isA5 ? 'A5 landscape' : 'A4 portrait';
        $pageMargin = '0mm';
        $pad = $isA5 ? '8mm' : '12mm';

        // Warranty label map (days => label)
        $warrantyMap = [
            0 => 'No Warranty',
            7 => '1 Week',
            14 => '2 Weeks',
            30 => '1 Month',
            90 => '3 Months',
            180 => '6 Months',
            365 => '1 Year',
            730 => '2 Years',
            1095 => '3 Years',
            1825 => '5 Years',
        ];

        $invoiceDate = \Carbon\Carbon::parse($invoice->invoice_date);
    @endphp

    <style>
        @page {
            size: {{ $pageSize }};
            margin: {{ $pageMargin }};
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
            background: #f3f3f3;
            padding: 12px;
            font-size: {{ $isA5 ? '11px' : '12px' }};
        }

        /* Toolbar */
        .print-toolbar {
            position: fixed;
            top: 12px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            display: flex;
            gap: 10px;
            align-items: center;
            background: #fff;
            padding: 8px 10px;
            border: 1px solid rgba(0, 0, 0, .15);
            border-radius: 10px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, .15);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 10px;
            font-size: 12px;
            font-weight: 700;
            border-radius: 8px;
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
            user-select: none;
            white-space: nowrap;
            transition: .15s ease;
        }

        .btn:hover {
            filter: brightness(.96);
        }

        .btn-print {
            background: #198754;
            color: #fff;
            border-color: #198754;
        }

        .btn-pdf {
            background: #6f42c1;
            color: #fff;
            border-color: #6f42c1;
        }

        .btn-a5 {
            background: {{ $isA5 ? '#8b0000' : '#fff' }};
            color: {{ $isA5 ? '#fff' : '#8b0000' }};
            border-color: #8b0000;
        }

        .btn-a4 {
            background: {{ !$isA5 ? '#0d6efd' : '#fff' }};
            color: {{ !$isA5 ? '#fff' : '#0d6efd' }};
            border-color: #0d6efd;
        }

        .tag {
            font-size: 11px;
            font-weight: 700;
            padding: 6px 10px;
            border-radius: 999px;
            background: #f1f3f5;
            border: 1px solid #ddd;
            color: #333;
        }

        /* Paper */
        .paper {
            width: {{ $pageW }};
            min-height: {{ $pageH }};
            margin: 60px auto 0;
            background: #fff;
            padding: {{ $pad }};
            box-shadow: 0 0 10px rgba(0, 0, 0, .12);
            border-radius: 10px;
            page-break-inside: avoid;
        }

        /* HEADER */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10mm;
            padding-bottom: {{ $isA5 ? '8px' : '12px' }};
            border-bottom: 4px solid #070F9C;
            margin-bottom: {{ $isA5 ? '8px' : '12px' }};
        }

        .logo {
            max-width: {{ $isA5 ? '240px' : '220px' }};
            height: auto;
            object-fit: contain;
        }

        .company-info {
            text-align: right;
            font-size: {{ $isA5 ? '10px' : '11px' }};
            line-height: 1.5;
            white-space: nowrap;
            font-weight: 600;
        }

        /* META */
        .meta {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            font-size: {{ $isA5 ? '10px' : '11px' }};
            margin: 4px 0 8px;
        }

        .meta .right {
            text-align: right;
        }

        .invoice-title {
            text-align: center;
            font-size: {{ $isA5 ? '12px' : '13px' }};
            font-weight: 800;
            letter-spacing: 1px;
            text-decoration: underline;
            margin: 6px 0 10px;
        }

        .thank-you {
            text-align: center;
            font-size: {{ $isA5 ? '12px' : '13px' }};
            font-weight: 800;
            letter-spacing: 1px;
            margin: 6px 0 10px;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: {{ $isA5 ? '10px' : '11px' }};
        }

        thead {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }

        th {
            padding: {{ $isA5 ? '6px' : '8px' }} 6px;
            text-align: left;
            font-weight: 700;
            background: #f9f9f9;
            text-transform: uppercase;
        }

        td {
            padding: {{ $isA5 ? '6px' : '8px' }} 6px;
            border-bottom: 1px solid #d0d0d0;
            vertical-align: top;
        }

        .col-item {
            width: 50%;
        }

        .col-qty {
            width: 10%;
            text-align: center;
        }

        .col-unit {
            width: 16%;
            text-align: center;
        }

        .col-amt {
            width: 16%;
            text-align: right;
        }

        .col-war {
            width: 8%;
            text-align: center;
        }

        .item-name {
            font-weight: 700;
        }

        .item-details {
            font-size: {{ $isA5 ? '9px' : '10px' }};
            color: #666;
            margin-top: 2px;
        }

        .w-badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 999px;
            font-size: {{ $isA5 ? '9px' : '10px' }};
            font-weight: 800;
            border: 1px solid #ddd;
            background: #f1f3f5;
            white-space: nowrap;
        }

        .w-badge.none {
            background: #eee;
            color: #333;
            border-color: #ddd;
        }

        .w-badge.has {
            background: #fff3cd;
            border-color: #ffe69c;
            color: #8b0000;
        }

        .total-row td {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            font-weight: 800;
        }

        /* NOTES */
        .warranty-notes {
            font-size: {{ $isA5 ? '9px' : '10px' }};
            line-height: 1.6;
            margin-top: {{ $isA5 ? '8px' : '12px' }};
            color: #333;
            font-weight: 700;
        }

        .highlight {
            color: #D2042D;
            font-weight: 800;
        }

        /* SIGNATURE */
        .signature {
            margin-top: {{ $isA5 ? '10px' : '18px' }};
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 1px;
            font-size: {{ $isA5 ? '10px' : '11px' }};
        }

        .sig-block {
            width: 25%;
            text-align: center;
        }

        .sig-line {
            width: 45%;
            border-bottom: 1px dashed #000;
            height: 20px;
            margin-bottom: 6px;
            margin: 0 auto 6px auto;
        }

        .footer-line {

            margin-top: {{ $isA5 ? '10px' : '14px' }};
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .print-toolbar {
                display: none !important;
            }

            .paper {
                margin: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                width: {{ $pageW }} !important;
                height: {{ $pageH }} !important;
                min-height: {{ $pageH }} !important;
                overflow: hidden !important;
            }

            .paper,
            table,
            .warranty-notes,
            .signature {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    <div class="print-toolbar">
        <span class="tag">Size: {{ $isA5 ? 'A5 Bill' : 'A4 Invoice' }}</span>

        <button class="btn btn-print" onclick="window.print()">🖨 Print</button>
        <button class="btn btn-pdf" onclick="savePdf()">⬇️ Save PDF</button>

        <a class="btn btn-a5" href="?size=a5">A5 Bill</a>
        <a class="btn btn-a4" href="?size=a4">A4 Invoice</a>
    </div>

    <div class="paper">

        <div class="header">
            <div>
                <img src="{{ asset('images/logo.png') }}" class="logo" alt="Dynamic Computer Systems">
            </div>

            <div class="company-info">
                <div>
                    <h3> NO: 268/A, ANAGARIKA DHARMAPALA MW, NUPE, MATARA.<h3>
                </div>
                <div>
                    <h3>Tel: 071 - 8209511</h3>
                </div>
                <div>Email: dynamiccom@yahoo.com</div>
            </div>
        </div>

        <div class="meta">
            <div><b>TO:</b> {{ strtoupper($invoice->customer_name ?? 'CUSTOMER') }}</div>
            <div class="right">
                <div><b>Date:</b> {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</div>
                <div><b>Invoice No:</b> {{ $invoice->invoice_no }}</div>
            </div>
        </div>

        <div class="invoice-title">INVOICE</div>

        <table>
            <thead>
                <tr>
                    <th class="col-item">Description</th>
                    <th class="col-qty">Qty</th>
                    <th class="col-war">Warranty</th>
                    <th class="col-unit">Unit Price</th>
                    <th class="col-amt">Amount</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $it)
                    @php
                        // expects invoice_items.warranty_days (int). If null -> use product warranty if you want.
                        $days = (int) ($it->warranty_days ?? 0);
                        $label = $warrantyMap[$days] ?? ($days > 0 ? $days . 'd' : 'No');

                        $end = null;
                        if ($days > 0) {
                            $end = $invoiceDate->copy()->addDays($days)->format('d/m/Y');
                        }
                    @endphp

                    <tr>
                        <td class="col-item">
                            <div class="item-name">{{ $it->item_name }}</div>
                            @if (!empty($it->serial_no))
                                <div class="item-details">S/N: {{ $it->serial_no }}</div>
                            @endif

                            {{-- Optional: show warranty end date under item for A4 only --}}
                            {{-- @if (!$isA5 && $days > 0)
                                <div class="item-details">Warranty End: {{ $end }}</div>
                            @endif --}}
                        </td>

                        <td class="col-qty">{{ str_pad($it->qty, 2, '0', STR_PAD_LEFT) }}</td>
                        <td class="col-war">
                            {{ $days > 0 ? $days . ' days' : 'No' }}
                        </td>
                        {{-- <td class="col-war"> @if ($days <= 0) <span class="">No</span> @else <span class="">{{ $label }}</span> @endif </td> --}}

                        <td class="col-unit">Rs. {{ number_format($it->unit_price, 2) }}</td>
                        <td class="col-amt">Rs. {{ number_format($it->line_total, 2) }}</td>


                    </tr>
                @endforeach

                <tr class="total-row">
                    <td colspan="4" style="text-align:right;">TOTAL</td>
                    <td class="col-amt">Rs. {{ number_format($invoice->grand_total, 2) }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <div class="warranty-notes">
            <div>* WARRANTY PERIOD <span class="highlight">ONE YEAR LESS 17</span> WORKING DAYS</div>
            <div>* WARRANTY COVERS ONLY MANUFACTURES DEFECTS DAMAGE OR DEFECT DUE TO OTHER CAUSES</div>
            <div>* SUCH AS NEGLIGENCE, MISUSES, IMPROPER OPERATION, POWER FLUCTUATION, LIGHTNING OR OTHER</div>
            <div style="margin-left:5px;">NATURAL DISASTERS, SABOTAGE OR ACCIDENT ETC.</div>
            <div>* FOR THE ITEMS IF THERE IS <span class="highlight">BURN MARKS, PHYSICAL DAMAGES</span> AND <span
                    class="highlight">CORROSION</span> NO WARRANTY.</div>
            <div>* GOODS ONES SOLD ARE NOT RETURNABLE UNDER ANY CIRCUMSTANCES</div>
            <div>* විකුණන ලද භාණ්ඩ නැවත භාරගැනීම හ ඒ සඳහා මුදල් ආපසු ලබාදීම සිදුනොකෙරේ</div>
            <div><span class="highlight">* වගකීම් ආවරණය සඳහා බිල්පත ඉදිරිපත් කිරීම අනිවාර්‍ය වේ</span></div>

            {{-- Optional: show per-item warranty end dates in A4 only --}}
            {{-- @if (!$isA5)
                <div style="margin-top:6px;">
                    <span class="highlight">Warranty End Dates:</span>
                    <ul style="margin:4px 0 0 16px;">
                        @foreach ($invoice->items as $it)
                            @php
                                $d = (int) ($it->warranty_days ?? 0);
                            @endphp
                            @if ($d > 0)
                                <li>
                                    {{ $it->item_name }} -
                                    {{ $warrantyMap[$d] ?? $d . ' days' }}
                                    (End: {{ $invoiceDate->copy()->addDays($d)->format('d/m/Y') }})
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif --}}
        </div>

        <div class="signature">
            <div class="sig-block">
                <div class="sig-line"></div>
                <b>DYNAMIC COMPUTER SYSTEMS</b><br>
                <small>Authorized Signature</small>
            </div>
            <div class="sig-block">
                <div class="sig-line"></div>
                <b>CUSTOMER</b><br>
                <small>Goods Received in Good Conditions</small>
            </div>


        </div>

        {{-- <div class="footer-line"></div> --}}
        <br>
        <div class="thank-you">
            Thank You Come Again !
        </div>
        <div class="footer-line">
            {{-- {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('l, d F Y - h:i A') }} --}}
            {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('l, d F Y ') }}
        </div>


    </div>

    <script>
        function savePdf() {
            // browser will show print dialog; user selects "Save as PDF"
            document.title = "{{ $invoice->invoice_no }}_{{ $isA5 ? 'A5' : 'A4' }}";
            window.print();
        }
    </script>

</body>

</html>
