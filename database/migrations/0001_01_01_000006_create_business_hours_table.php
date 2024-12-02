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
        Schema::create('business_hours', function (Blueprint $table) {
            $table->id();
            /**
             * Día de la semana
             */
            $table->integer('day');
            /**
             * Hora de inicio
             */
            $table->time('start')->nullable();
            /**
             * Hora de fin
             */
            $table->time('end')->nullable();
            /**
             * True si está cerrado todo el día
             */
            $table->boolean('closed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_hours');
    }
};
