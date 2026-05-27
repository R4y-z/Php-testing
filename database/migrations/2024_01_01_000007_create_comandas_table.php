<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comandas', function (Blueprint $table) {
            $table->id();
            $table->string('number', 20)->unique();
            $table->foreignId('table_id')->nullable()->constrained('restaurant_tables')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('opened_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_name')->nullable();
            $table->enum('status', ['aberta', 'fechamento', 'finalizada', 'cancelada'])->default('aberta');
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('comanda_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comanda_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('added_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('product_name');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('quantity', 10, 3)->default(1);
            $table->enum('unit', ['un', 'kg'])->default('un');
            $table->decimal('total', 10, 2);
            $table->text('notes')->nullable();
            $table->enum('status', ['pendente', 'preparando', 'pronto', 'cancelado'])->default('pendente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comanda_items');
        Schema::dropIfExists('comandas');
    }
};
