<?php if (isset($component)) {
    $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component;
} ?>
<?php if (isset($attributes)) {
    $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes;
} ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index', 'data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
            <input type="text"
                wire:model.lazy="barcode"
                wire:keydown.enter="searchProduct($event.target.value)"
                placeholder="Enter barcode here or use scanner..."
                class="w-full text-center text-xl p-3 rounded bg-white text-black"
                autofocus />
        </div>

        <div class="p-6 bg-gray-800 rounded-xl border border-green-500 shadow-lg text-white">
            <h3 class="text-xl font-bold mb-4">Current Invoice List:</h3>
            <div class="overflow-x-auto mb-6">
                <table class="w-full text-right text-sm">
                    <thead class="border-b border-gray-600 text-green-400">
                        <tr>
                            <th class="py-2">Product</th>
                            <th class="py-2">Price</th>
                            <th class="py-2">Quantity</th>
                            <th class="py-2">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true;
                                                                                                                                                                                                                                                        $__currentLoopData = $cart;
                                                                                                                                                                                                                                                        $__env->addLoop($__currentLoopData);
                                                                                                                                                                                                                                                        foreach ($__currentLoopData as $item): $__env->incrementLoopIndices();
                                                                                                                                                                                                                                                            $loop = $__env->getLastLoop();
                                                                                                                                                                                                                                                            $__empty_1 = false; ?><?php if (\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr class="border-b border-gray-700">
                                <td class="py-2 font-bold"><?php echo e($item['name']); ?></td>
                                <td class="py-2"><?php echo e(number_format($item['price'], 2)); ?></td>
                                <td class="py-2 text-green-400 font-bold"><?php echo e($item['quantity']); ?></td>
                                <td class="py-2"><?php echo e(number_format($item['total'], 2)); ?></td>
                            </tr>
                            <?php if (\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach;
                                                                                                                                                                                                                                                        $__env->popLoop();
                                                                                                                                                                                                                                                        $loop = $__env->getLastLoop();
                                                                                                                                                                                                                                                        if ($__empty_1): ?><?php if (\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="4" class="py-4 text-center text-gray-400">No products scanned yet</td>
                            </tr>
                            <?php endif; ?><?php if (\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <hr class="my-4 border-gray-700">

            <div class="text-center">
                <h2 class="text-3xl font-bold text-white">
                    TOTAL: <?php echo e(number_format($totalPrice, 2)); ?> SYP
                </h2>
            </div>

            <div class="flex justify-center gap-4 mt-6">
                <button wire:click="completeSale"
                    class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition">
                    Complete Sale & Save Invoice </button>
                <button wire:click="resetScanner"
                    class="px-8 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition">
                    Clear / New Invoice </button>
            </div>
        </div>

        <div class="p-6 bg-gray-800 rounded-xl border border-green-500 shadow-lg text-center space-y-4">
            <label class="block text-white font-bold text-lg">Upload Barcode Image:</label>
            <input type="file" id="qr-input-file" accept="image/*" class="block w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-green-600 file:text-white cursor-pointer" />
            <div id="reader" class="w-full mt-4 bg-black rounded-lg overflow-hidden min-h-[150px]"></div>
        </div>

        <script src="https://unpkg.com/html5-qrcode"></script>
        <script>
            document.addEventListener('livewire:initialized', () => {
                const html5QrCode = new Html5Qrcode("reader");
                let isProcessing = false;

                const fileInput = document.getElementById('qr-input-file');
                fileInput.addEventListener('change', e => {
                    if (e.target.files.length == 0 || isProcessing) return;
                    isProcessing = true;
                    html5QrCode.scanFile(e.target.files[0], true)
                        .then(decodedText => {
                            window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('searchProduct', decodedText).then(() => {
                                setTimeout(() => {
                                    isProcessing = false;
                                }, 1500);
                            });
                        })
                        .catch(err => {
                            isProcessing = false;
                            alert("No clear barcode was found in the image.");
                        });
                });

                const config = {
                    fps: 5,
                    qrbox: {
                        width: 300,
                        height: 150
                    }
                };
                html5QrCode.start({
                    facingMode: "environment"
                }, config, (decodedText) => {
                    if (!isProcessing) {
                        isProcessing = true;
                        window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('searchProduct', decodedText).then(() => {
                            setTimeout(() => {
                                isProcessing = false;
                            }, 1500);
                        });
                    }
                }).catch(err => console.warn("Camera not available  "));
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
    <?php endif; ?><?php /**PATH D:\laragon\www\ipi405\resources\views/filament/admin/pages/scan-product.blade.php ENDPATH**/ ?>