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
        Schema::create('quatations', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('sheet_id')->constrained('inspection_sheets')->onDelete('cascade');
            $table->decimal('cost',8,2)->nullable();
            $table->string('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quatations');
    }
};
