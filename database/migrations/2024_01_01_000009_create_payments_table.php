<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->morphs('payable'); // order ou comanda
            $table->foreignId('cash_session_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('method', ['dinheiro', 'pix', 'cartao_credito', 'cartao_debito', 'misto'])->default('dinheiro');
            $table->enum('status', ['pendente', 'aprovado', 'estornado', 'cancelado'])->default('pendente');
            $table->decimal('amount', 10, 2);
            $table->decimal('change_amount', 10, 2)->default(0);
            $table->decimal('cash_received', 10, 2)->default(0);
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
