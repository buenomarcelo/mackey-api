<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('viagens', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('motorista_id')->constrained('motoristas');
            $table->foreignId('caminhao_id')->constrained('caminhoes');
            $table->date('data');
            $table->string('origem');
            $table->string('destino');
            $table->string('contrato')->nullable();
            $table->decimal('peso', 10, 2);
            $table->decimal('frete', 12, 2);
            $table->decimal('entrada', 12, 2)->default(0);
            $table->decimal('valor_2', 12, 2)->default(0);
            $table->decimal('valor_3', 12, 2)->default(0);
            $table->decimal('pedagio', 12, 2)->default(0);
            $table->text('motivo')->nullable();
            $table->string('status_viagem')->default('agendada');
            $table->string('status_pagamento')->default('pendente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('viagens');
    }
};
