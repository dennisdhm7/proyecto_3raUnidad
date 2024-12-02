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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            /**
             * Imagen del producto (formato WEBP)
             */
            $table->string('image')->nullable()->default(null);
            /**
             * Imagen auxiliar del producto (formatos JPG o PNG)
             */
            $table->string('aux_image')->nullable()->default(null);
            /**
             * Nombre del producto
             */
            $table->string('name');
            /**
             * Descripción del producto
             */
            $table->text('description');
            /**
             * Características del producto
             */
            $table->text('features')->nullable()->default(null);
            /**
             * Categoría a la que pertenece este producto
             */
            $table->foreignId('category_id')->nullable()->references('id')->on('categories')->onDelete('set null');
            /**
             * Precio del producto (local)
             */
            $table->decimal('local_price');
            /**
             * Precio de oferta del producto (local)
             */
            $table->decimal('local_offer')->nullable()->default(null);
            /**
             * Precio del producto (extranjero)
             */
            $table->decimal('foreign_price')->nullable()->default(null);
            /**
             * Precio de oferta  del producto (extranjero)
             */
            $table->decimal('foreign_offer')->nullable()->default(null);
            /**
             * Likes del producto
             */
            $table->integer('likes')->default(0);
            /**
             * True si es una comida vegana
             */
            $table->boolean('vegan')->default(false);
            /**
             * True si es una comida vegana
             */
            $table->boolean('delivery')->default(true);
            /**
             * True si el item está activo
             */
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
