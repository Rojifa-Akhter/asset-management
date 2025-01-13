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
            $table->string('QR_code');
            $table->string('Unit_Price');
            $table->string('Current_Spend');
            $table->string('Max_Spend');
            $table->string('range');
            $table->string('location');
            $table->string('manufacture_sno');
            $table->string('manufacture_date');
            $table->string('installation_date');
            $table->string('warranty_date');
            $table->string('service_contract');
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
