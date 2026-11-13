<?php

namespace Modules\Article\App\Services\Front;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Article\App\Models\Article;

class ArticleReactionService
{
    public const OPTIONS = [
        'like' => ['👍', 'Lubię to'],
        'love' => ['❤️', 'Uwielbiam'],
        'laugh' => ['😄', 'Rozbawiło mnie'],
        'wow' => ['😮', 'Zaskoczyło mnie'],
        'sad' => ['😢', 'Zasmuciło mnie'],
    ];

    public function summary(Article $article, Request $request): array
    {
        $counts = $article->likes()->selectRaw('reaction, COUNT(*) AS total')
            ->groupBy('reaction')->pluck('total', 'reaction');
        // Older anonymous likes were stored solely in the aggregate counter.
        $counts['like'] = ($counts['like'] ?? 0) + max(0, $article->likeCount - $counts->sum());
        $reader = $request->user()?->id ?? $request->session()->get('article_reaction_reader');

        return [
            'counts' => $counts,
            'selected' => $reader === null ? null : $article->likes()->where('user_id', $reader)->value('reaction'),
        ];
    }

    public function set(Article $article, Request $request, ?string $reaction): void
    {
        $reader = $request->user()?->id ?? $request->session()->get('article_reaction_reader');
        if ($reader === null && $reaction === null) {
            return;
        }
        if ($reader === null) {
            $reader = (string) Str::uuid();
            $request->session()->put('article_reaction_reader', $reader);
        }

        DB::transaction(function () use ($article, $reader, $reaction) {
            $locked = Article::publiclyVisible()->lockForUpdate()->findOrFail($article->id);
            if ($reaction === null) {
                $locked->unlike($reader);

                return;
            }

            $locked->like($reader);
            $locked->likes()->where('user_id', $reader)->update(['reaction' => $reaction]);
        }, 3);
    }
}
