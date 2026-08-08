<table class="table">
    <thead>
        <tr>
            <th>Item</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sales as $sale)
            <tr>
                <td>{{ $sale->product->name }}</td>
                <td>{{ $sale->quantity_sold }}</td>
                <td>{{ number_format($sale->selling_price) }}</td>
                <td>{{ number_format($sale->total_price) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3"> Grand Total</td>
            <td>{{ number_format($grandTotal) }} SYP</td>
        </tr>
    </tfoot>
</table>
