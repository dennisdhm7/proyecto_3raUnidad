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
        Schema::create('item_variations', function (Blueprint $table) {
            $table->id();
            /**
             * Item al que pertenece esta variación
             */
            $table->foreignId('item_id')->nullable()->references('id')->on('items')->onDelete('set null');
            /**
             * Tipo de variación (para agrupar)
             */
            $table->string('type');
            /**
             * Nombre de la variación
             */
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_variations');
    }
};
