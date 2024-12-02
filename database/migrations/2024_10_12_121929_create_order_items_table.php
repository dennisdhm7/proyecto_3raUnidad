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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            /**
             * Orden a la que pertenece este item
             */
            $table->foreignId('order_id')->nullable()->references('id')->on('orders')->onDelete('set null');
            /**
             * Producto o servicio de este item
             */
            $table->foreignId('item_id')->nullable()->references('id')->on('items')->onDelete('set null');
            /**
             * Variación de este item
             */
            $table->foreignId('item_variation_id')->nullable()->references('id')->on('item_variations')->onDelete('set null');
            /**
             * Precio
             */
            $table->decimal('price');
            /**
             * Cantidad
             */
            $table->integer('quantity');
            /**
             * Nota del pedido
             */
            $table->text('notes')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
