<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $sale->id }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Sales Invoice </h2>
    <p>Invoice Number : #{{ $sale->id }}</p>
    <p>Date: {{ $sale->created_at->format('Y-m-d H:i') }}</p>
    <p>Customer: {{ $sale->customer_name ?? 'Cash' }}</p>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>

            </tr>
        </thead>
        <tbody>
            @foreach($sale->saleItems as $item)
            <tr>
                <td>{{ $item->product?->name ?? 'Deleted Product ' }}</td>
                <td>{{ (int) $item->quantity }}</td>
                <td>{{ number_format($item->price, 0) }} SYP</td>

            </tr>
            @endforeach
        </tbody>
    </table>

    <h3 style="margin-top: 20px;">Total: {{ number_format($sale->total_price, 0) }} SYP</h3>
    @if($sale->remaining_price > 0)
    <h3>Remaining Balance: {{ number_format($sale->remaining_price, 0) }} SYP</h3>
    @endif
</body>
</html>
