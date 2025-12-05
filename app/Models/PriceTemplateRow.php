<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceTemplateRow extends Model
{
    protected $fillable = [
        'price_template_id',
        'row_label',
        'price',
    ];

    // A row belongs to a price template
    public function template()
    {
        return $this->belongsTo(PriceTemplate::class, 'price_template_id');
    }
}
