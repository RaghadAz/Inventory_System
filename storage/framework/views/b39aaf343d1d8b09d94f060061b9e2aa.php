<?php if (isset($component)) { $__componentOriginald2aa9f7b74553621bdcc3c69267ff328 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald2aa9f7b74553621bdcc3c69267ff328 = $attributes; } ?>
<?php $component = Filament\View\LegacyComponents\PageComponent::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Filament\View\LegacyComponents\PageComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: Arial;
        }

        .box {
            width: 70%;
            margin: auto;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .card {
            background: #3498db;
            padding: 20px;
            margin: 15px;
            border-radius: 10px;
            font-size: 22px;
        }

        .alert {
            background: red;
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
        }
    </style>

    <div class="box dark:bg-gray-900 dark:text-gray-100">

        <h1 class="text-center text-gray-800 dark:text-gray-100">
            📦 Smart Inventory Forecast
        </h1>

        <div class="bg-white dark:bg-gray-800 dark:text-gray-100"
            style="padding:20px;margin:15px;border-radius:12px;box-shadow:0 3px 10px rgba(0,0,0,.2);">
            <b>📊 Average Sales Per Day</b>
            <br><br>
            <?php echo e($this->data['avg']); ?>

        </div>

        <div class="bg-[#fff8dc] dark:bg-yellow-900 dark:text-gray-100"
            style="padding:20px;margin:15px;border-radius:12px;box-shadow:0 3px 10px rgba(0,0,0,.2);">
            <b>🧠 AI Status</b>
            <br><br>
            <?php echo e($this->data['aiMessage']); ?>

        </div>

        <div class="bg-white dark:bg-gray-800 dark:text-gray-100"
            style="padding:20px;margin:15px;border-radius:12px;box-shadow:0 3px 10px rgba(0,0,0,.2);">
            <b>📦 Current Stock</b>
            <br><br>
            <?php echo e($this->data['stock']); ?>

        </div>
        <div class="bg-white dark:bg-gray-800 dark:text-gray-100"
            style="padding:20px;margin:15px;border-radius:12px;box-shadow:0 3px 10px rgba(0,0,0,.2);">

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->data['status'] == 'In Stock'): ?>
                <div class="bg-[#d4edda] dark:bg-green-900 dark:text-white"
                    style="padding:10px;margin:15px;border-radius:12px;">
                    <b>🟢 Stock Status</b>
                    <br><br>
                    <?php echo e($this->data['status']); ?>

                </div>
            <?php elseif($this->data['status'] == 'Running Low'): ?>
                <div class="bg-[#fff3cd] dark:bg-yellow-900 dark:text-white"
                    style="padding:20px;margin:5px;border-radius:12px;">
                    <b>🟡 Stock Status</b>
                    <br><br>
                    <?php echo e($this->data['status']); ?>

                </div>
            <?php else: ?>
                <div class="bg-[#f8d7da] dark:bg-red-900 dark:text-white"
                    style="padding:20px;margin:15px;border-radius:12px;">
                    <b>🔴 Stock Status</b>
                    <br><br>
                    <?php echo e($this->data['status']); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div class="bg-white dark:bg-gray-800 dark:text-gray-100"
            style="padding:20px;margin:15px;border-radius:12px;box-shadow:0 3px 10px rgba(0,0,0,.2);">
            <b>🔥 Forecast Risk</b>
            <br><br>
            <?php echo e($this->data['risk']); ?>

        </div>

        <div class="bg-[#e8f5e9] dark:bg-green-900 dark:text-gray-100"
            style="padding:20px;margin:15px;border-radius:12px;box-shadow:0 3px 10px rgba(0,0,0,.2);">
            <b>🤖 AI Prediction (Moving Average)</b>
            <br><br>
            <?php echo e($this->data['prediction']); ?>

        </div>

        <div class="bg-white dark:bg-gray-800 dark:text-gray-100"
            style="padding:20px;margin:15px;border-radius:12px;box-shadow:0 3px 10px rgba(0,0,0,.2);">
            <b>🛒 Recommended Reorder</b>
            <br><br>
            <?php echo e($this->data['product_name']); ?>: <?php echo e($this->data['recommendedOrder']); ?> Units
        </div>

        <div class="bg-white dark:bg-gray-800 dark:text-gray-100"
            style="padding:20px;margin:15px;border-radius:12px;box-shadow:0 3px 10px rgba(0,0,0,.2);">
            <b>📈 Sales Trend</b>
            <br><br>
            <?php echo e($this->data['trend']); ?>

        </div>

        <div class="bg-white dark:bg-gray-800 dark:text-gray-100"
            style="padding:20px;margin:15px;border-radius:12px;box-shadow:0 3px 10px rgba(0,0,0,.2);">
            <b>⏳ Expected Out Of Stock After</b>
            <br><br>
            <?php echo e($this->data['days']); ?> Days
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->data['alert']): ?>
            <div class="bg-[#ffebee] dark:bg-red-900 dark:text-white"
                style="padding:20px;margin:15px;border-radius:12px;border:2px solid red;">
                <b>🚨 Alert</b>
                <br><br>
                <?php echo e($this->data['alert']); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <h2 class="text-gray-800 dark:text-gray-100">Sales Analysis Chart</h2>

        <canvas id="salesChart" style="max-width:800px;margin:auto;"></canvas>

        <script>
            const salesData = <?php echo json_encode($this->data['salesData'], 15, 512) ?>;

            const labels = [];
            for (let i = 1; i <= salesData.length; i++) {
                labels.push("Sale " + i);
            }
            labels.push("Prediction");

            new Chart(document.getElementById('salesChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Actual Sales',
                            data: [...salesData, null],
                            borderWidth: 3,
                            fill: false,
                            tension: 0.3
                        },
                        {
                            label: 'AI Prediction',
                            data: [...Array(salesData.length).fill(null), <?php echo e($this->data['prediction']); ?>],
                            borderWidth: 3,
                            fill: false,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true
                        }
                    }
                }
            });
        </script>

    </div>

    <p class="text-gray-500 dark:text-gray-300 text-lg mt-4">
        🕒 Last Updated <br>
        <?php echo e($this->data['lastUpdated']); ?>

    </p>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald2aa9f7b74553621bdcc3c69267ff328)): ?>
<?php $attributes = $__attributesOriginald2aa9f7b74553621bdcc3c69267ff328; ?>
<?php unset($__attributesOriginald2aa9f7b74553621bdcc3c69267ff328); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald2aa9f7b74553621bdcc3c69267ff328)): ?>
<?php $component = $__componentOriginald2aa9f7b74553621bdcc3c69267ff328; ?>
<?php unset($__componentOriginald2aa9f7b74553621bdcc3c69267ff328); ?>
<?php endif; ?>
<?php /**PATH D:\laragon\www\ipi405\resources\views/filament/admin/pages/forecast.blade.php ENDPATH**/ ?>