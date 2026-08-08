<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('category_id')->constrained('categories');
            $table->string('name');
            $table->string('sku')->nullable();
            $table->string('barcode')->nullable();
            $table->decimal('cost_price', 15)->default(0);
            $table->decimal('price', 15)->default(0);
            $table->integer('quantity');
            $table->integer('alert_threshold')->default(10);
            $table->string('image')->nullable();
            $table->decimal('purchase_price', 15)->default(0);
            $table->decimal('selling_price', 15)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
