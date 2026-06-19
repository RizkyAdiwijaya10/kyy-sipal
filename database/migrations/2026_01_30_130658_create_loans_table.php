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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_code')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('loan_date'); 
            $table->date('return_date'); 
            $table->date('actual_return_date')->nullable(); 
            $table->text('purpose')->nullable(); 
            $table->string('surat_path')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'borrowed', 'returned', 'overdue', 'cancelled'])->default('pending');
            $table->text('notes')->nullable(); 
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete(); 
            $table->timestamp('approved_at')->nullable(); 
            $table->timestamp('return_requested_at')->nullable(); 
            $table->text('return_request_notes')->nullable(); 
            $table->enum('return_request_status', ['pending', 'approved', 'rejected'])->nullable();
            $table->foreignId('return_approved_by')->nullable()->constrained('users')->nullOnDelete(); 
             $table->timestamp('return_approved_at')->nullable();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
