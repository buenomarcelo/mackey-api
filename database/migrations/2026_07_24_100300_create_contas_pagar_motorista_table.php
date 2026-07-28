<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contas_pagar_motorista', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('motorista_id')->constrained('motoristas');
            $table->foreignId('viagem_id')->unique()->constrained('viagens');
            $table->decimal('valor_comissao', 12, 2);
            $table->string('status')->default('pendente');
            $table->date('data_pagamento')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contas_pagar_motorista');
    }
};
