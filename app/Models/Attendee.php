<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Attendee extends Model
{
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'category',
        'pass_id',
        'qr_path',
        'is_verified',
        'meta',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'meta'        => 'array',
    ];

    /**
     * Generate a pretty, human-friendly festival Pass ID.
     * Example: BFFS-92KX-7QPA
     */
    public static function generatePassId(): string
    {
        do {
            $candidate = 'BFFS-' 
                . strtoupper(Str::random(4))
                . '-' 
                . strtoupper(Str::random(4));
        } while (self::where('pass_id', $candidate)->exists());

        return $candidate;
    }
}
