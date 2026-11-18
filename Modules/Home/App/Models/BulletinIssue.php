<?php

namespace Modules\Home\App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BulletinIssue extends Model
{
    protected $fillable = ['title', 'description', 'pdf_path', 'cover_path', 'published_on', 'is_published'];

    protected $casts = ['published_on' => 'date', 'is_published' => 'boolean'];

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true)->whereDate('published_on', '<=', today()->toDateString());
    }

    public function coverUrl(): string
    {
        return Storage::disk('public')->url($this->cover_path);
    }

    public function pdfUrl(): string
    {
        return route('bulletins.pdf', $this);
    }
}
