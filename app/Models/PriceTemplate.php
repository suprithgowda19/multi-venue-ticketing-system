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
    ];

    // A template has many row prices
    public function rows()
    {
        return $this->hasMany(PriceTemplateRow::class);
    }
}
