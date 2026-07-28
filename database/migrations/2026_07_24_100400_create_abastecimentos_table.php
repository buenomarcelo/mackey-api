<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abastecimentos', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('motorista_id')->constrained('motoristas');
            $table->foreignId('caminhao_id')->constrained('caminhoes');
            $table->date('data');
            $table->unsignedInteger('km')->nullable();
            $table->decimal('litragem', 10, 2);
            $table->decimal('valor_litro', 8, 3);
            $table->decimal('valor_enviado', 12, 2);
            $table->string('posto');
            $table->decimal('valor_sobrando', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abastecimentos');
    }
};
