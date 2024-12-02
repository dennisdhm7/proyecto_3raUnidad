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
        Schema::create('reservation_tables', function (Blueprint $table) {
            $table->id();
            /**
             * Reservación a la que pertenece esta mesa de reservación
             */
            $table->foreignId('reservation_id')->nullable()->references('id')->on('reservations')->onDelete('set null');
            /**
             * Mesa de esta reservación de mesa
             */
            $table->foreignId('table_id')->nullable()->references('id')->on('tables')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_tables');
    }
};
