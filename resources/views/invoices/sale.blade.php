<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>Sales Invoice #{{ $sale->id }}</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            border: 1px solid #eee;
            padding: 30px;
        }

        table {
            width: 100%;
            line-height: 2column;
            text-align: right;
            border-collapse: collapse;
        }

        table td {
            padding: 5px;
            vertical-align: top;
        }

        table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <h2> Sales Invoice</h2>
        <p><strong>Invoice Number :</strong> {{ $sale->id }}</p>
        <p><strong> Customer Name:</strong> {{ $sale->customer_name }}</p>
        <p><strong>Date:</strong> {{ $sale->created_at->format('Y-m-d') }}</p>

        <table>
            <tr class="heading">
                <td>Product</td>
                <td>Quantity</td>
                <td>Unit Price </td>
                <td>Total</td>
            </tr>
            @foreach ($sale->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->price) }} SYP</td>
                    <td>{{ number_format($item->quantity * $item->price) }} SYP</td>
                </tr>
            @endforeach
        </table>
    </div>
</body>

</html>
