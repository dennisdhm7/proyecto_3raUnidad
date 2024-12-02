<?php

namespace App\Models;

use App\Jobs\SendTelegramNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    /**
     * Define que todos los atributos pueden ser asignados de forma masiva en la base de datos
     *
     * @var array
     */
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toArray()
    {
        $data = parent::toArray();
        $data['user'] = $this->user;
        return $data;
    }
}
