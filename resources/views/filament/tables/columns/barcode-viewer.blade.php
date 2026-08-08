@php
    $record = $getRecord();

    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
    $barcode = $generator->getBarcode($record->barcode, $generator::TYPE_CODE_128, 2, 60);
@endphp

<div
    style="
    text-align: center;
    padding: 15px;
    background: white;
    border: 3px solid #333;
    border-radius: 8px;
    display: inline-block;
    margin: 5px;
">
    <img src="data:image/png;base64,{{ base64_encode($barcode) }}" style="width: 200px; height: auto;">

    <p style="font-size: 16px; font-weight: bold; margin: 8px 0; color: #333;">
        {{ $record->barcode }}
    </p>

    <p style="font-size: 14px; color: #666; margin: 0;">
        {{ $record->name }}
    </p>
</div>
