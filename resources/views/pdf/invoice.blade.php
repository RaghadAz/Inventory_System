<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $sale->id }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap');

        body {
            font-family: 'Cairo', sans-serif;
            direction: rtl;
            padding: 20px;
            background: #f5f5f5;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            background: #fff;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #2c3e50;
            margin: 0;
        }

        .info {
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background: #2c3e50;
            color: #fff;
        }

        .totals {
            margin-top: 20px;
            border-top: 2px solid #333;
            padding-top: 20px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .grand-total {
            font-size: 20px;
            font-weight: bold;
            color: #e74c3c;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #7f8c8d;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <div class="header">
            <h1>🧾 Sales Invoice</h1>
            <p>Invoice Number : #{{ $sale->id }}</p>
            <p>Date: {{ $sale->created_at->format('Y-m-d H:i') }}</p>
        </div>

        <div class="info">
            <div class="info-row">
                <span><strong>👤 Customer:</strong> {{ $sale->customer_name ?? 'Cash' }}</span>
                <span><strong>💳 Payment Method:</strong>
                    {{ $sale->payment_type === 'cash' ? 'Cash' : 'Credit' }}</span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price </th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->saleItems as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->product?->name ?? 'Deleted Product ' }}</td>
                        <td>{{ (int) $item->quantity }}</td>
                        <td>{{ number_format($item->price, 0) }} SYP</td>
                        <td>{{ number_format($item->total, 0) }} SYP</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="total-row">
                <span>💰 Total:</span>
                <span>{{ number_format($sale->total_price, 0) }} SYP</span>
            </div>
            <div class="total-row">
                <span>💵 Paid:</span>
                <span>{{ number_format($sale->paid_amount, 0) }} SYP</span>
            </div>
            @if ($sale->remaining_price > 0)
                <div class="total-row" style="color: #e74c3c;">
                    <span>📋 Remaining Balance:</span>
                    <span>{{ number_format($sale->remaining_price, 0) }} SYP</span>
                </div>
            @endif
            <div class="total-row grand-total">
                <span>📈 Profit:</span>
                <span>{{ number_format($sale->total_profit, 0) }} SYP</span>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for your business 🙏</p>
            <p> Printed on: {{ now()->format('Y-m-d H:i') }}</p>
        </div>
    </div>
</body>

</html>
