<?php
$record = $getRecord();

$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
$barcode = $generator->getBarcode(
    $record->barcode,
    $generator::TYPE_CODE_128,
    2,
    60
);
?>

<div style="
    text-align: center;
    padding: 15px;
    background: white;
    border: 3px solid #333;
    border-radius: 8px;
    display: inline-block;
    margin: 5px;
">

    <img src="data:image/png;base64,<?php echo e(base64_encode($barcode)); ?>"
        style="width: 200px; height: auto;">


    <p style="font-size: 16px; font-weight: bold; margin: 8px 0; color: #333;">
        <?php echo e($record->barcode); ?>

    </p>


    <p style="font-size: 14px; color: #666; margin: 0;">
        <?php echo e($record->name); ?>

    </p>
</div><?php /**PATH D:\laragon\www\ipi405\resources\views/filament/tables/columns/barcode-viewer.blade.php ENDPATH**/ ?>