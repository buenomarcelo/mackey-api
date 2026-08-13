<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('viagens', function (Blueprint $table) {
            $table->decimal('valor_adicional', 12, 2)->default(0)->after('pedagio');
        });
    }

    public function down(): void
    {
        Schema::table('viagens', function (Blueprint $table) {
            $table->dropColumn('valor_adicional');
        });
    }
};
