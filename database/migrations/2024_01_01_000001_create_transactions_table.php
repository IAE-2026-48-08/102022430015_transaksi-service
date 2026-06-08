<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('account_id');           // ID rekening nasabah
            $table->enum('type', ['credit', 'debit']); // jenis transaksi
            $table->decimal('amount', 15, 2);       // nominal transaksi
            $table->string('description')->nullable(); // keterangan
            $table->string('reference_number')->unique(); // nomor referensi unik
            $table->timestamp('transaction_date');
            $table->timestamps();
        });

        Schema::create('repayments', function (Blueprint $table) {
            $table->id();
            $table->string('account_id');
            $table->decimal('loan_amount', 15, 2);      // total pinjaman
            $table->decimal('repayment_amount', 15, 2); // nominal cicilan yang dibayar
            $table->integer('installment_number');      // cicilan ke berapa
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
            $table->timestamp('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repayments');
        Schema::dropIfExists('transactions');
    }
};
