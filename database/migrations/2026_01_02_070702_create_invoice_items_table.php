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
        Schema::create('invoice_items', function (\Illuminate\Database\Schema\Blueprint $table) {
    $table->id();
    $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
    $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
    $table->string('item_name');
    $table->string('serial_no')->nullable();
    $table->integer('qty')->default(1);
    $table->decimal('unit_price', 10, 2)->default(0);
    $table->decimal('line_total', 10, 2)->default(0);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
