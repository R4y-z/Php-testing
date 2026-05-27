<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_tables', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->string('name')->nullable();
            $table->integer('capacity')->default(4);
            $table->enum('status', ['disponivel', 'ocupada', 'reservada', 'manutencao'])->default('disponivel');
            $table->string('location')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_tables');
    }
};
