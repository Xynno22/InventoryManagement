<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transaction #{{ $transaction->voucher_code }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 3rem;
            font-size: 12px;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 20px;
            min-width: 800px;
            width: 100%;
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
            width: 140px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            min-width: 760px;
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

        .no-promo {
            font-style: italic;
            color: #666;
        }

        .product-not-found {
            font-style: italic;
            color: #666;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>TRANSACTION REPORT</h1>
        <div>Transaction ID: #{{ $transaction->voucher_code }}</div>
        <div>Generated on: {{ date('d M Y H:i:s') }}</div>
    </div>

    <div>
        <div class="section-title">Transaction Information</div>

        <div class="info-row">
            <span class="info-label">Customer:</span>
            <span>{{ $transaction->customer_name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Transaction Date:</span>
            <span>{{ $transaction->created_at ? $transaction->created_at->format('d M Y H:i:s') : 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Items:</span>
            <span>{{ $transaction->transactionDetails->sum('quantity') }} items</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Price:</span>
            <span>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Created:</span>
            <span>{{ $transaction->created_at->format('d M Y H:i:s') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Updated:</span>
            <span>{{ $transaction->updated_at->format('d M Y H:i:s') }}</span>
        </div>
    </div>

    <div>
        <div class="section-title">Transaction Details</div>

        @if($transaction->transactionDetails && $transaction->transactionDetails->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Product Name</th>
                        <th>Qty</th>
                        <th>Promo</th>
                        <th>Unit Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaction->transactionDetails as $index => $detail)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                @if($detail->product)
                                    {{ $detail->product->name }}
                                    @if($detail->product->description)
                                        <div style="font-size: 10px; font-style: italic;">
                                            {{ $detail->product->description }}
                                        </div>
                                    @endif
                                @else
                                    <span class="product-not-found">Product not found</span>
                                @endif
                            </td>
                            <td class="text-center">{{ number_format($detail->quantity, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @if($detail->promo)
                                    {{ $detail->promo->name }}
                                @else
                                    <span class="no-promo">No promo</span>
                                @endif
                            </td>
                            <td class="text-right">Rp {{ number_format($detail->price ?? 0, 0, ',', '.') }}</td>
                            <td class="text-right">
                                @php
                                    $baseAmount = $detail->quantity * $detail->price;
                                        $subtotal = $baseAmount;

                                        if ($detail->promo) {
                                            $promoValue = $detail->promo->amount ?? 0;
                                            $promoValueLength = strlen((string)$promoValue);

                                            if ($promoValueLength == 2) {
                                                // Percentage discount
                                                $discount = ($baseAmount * $promoValue) / 100;
                                                $subtotal = $baseAmount - $discount;
                                            } elseif ($promoValueLength > 2) {
                                                // Fixed amount discount
                                                $subtotal = $baseAmount - $promoValue;
                                                // Ensure subtotal doesn't go below 0
                                                $subtotal = max(0, $subtotal);
                                            }
                                        }
                                    @endphp
                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right">Total Amount:</td>
                        <td class="text-right">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        @else
            <div class="no-details">
                No transaction details found.
            </div>
        @endif
    </div>

    <div class="footer">
        <p>This document was generated automatically from the system.</p>
    </div>

</body>
</html>
