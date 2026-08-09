<?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="space-y-6" dir="rtl">

        <div class="p-4 bg-gray-800 rounded-xl border border-green-500 shadow-lg">
            <input type="text" wire:model.defer="barcode" placeholder="Enter barcode here..."
                wire:keydown.enter="searchProduct(barcode)"
                class="w-full text-center text-xl p-3 rounded bg-white text-black" />
        </div>

        <div class="p-6 bg-gray-800 rounded-xl border border-green-500 shadow-lg">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lastScannedProduct): ?>
                <div class="text-center">
                    <h3 class="text-2xl font-bold text-white mb-2">
                        <?php echo e($lastScannedProduct->name); ?>

                    </h3>
                    <p class="text-lg text-green-400">
                        Price: <?php echo e(number_format($lastScannedProduct->price, 2)); ?>SYP
                    </p>
                </div>
            <?php else: ?>
                <div class="text-center text-gray-400 py-4">
                    <p>Waiting for a new scan...</p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <hr class="my-6 border-gray-700">

            <div class="text-center">
                <h2 class="text-3xl font-bold text-white">
                    Total: <?php echo e(number_format($totalPrice, 2)); ?>SYP
                </h2>
            </div>

            <div class="flex justify-center mt-6">
                <button wire:click="completeSale"
                    class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition duration-200">
                    Complete Sale
                </button>
            </div>

            <div class="flex justify-center mt-4">
                <button wire:click="resetScanner"
                    class="px-8 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition duration-200">
                    Scan Another Product
                </button>
            </div>
        </div>

        <div class="bg-black rounded-lg overflow-hidden border border-gray-700">
            <div id="reader" class="w-full"></div>
        </div>

        <script src="https://unpkg.com/html5-qrcode"></script>

        <script>
            const html5QrcodeScanner = new Html5QrcodeScanner("reader", {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 150
                }
            }, false);

            html5QrcodeScanner.render((decodedText) => {
                window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('searchProduct', decodedText);
                html5QrcodeScanner.clear();
            }, (error) => {});

            window.addEventListener('refresh-page', () => {
                location.reload();
            });
            document.addEventListener('DOMContentLoaded', function() {
                Quagga.init({
                    inputStream: {
                        name: "Live",
                        type: "LiveStream",
                        target: document.querySelector('#quagga-reader'),
                        constraints: {
                            facingMode: "environment"
                        }
                    },
                    decoder: {
                        readers: [
                            "code_128_reader",
                            "code_39_reader",
                            "ean_reader",
                            "ean_8_reader",
                            "upc_reader",
                            "upc_e_reader"
                        ]
                    }
                }, function(err) {
                    if (err) {
                        console.error(err);
                        return;
                    }
                    Quagga.start();
                });

                Quagga.onDetected(function(result) {
                    let code = result.codeResult.code;

                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('searchProduct', code);

                    Quagga.stop();
                });

                window.addEventListener('refresh-page', () => {
                    Quagga.start();
                });
            });
        </script>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php /**PATH D:\laragon\www\ipi405\resources\views/filament/admin/pages/scan-product.blade.php ENDPATH**/ ?>