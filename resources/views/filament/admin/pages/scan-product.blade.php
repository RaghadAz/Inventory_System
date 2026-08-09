<x-filament-panels::page>
    <div class="space-y-6" dir="rtl">

        <!-- حقل إدخال الباركود -->
        <div class="p-4 bg-gray-800 rounded-xl border border-green-500 shadow-lg">
            <input type="text" 
                   wire:model.lazy="barcode" 
                   wire:keydown.enter="searchProduct($event.target.value)"
                   placeholder="Enter barcode here or use scanner..."
                   class="w-full text-center text-xl p-3 rounded bg-white text-black" 
                   autofocus />
        </div>

        <!-- جدول المنتجات في الفاتورة الحالية -->
        <div class="p-6 bg-gray-800 rounded-xl border border-green-500 shadow-lg text-white">
            <h3 class="text-xl font-bold mb-4">قائمة الفاتورة الحالية:</h3>
            
            <div class="overflow-x-auto mb-6">
                <table class="w-full text-right text-sm">
                    <thead class="border-b border-gray-600 text-green-400">
                        <tr>
                            <th class="py-2">المنتج</th>
                            <th class="py-2">السعر</th>
                            <th class="py-2">الكمية</th>
                            <th class="py-2">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cart as $item)
                            <tr class="border-b border-gray-700">
                                <td class="py-2 font-bold">{{ $item['name'] }}</td>
                                <td class="py-2">{{ number_format($item['price'], 2) }}</td>
                                <td class="py-2 text-green-400 font-bold">{{ $item['quantity'] }}</td>
                                <td class="py-2">{{ number_format($item['total'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-gray-400">لم يتم إدخال أي منتج بعد</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <hr class="my-4 border-gray-700">

            <div class="text-center">
                <h2 class="text-3xl font-bold text-white">
                    TOTAL: {{ number_format($totalPrice, 2) }} SYP
                </h2>
            </div>

            <div class="flex justify-center gap-4 mt-6">
                <button wire:click="completeSale"
                        class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition">
                        إتمام البيع وحفظ الفاتورة
                </button>
                <button wire:click="resetScanner"
                        class="px-8 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition">
                        تفريغ / فاتورة جديدة
                </button>
            </div>
        </div>

        <!-- رفع صورة أو تشغيل الكاميرا -->
        <div class="p-6 bg-gray-800 rounded-xl border border-green-500 shadow-lg text-center space-y-4">
            <label class="block text-white font-bold text-lg">رفع صورة الباركوود:</label>
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
                            @this.call('searchProduct', decodedText).then(() => {
                                setTimeout(() => { isProcessing = false; }, 1500);
                            });
                        })
                        .catch(err => {
                            isProcessing = false;
                            alert("لم يتم العثور على باركوود واضح في الصورة.");
                        });
                });

                const config = { fps: 5, qrbox: { width: 300, height: 150 } };
                html5QrCode.start({ facingMode: "environment" }, config, (decodedText) => {
                    if (!isProcessing) {
                        isProcessing = true;
                        @this.call('searchProduct', decodedText).then(() => {
                            setTimeout(() => { isProcessing = false; }, 1500);
                        });
                    }
                }).catch(err => console.warn("الكاميرا غير متاحة"));
            });
        </script>
    </div>
</x-filament-panels::page>