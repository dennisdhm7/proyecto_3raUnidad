<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPermission extends Model
{
    use HasFactory;

    /**
     * Define que todos los atributos pueden ser asignados de forma masiva en la base de datos
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Obtener el usuario del permiso de usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener el permiso del permiso de usuario
     */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }
}
