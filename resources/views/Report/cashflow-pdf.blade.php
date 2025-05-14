<!DOCTYPE html>
<html>
<head>
    <title>Cashflow Report</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Cashflow Report</h2>
    <p>Date: {{ $startDate->format('d/m/Y') }} to {{ $endDate->format('d/m/Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Product Name</th>
                <th>Note</th>
                <th>Cash In</th>
                <th>Cash Out</th>
                <th>Payment</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $item)
                <tr>
                    <td>{{ $item['date'] }}</td>
                    <td>{{ $item['product_name'] }}</td>
                    <td>{{ $item['note'] }}</td>
                    <td>Rp {{ number_format($item['cash_in'], 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($item['cash_out'], 0, ',', '.') }}</td>
                    <td>{{ $item['payment_method'] }}</td>
                    <td>{{ $item['status'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>


</body>
</html>
