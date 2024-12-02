<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            /**
             * Nombre de la moneda (inglés)
             */
            $table->string('en_name');
            /**
             * Nombre de la moneda (español)
             */
            $table->string('es_name')->default('');
            /**
             * Símbolo de la moneda
             */
            $table->string('symbol', 5);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
