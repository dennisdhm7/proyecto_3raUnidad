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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            /**
             * Nombre del usuario que hizo la reseña
             */
            $table->string('user');
            /**
             * Texto de la reseña
             */
            $table->string('review')->nullable()->default(null);
            /**
             * Número de estrellas
             */
            $table->string('rating')->default(5);
            /**
             * Item al que pertenece esta reseña
             */
            $table->foreignId('item_id')->nullable()->references('id')->on('items')->onDelete('set null');
            /**
             * Correo electrónico del cliente
             */
            $table->string('email');
            /**
             * True si es visible
             */
            $table->boolean('visible')->default(false);
            /**
             * Dirección IP del usuario que comenta
             */
            $table->string('ip');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
