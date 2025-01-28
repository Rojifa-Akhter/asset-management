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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('brand');
            $table->string('range')->nullable();
            $table->string('product');
            $table->string('qr_code')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('external_serial_number')->nullable();
            $table->date('manufacturing_date')->nullable();
            $table->date('installation_date')->nullable();
            $table->date('warranty_end_date')->nullable();
            $table->string('unit_price')->nullable();
            $table->string('max_spend')->nullable();
            $table->boolean('fitness_product')->nullable();
            $table->boolean('has_odometer')->nullable();
            $table->string('location')->nullable();
            $table->string('residual_price')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
