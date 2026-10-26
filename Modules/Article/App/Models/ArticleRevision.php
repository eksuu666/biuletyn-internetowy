<?php

namespace Modules\Article\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\App\Models\User;

class ArticleRevision extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['snapshot' => 'array'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
