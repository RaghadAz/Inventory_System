<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('ai_forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->integer('expected_demand');
            $table->date('forecast_date');
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('ai_forecasts');
    }
};
