<?php

namespace Modules\Comment\App\Http\Controllers\Front;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\Comment\App\Models\Comment;
use Modules\Comment\App\Services\Front\CommentReactionService;

class CommentReactionController
{
    public function __invoke(Request $request, Comment $comment, CommentReactionService $reactions): JsonResponse|RedirectResponse
    {
        $data = $request->validate(['reaction' => ['present', 'nullable', 'string', Rule::in(array_keys(CommentReactionService::OPTIONS))]]);
        $article = $reactions->set($comment, $request, $data['reaction']);
        if ($request->expectsJson()) {
            return response()->json($reactions->summaries(collect([$comment]), $request)[$comment->id]);
        }

        return redirect($article->getUrl().'#comment-'.$comment->id);
    }
}
