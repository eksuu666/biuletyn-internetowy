<?php

namespace Modules\Comment\App\Services\Front;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Article\App\Models\Article;
use Modules\Article\App\Services\Front\ArticleReactionService;
use Modules\Comment\App\Models\Comment;
use Modules\Comment\App\Models\CommentReaction;

class CommentReactionService
{
    public const OPTIONS = ArticleReactionService::OPTIONS;

    public function summaries(Collection $comments, Request $request): array
    {
        $ids = $comments->pluck('id')->unique();
        if ($ids->isEmpty()) return [];
        $counts = CommentReaction::whereIn('comment_id', $ids)->selectRaw('comment_id, reaction, COUNT(*) AS total')
            ->groupBy('comment_id', 'reaction')->get()->groupBy('comment_id');
        $actor = $this->actor($request);
        $selected = $actor ? CommentReaction::whereIn('comment_id', $ids)->where('actor', $actor)->pluck('reaction', 'comment_id') : collect();

        return $ids->mapWithKeys(fn ($id) => [$id => [
            'counts' => array_replace(array_fill_keys(array_keys(self::OPTIONS), 0), $counts->get($id, collect())->pluck('total', 'reaction')->map(fn ($total) => (int) $total)->all()),
            'selected' => $selected->get($id),
        ]])->all();
    }

    public function set(Comment $comment, Request $request, ?string $reaction): Article
    {
        abort_if($request->user() && ! $request->user()->status, 403);
        return DB::transaction(function () use ($comment, $request, $reaction) {
            $locked = Comment::approved()->lockForUpdate()->findOrFail($comment->id);
            $article = $locked->commentable;
            abort_unless($article instanceof Article && $article->isPubliclyVisible(), 404);
            $ancestor = $locked;
            $seen = [$locked->id => true];
            while ($ancestor->parent_id !== null) {
                $ancestor = $ancestor->approvedParent()->first();
                abort_unless($ancestor && ! isset($seen[$ancestor->id]) && $ancestor->commentable_type === $locked->commentable_type && (int) $ancestor->commentable_id === (int) $locked->commentable_id, 404);
                $seen[$ancestor->id] = true;
            }
            $actor = $this->actor($request);
            if ($actor === null && $reaction === null) return $article;
            if ($actor === null) {
                $request->session()->put('comment_reaction_reader', (string) Str::uuid());
                $actor = $this->actor($request);
            }
            // Merge this browser's anonymous vote when its reader signs in.
            if ($request->user() && $request->session()->has('comment_reaction_reader')) {
                CommentReaction::where('comment_id', $locked->id)->where('actor', 'guest:'.$request->session()->get('comment_reaction_reader'))->delete();
            }
            if ($reaction === null) {
                CommentReaction::where('comment_id', $locked->id)->where('actor', $actor)->delete();
            } else {
                CommentReaction::updateOrCreate(['comment_id' => $locked->id, 'actor' => $actor], ['reaction' => $reaction]);
            }

            return $article;
        }, 3);
    }

    private function actor(Request $request): ?string
    {
        if ($request->user()) return 'user:'.$request->user()->id;
        $guest = $request->session()->get('comment_reaction_reader');
        return $guest ? 'guest:'.$guest : null;
    }
}
