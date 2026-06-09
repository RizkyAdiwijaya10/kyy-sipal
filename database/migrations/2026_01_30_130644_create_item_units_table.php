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
        Schema::create('item_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->string('serial_number')->nullable();
            $table->string('inventory_code')->nullable(); 
            $table->enum('condition', ['baik','rusak','maintenance','hilang'])->default('baik');
            $table->enum('status', ['tersedia','dipinjam','dipesan','nonaktif'])->default('tersedia');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_units');
    }
};
