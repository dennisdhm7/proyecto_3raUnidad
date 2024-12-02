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
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
            /**
             * Id del usuario del permiso de usuario
             */
            $table->foreignId('user_id')->nullable()->references('id')->on('users')->onDelete('set null');
            /**
             * Id del permiso del permiso de usuario
             */
            $table->foreignId('permission_id')->nullable()->references('id')->on('permissions')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
    }
};
