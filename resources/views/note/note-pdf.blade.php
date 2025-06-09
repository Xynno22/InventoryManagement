<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Note #{{ $note->customer_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
            background-color: "pink",
            margin: 0;
            padding: 20px;
        }

        .header, .footer {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .section-title {
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .info-row {
            margin-bottom: 5px;
        }

        .info-label {
            display: inline-block;
            width: 100px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 11px;
            text-align: left;
        }

        th {
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .no-details {
            text-align: center;
            font-style: italic;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>NOTE DETAIL</h1>
        <div>Note ID: #{{ $note->id }}</div>
        <div>Generated on: {{ date('d M Y H:i:s') }}</div>
    </div>

    <div>
        <div class="section-title">Note Information</div>

        <div class="info-row">
            <span class="info-label">Customer:</span>
            <span>{{ $note->customer_name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Price:</span>
            <span>Rp {{ number_format($note->total_price, 0, ',', '.') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Description:</span>
            <span>{{ $note->description ?? 'No description' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Created:</span>
            <span>{{ $note->created_at->format('d M Y H:i:s') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Updated:</span>
            <span>{{ $note->updated_at->format('d M Y H:i:s') }}</span>
        </div>
    </div>

    <div>
        <div class="section-title">Note Details</div>

        @if($note->noteDetails && $note->noteDetails->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Product Name</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($note->noteDetails as $index => $detail)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                {{ $detail->product->name ?? 'Product not found' }}
                                @if($detail->product && $detail->product->description)
                                    <div style="font-size: 10px; font-style: italic;">
                                        {{ $detail->product->description }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">{{ number_format($detail->quantity, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($detail->quantity * $detail->price, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right">Total Amount:</td>
                        <td class="text-right">Rp {{ number_format($note->total_price, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        @else
            <div class="no-details">
                No note details found.
            </div>
        @endif
    </div>

    <div class="footer">
        <p>This document was generated automatically from the system.</p>
    </div>

</body>
</html>
