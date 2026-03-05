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
            $table->string('loan_code')->unique(); // Kode peminjaman (PJM/2024/001)
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Peminjam
            $table->date('loan_date'); // Tanggal pinjam
            $table->date('return_date'); // Tanggal rencana kembali
            $table->date('actual_return_date')->nullable(); // Tanggal kembali sebenarnya
            $table->text('purpose')->nullable(); // Tujuan peminjaman
            $table->enum('status', ['pending', 'approved', 'rejected', 'borrowed', 'returned', 'overdue'])->default('pending');
            $table->text('notes')->nullable(); // Catatan admin
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete(); // Disetujui oleh
            $table->timestamp('approved_at')->nullable();
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
