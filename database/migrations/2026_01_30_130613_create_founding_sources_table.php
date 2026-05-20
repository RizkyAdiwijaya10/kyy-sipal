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
            $table->string('name'); 
            $table->string('code')->nullable(); 
            $table->integer('year')->nullable(); 
            $table->text('description')->nullable();
            $table->timestamps();
        });
        
    }


    public function down(): void
    {
        Schema::dropIfExists('founding_sources');
    }
};
