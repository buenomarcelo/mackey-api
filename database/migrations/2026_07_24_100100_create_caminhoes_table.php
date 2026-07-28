<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caminhoes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('placa')->unique();
            $table->string('modelo');
            $table->string('marca');
            $table->unsignedSmallInteger('ano');
            $table->decimal('capacidade_carga', 10, 2);
            $table->string('renavam')->nullable();
            $table->string('cor')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caminhoes');
    }
};
