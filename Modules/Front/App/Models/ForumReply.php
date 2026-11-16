<?php

namespace Modules\Front\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\User\App\Models\User;

class ForumReply extends Model
{
    use SoftDeletes;

    protected $fillable = ['body'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
