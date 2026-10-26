<?php

namespace Modules\Newsletter\App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Newsletter extends Model
{
    use Searchable;

    protected $fillable = [
        'email',
        'subscribed_at',
        'confirmation_hash',
        'consented_at',
        'confirmed_at',
        'unsubscribed_at',
    ];

    protected $hidden = ['confirmation_hash'];

    protected $casts = ['confirmed_at' => 'datetime', 'unsubscribed_at' => 'datetime'];

    public function scopeConfirmed($query): void
    {
        $query->whereNotNull('confirmed_at')->whereNull('unsubscribed_at');
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->id,
            'email' => $this->email,
        ];
    }
}
