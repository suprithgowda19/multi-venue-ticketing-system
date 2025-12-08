<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceTemplate extends Model
{
    protected $fillable = [
        'name',
        'base_price',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'base_price' => 'float',
    ];

    public function rows()
    {
        return $this->hasMany(PriceTemplateRow::class);
    }

    public function getPricingMapAttribute()
    {
        return $this->rows->mapWithKeys(fn($row) => [
            $row->row_label => $row->price,
        ]);
    }
}
