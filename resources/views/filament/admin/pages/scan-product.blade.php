<x-filament-panels::page>
    <div class="space-y-6" dir="rtl">

        <div class="p-4 bg-gray-800 rounded-xl border border-green-500 shadow-lg">
            <input type="text" wire:model.defer="barcode" placeholder="Enter barcode here..."
                wire:keydown.enter="searchProduct(barcode)"
                class="w-full text-center text-xl p-3 rounded bg-white text-black" />
        </div>

        <div class="p-6 bg-gray-800 rounded-xl border border-green-500 shadow-lg">
            @if ($lastScannedProduct)
                <div class="text-center">
                    <h3 class="text-2xl font-bold text-white mb-2">
                        {{ $lastScannedProduct->name }}
                    </h3>
                    <p class="text-lg text-green-400">
                        Price: {{ number_format($lastScannedProduct->price, 2) }}SYP
                    </p>
                </div>
            @else
                <div class="text-center text-gray-400 py-4">
                    <p>Waiting for a new scan...</p>
                </div>
            @endif

            <hr class="my-6 border-gray-700">

            <div class="text-center">
                <h2 class="text-3xl font-bold text-white">
                    Total: {{ number_format($totalPrice, 2) }}SYP
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
                @this.call('searchProduct', decodedText);
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

                    @this.call('searchProduct', code);

                    Quagga.stop();
                });

                window.addEventListener('refresh-page', () => {
                    Quagga.start();
                });
            });
        </script>
    </div>
</x-filament-panels::page>
