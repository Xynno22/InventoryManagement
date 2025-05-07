<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sales Report PDF</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Transaction Report</h2>
    <p>Date Range: 
        {{ $startDate ? $startDate->format('d/m/Y') : 'N/A' }} - 
        {{ $endDate ? $endDate->format('d/m/Y') : 'N/A' }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Invoice</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $tx)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($tx->date)->format('d/m/Y') }}</td>
                    <td>{{ $tx->voucher_code}}</td>
                    <td>{{ $tx->customer_name }}</td>
                    <td>Rp {{ number_format($tx->total_price, 0, ',', '.') }}</td>
                    <td>{{ $tx->payment->name ?? '-' }}</td>
                    <td>{{ $tx->status->name ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
