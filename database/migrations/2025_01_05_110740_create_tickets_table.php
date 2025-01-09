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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('serial_no');
            $table->longText('problem');
            $table->string('location');
            $table->longText('comment');
            $table->string('ticket_no');
            $table->json('image')->nullable();
            $table->json('video')->nullable();
            $table->enum('status',['Check-in','In Progress','Check-out'])->default('In Progress');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
