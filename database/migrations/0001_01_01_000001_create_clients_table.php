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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            /**
             * Imagen de perfil
             */
            $table->string('image')->nullable()->default(null);
            /**
             * Nombre del usuario
             */
            $table->string('name');
            /**
             * Teléfono del usuario
             */
            $table->string('phone')->nullable()->default(null);
            /**
             * Correo electrónico
             */
            $table->string('email')->unique();
            /**
             * Contraseña
             */
            $table->string('password');
            /**
             * Fecha y hora del último acceso
             */
            $table->dateTime('last_access')->nullable()->default(null);
            /**
             * True si está activa esta cuenta
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
        Schema::dropIfExists('clients');
    }
};
