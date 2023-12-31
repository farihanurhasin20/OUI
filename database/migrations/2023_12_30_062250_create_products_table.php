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
            $table->string('title');
            $table->string('barcode');
            $table->string('qty')->nullable();
            $table->string('size');
            $table->string('type');
            $table->string('price');
            $table->foreignId('unit_id')->nullable()->constrained('units');
            $table->foreignId('color_id')->nullable()->constrained('colors');
            $table->string('image');
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
