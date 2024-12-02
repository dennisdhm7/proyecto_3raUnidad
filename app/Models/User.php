<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword;

class User extends Authenticatable implements MustVerifyEmail, CanResetPassword
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'image',
        'name',
        'phone',
        'email',
        'password',
        'last_access',
        'password_restore_code',
        'is_admin',
        'active',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'password_restore_code',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions', 'user_id', 'permission_id');
    }

    /**
     * Verifica si el usuario está autorizado a realizar una acción determinada en el sistema
     *
     * @param string $action
     * @return bool True si el usuario tiene acceso al permiso o es administrador
     */
    public function isAuthorized($action): bool
    {
        if ($this->is_admin) {
            return true;
        } else {
            foreach ($this->permissions as $permission) {
                if ($permission->name == $action) {
                    return true;
                }
            }
        }
        return false;
    }

    public function toArray()
    {
        $data = parent::toArray();
        if ($this->image != null) {
            $data['image'] = asset($this->image);
        }
        $data['permissions'] = $this->permissions;
        return $data;
    }
}
