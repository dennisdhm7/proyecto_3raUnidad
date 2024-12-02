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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            /**
             * Número de orden
             */
            $table->string('order_id')->unique();
            /**
             * Usuario que registró esta orden
             */
            $table->foreignId('user_id')->nullable()->references('id')->on('users')->onDelete('set null');
            /**
             * Cliente que registró esta orden
             */
            $table->foreignId('client_id')->nullable()->references('id')->on('clients')->onDelete('set null');
            /**
             * Tipo de pedido
             */
            $table->enum('type', ['Delivery', 'Local', 'Sale']);
            /**
             * Método de pago
             */
            $table->enum('payment_method', ['Cash', 'Card']);
            /**
             * Total pagado
             */
            $table->decimal('total');
            /**
             * Vuelto
             */
            $table->decimal('rest')->default(0);
            /**
             * Dirección del pedido
             */
            $table->text('address')->nullable()->default(null);
            /**
             * Nombre del cliente
             */
            $table->string('client')->nullable()->default(null);
            /**
             * Teléfono del cliente
             */
            $table->string('phone')->nullable()->default(null);
            /**
             * Email del cliente
             */
            $table->string('email')->nullable()->default(null);
            /**
             * Estado del pedido
             */
            $table->enum('status', ['Created', 'Accepted', 'Prepared', 'Paid', 'Delievered', 'Cancelled']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
