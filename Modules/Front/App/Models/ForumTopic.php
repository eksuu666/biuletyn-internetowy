<?php

namespace Modules\Front\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\User\App\Models\User;

class ForumTopic extends Model
{
    use SoftDeletes;

    public const SECTIONS = [
        'psy' => ['name' => 'Psy', 'intro' => 'Spacery, zabawa i życie z psem.'],
        'koty' => ['name' => 'Koty', 'intro' => 'Kocie zwyczaje i pomysły na wspólne życie.'],
        'adopcje' => ['name' => 'Adopcje', 'intro' => 'Pytania o adopcję, pierwsze dni i wsparcie.'],
        'opieka' => ['name' => 'Opieka', 'intro' => 'Pielęgnacja i codzienna opieka nad zwierzętami.'],
    ];

    protected $fillable = ['section', 'title', 'body'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ForumReply::class, 'topic_id');
    }
}
