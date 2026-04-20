<?php 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('service_notes', function (Blueprint $table) {
      $table->id();

      $table->string('service_no')->unique(); // e.g. SN-000001
      $table->date('service_date')->nullable();

      // Customer box
      $table->string('customer_name')->nullable();
      $table->string('customer_address')->nullable();
      $table->string('customer_tel')->nullable();

      // Item grid
      $table->string('item')->nullable();
      $table->string('serial_no')->nullable();
      $table->string('invoice_no')->nullable();
      $table->string('details')->nullable();

      // Complains
      $table->text('customer_complains')->nullable();

      // Good Return Note section
      $table->string('received_service_item')->nullable();
      $table->string('grn_customer_name')->nullable();
      $table->date('grn_date')->nullable();

      $table->timestamps();
    });
  }

  public function down(): void {
    Schema::dropIfExists('service_notes');
  }
};
