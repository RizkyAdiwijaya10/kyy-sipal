<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('funding_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama sumber dana
            $table->string('code')->nullable(); // Kode anggaran
            $table->integer('year')->nullable(); // Tahun anggaran
            $table->text('description')->nullable();
            $table->timestamps();
        });
        
    }


    public function down(): void
    {
        Schema::dropIfExists('founding_sources');
    }
};
