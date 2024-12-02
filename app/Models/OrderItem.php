<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * Define que todos los atributos pueden ser asignados de forma masiva en la base de datos
     *
     * @var array
     */
    protected $guarded = [];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function variation(): BelongsTo
    {
        return $this->belongsTo(ItemVariation::class, 'item_variation_id');
    }

    public function toArray()
    {
        $data = parent::toArray();
        if ($this->item != null) {
            $data['item'] = [
                'id' => $this->item->id,
                'name' => $this->item->name,
                'image' => $this->item->image,
                'aux_image' => $this->item->aux_image,
                'local_price' => $this->item->local_price,
                'local_offer' => $this->item->local_offer,
                'foreign_price' => $this->item->foreign_price,
                'foreign_offer' => $this->item->foreign_offer,
                'delivery' => $this->item->delivery,
                'category' => $this->item->category,
            ];
        } else {
            $data['item'] = null;
        }
        if ($this->variation != null) {
            $data['variation'] = [
                'id' => $this->variation->id,
                'type' => $this->variation->type,
                'name' => $this->variation->name,
            ];
        } else {
            $data['variation'] = null;
        }
        return $data;
    }
}
