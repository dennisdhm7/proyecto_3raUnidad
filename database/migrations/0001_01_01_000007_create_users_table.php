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
        Schema::create('users', function (Blueprint $table) {
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
             * Fecha de verificación del email
             */
            $table->timestamp('email_verified_at')->nullable();
            /**
             * Contraseña
             */
            $table->string('password');
            /**
             * True si es el administrador principal
             */
            $table->boolean('is_admin')->default(false);
            /**
             * Rol del usuario
             */
            $table->enum('role', ['Administrator', 'Manager', 'Waiter', 'Grocer', 'Other'])->default(false)->default('Other');
            /**
             * Fecha y hora del último acceso
             */
            $table->dateTime('last_access')->nullable()->default(null);
            /**
             * True si está activa esta cuenta
             */
            $table->boolean('active')->default(true);
            $table->rememberToken();
            /**
             * Código para restablecer la contraseña
             */
            $table->string('password_restore_code')->nullable()->default(null);
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
