<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $note->service_no }}</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            font-family: Arial, sans-serif;
            background: #fff;
            color: #0b2a55;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 10mm;
            border: 3px solid #1b4f9c;
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .brand {
            font-size: 34px;
            font-weight: 900;
            color: #1b4f9c;
        }

        .brand small {
            display: block;
            font-size: 14px;
            letter-spacing: 6px;
            margin-top: 2px;
            color: #c0392b;
        }

        .right {
            text-align: right;
            font-size: 12px;
            line-height: 1.5;
        }

        .title {
            text-align: center;
            font-weight: 900;
            margin: 10px 0;
            background: #1b4f9c;
            color: #fff;
            padding: 6px 10px;
            display: inline-block;
        }

        .grid {
            display: flex;
            gap: 12px;
            margin-top: 10px;
        }

        .box {
            border: 2px solid #1b4f9c;
            padding: 8px;
            flex: 1;
            font-size: 13px;
        }

        .box .row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .label {
            width: 160px;
            font-weight: 700;
        }

        .line {
            border-bottom: 1px dotted #1b4f9c;
            flex: 1;
            height: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th,
        td {
            border: 2px solid #1b4f9c;
            padding: 10px;
            font-size: 13px;
            vertical-align: top;
        }

        th {
            background: #f2f6ff;
        }

        .complain {
            border: 2px solid #1b4f9c;
            padding: 10px;
            margin-top: 12px;
            min-height: 130px;
        }

        .sign {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            font-weight: 700;
        }

        .sigline {
            width: 260px;
            border-bottom: 1px dotted #1b4f9c;
            height: 22px;
        }

        .section {
            text-align: center;
            font-weight: 900;
            margin-top: 18px;
            font-size: 16px;
        }

        .no-print {
            margin-top: 10px;
        }

        @media print {
            .no-print {
                display: none;
            }

            .page {
                border: 3px solid #1b4f9c;
            }
        }
    </style>
</head>

<body>

    <div class="page">

        <div class="top">
            <div>
                <img src="{{ asset('images/logo.png') }}" class="logo" alt="Dynamic Computer Systems" width="250vh"
                    height="auto">
            </div>
            <div class="right">
                No. 268/A, Anagarika Dharmapala Mw., Matara<br>
                Hot Line : 071 - 8209511<br>
                Email : dynamiccom@yahoo.com<br>
                <b>Service No:</b> {{ $note->service_no }}
            </div>
        </div>

        <div style="text-align:center;margin-top:6px;">
            <div class="title">Service Note</div>
        </div>

        <div class="grid">
            <div class="box">
                <div class="row">
                    <div class="label">Name</div>
                    <div class="line">{{ $note->customer_name }}</div>
                </div>
                <div class="row">
                    <div class="label">Address</div>
                    <div class="line">{{ $note->customer_address }}</div>
                </div>
                <div class="row">
                    <div class="label">Tel</div>
                    <div class="line">{{ $note->customer_tel }}</div>
                </div>
            </div>

            <div class="box" style="max-width:260px;">
                <div class="row">
                    <div class="label">Service No.</div>
                    <div class="line">{{ $note->service_no }}</div>
                </div>
                <div class="row">
                    <div class="label">Invoice Date</div>
                    <div class="line">{{ $note->service_date }}</div>
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width:26%;">Item</th>
                    <th style="width:24%;">Serial No.</th>
                    <th style="width:18%;">Invoice No.</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <tr style="height:120px;">
                    <td>{{ $note->item }}</td>
                    <td>{{ $note->serial_no }}</td>
                    <td>{{ $note->invoice_no }}</td>
                    <td>{{ $note->details }}</td>
                </tr>
            </tbody>
        </table>

        <div class="complain">
            <b>Customer Complains :</b><br><br>
            {!! nl2br(e($note->customer_complains)) !!}
        </div>
        <br><br> <br><br><br><br>
        <div class="sign">
            <div>
                <div class="sigline"></div>
                Customer Signature
            </div>
            <div>
                <div class="sigline"></div>
                Dynamic Computer Systems
            </div>
        </div>
        <br><br>
        <hr>
        <div class="section">Good Return Note</div>

        <div class="box" style="margin-top:10px;">
            <div class="row">
                <div class="label">Received Service Item</div>
                <div class="line">{{ $note->received_service_item }}</div>
            </div>
            <div class="row">
                <div class="label">Customer Name</div>
                <div class="line">{{ $note->grn_customer_name }}</div>
            </div>
            <div class="row">
                <div class="label">Customer Signature</div>
                <div class="line"></div>
                <div class="label">Date : </div>
                <div class="line"></div>
            </div>
        </div>
        <p> <strong>
                <ul style="color: #c0392b">

                    <li> අළුත්වැඩියා කිරීමෙන් පසු දින 07 ඇතුලත රැගෙන නොයන භාණ්ඩ සඳහා වගකියනු නොලැබේ . </li>
                    <li>රිසිට් පත නොමැතිව අළුත්වැඩියා කරනු ලබන භාණ්ඩ නිකුත් කරනු නොලැබේ .</li>
            </strong>
            </ul>
        </p>

        <div class="no-print">
            <button onclick="window.print()">Print</button>
        </div>

    </div>

</body>

</html>
