<?php

namespace Modules\Comment\App\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use Modules\Comment\App\Models\Comment;

trait HasComments
{
    public function publicCommentThreads(): Collection
    {
        $comments = $this->approvedComments()->with('commenter.image')->orderByDesc('id')->get()->groupBy('parent_id');

        return $comments->get('', collect())->map(function (Comment $root) use ($comments) {
            $replies = collect();
            $pending = [$root];
            while ($pending !== []) {
                $parent = array_pop($pending);
                foreach ($comments->get($parent->id, []) as $reply) {
                    $replies->push(['comment' => $reply, 'parent' => $parent]);
                    $pending[] = $reply;
                }
            }

            return ['root' => $root, 'replies' => $replies->sortBy('comment.id')];
        });
    }

    public function approvedComments(): MorphMany
    {
        return $this->comments()->where('status', Comment::APPROVED);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->latest();
    }
}
