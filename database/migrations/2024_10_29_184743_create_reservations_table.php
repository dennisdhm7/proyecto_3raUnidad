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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            /**
             * Identificador de la reservación para el QR
             */
            $table->string('reservation_id');
            /**
             * Nombre del cliente que realizó la reservación
             */
            $table->string('client');
            /**
             * Cliente que registró esta orden
             */
            $table->foreignId('client_id')->nullable()->references('id')->on('clients')->onDelete('set null');
            /**
             * Cantidad de clientes
             */
            $table->integer('client_quantity');
            /**
             * Fecha y hora de la reservación
             */
            $table->dateTime('reservation_datetime');
            /**
             * Tiempo total de la reservación (en minutos)
             */
            $table->integer('reservation_time')->default(45);
            /**
             * True si se ha confirmado la reserva
             */
            $table->enum('status', ['Created', 'Confirmed', 'Cancelled'])->default('Created');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
