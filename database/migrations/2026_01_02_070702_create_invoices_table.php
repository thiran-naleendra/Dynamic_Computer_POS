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
        Schema::create('invoices', function (\Illuminate\Database\Schema\Blueprint $table) {
    $table->id();
    $table->string('invoice_no')->unique();
    $table->string('shop_name')->default('Dynamic computer system');
    $table->string('customer_name')->nullable();
    $table->date('invoice_date');
    $table->decimal('sub_total', 10, 2)->default(0);
    $table->decimal('grand_total', 10, 2)->default(0);
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
