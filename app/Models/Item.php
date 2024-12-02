<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    /**
     * Define que todos los atributos pueden ser asignados de forma masiva en la base de datos
     *
     * @var array
     */
    protected $guarded = [];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variations(): HasMany
    {
        return $this->hasMany(ItemVariation::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    static function getSeoURL($string)
    {
        // Convertir a minúsculas
        $string = strtolower($string);
        // Reemplazar caracteres especiales
        $string = preg_replace('/[^a-z0-9\s\-áéíóúñ]/', '', $string);
        // Reemplazar espacios por guiones
        $string = preg_replace('/[\s-]+/', '-', $string);
        // Eliminar guiones al inicio y al final
        $string = trim($string, '-');
        //Reemplazar acentos por letras sin acentos
        $words = [
            ['á', 'a'],
            ['é', 'e'],
            ['í', 'i'],
            ['ó', 'o'],
            ['u', 'u'],
            ['ñ', 'nh'],
        ];
        foreach ($words as $word) {
            $string = str_replace($word[0], $word[1], $string);
        }
        return $string;
    }

    public function toArray()
    {
        $data = parent::toArray();
        if ($this->image != null) {
            $data['image'] = asset($this->image);
        }
        if ($this->aux_image != null) {
            $data['aux_image'] = asset($this->aux_image);
        }
        $data['category'] = $this->category;
        $data['variations'] = $this->variations;
        return $data;
    }
}
