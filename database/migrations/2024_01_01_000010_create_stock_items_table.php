<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('unit')->default('un');
            $table->decimal('quantity', 10, 3)->default(0);
            $table->decimal('min_quantity', 10, 3)->default(0);
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->string('supplier')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['entrada', 'saida', 'ajuste'])->default('entrada');
            $table->decimal('quantity', 10, 3);
            $table->decimal('quantity_before', 10, 3)->default(0);
            $table->decimal('quantity_after', 10, 3)->default(0);
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('stock_items');
    }
};
