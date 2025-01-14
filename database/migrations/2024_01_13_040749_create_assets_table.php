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
            $table->string('asset_name');
            $table->string('brand_name');
            $table->string('QR_code')->nullable();
            $table->string('Unit_Price')->nullable();
            $table->string('Current_Spend')->nullable();
            $table->string('Max_Spend')->nullable();
            $table->string('range')->nullable();
            $table->string('location')->nullable();
            $table->string('manufacture_sno')->nullable();
            $table->string('manufacture_date')->nullable();
            $table->string('installation_date')->nullable();
            $table->string('warranty_date')->nullable();
            $table->string('service_contract')->nullable();
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
