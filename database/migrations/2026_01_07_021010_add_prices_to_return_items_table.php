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
        Schema::table('return_items', function (Blueprint $table) {
        $table->decimal('sell_price', 10, 2)->default(0)->after('qty');
        $table->decimal('dealer_price', 10, 2)->default(0)->after('sell_price');
        $table->decimal('line_total', 10, 2)->default(0)->after('dealer_price');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_items', function (Blueprint $table) {
            //
        });
    }
};
