<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;

class SalePdfController extends Controller
{
    public function print(Sale $sale)
    {
        $sale->load('saleItems.product');

        $pdf = Pdf::loadView('pdf.invoice', [
            'sale' => $sale,
        ]);

        return $pdf->stream('Invoice ' . $sale->id . '.pdf');
    }
}
