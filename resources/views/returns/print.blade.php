<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>{{ $return->return_no }}</title>
  <style>
    @page { size: A5 landscape; margin: 8mm; }
    body{ font-family: Arial; font-size: 12px; }
    table{ width:100%; border-collapse: collapse; margin-top: 10px; }
    th,td{ border:1px solid #000; padding:6px; }
  </style>
</head>
<body>
  <h3>Return Note: {{ $return->return_no }}</h3>
  <div>Date: {{ $return->return_date }}</div>
  <div>Customer: {{ $return->customer_name ?? '-' }} | Tel: {{ $return->customer_tel ?? '-' }}</div>
  <div>Reason: {{ $return->reason ?? '-' }}</div>

  <table>
    <thead>
      <tr><th>Item</th><th>Serial</th><th>Qty</th></tr>
    </thead>
    <tbody>
      @foreach($return->items as $it)
      <tr>
        <td>{{ $it->item_name }}</td>
        <td>{{ $it->serial_no ?? '-' }}</td>
        <td style="text-align:center">{{ $it->qty }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <br><br>
  <div style="display:flex;justify-content:space-between">
    <div>____________________<br>Customer</div>
    <div>____________________<br>Shop</div>
  </div>

  <script>window.print()</script>
</body>
</html>
