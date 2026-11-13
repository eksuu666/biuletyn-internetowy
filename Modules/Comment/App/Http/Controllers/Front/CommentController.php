<?php

namespace Modules\Comment\App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Comment\App\Http\Requests\Front\CommentRequest;
use Modules\Comment\App\Models\Comment;
use Modules\Comment\App\Services\Front\CommentService;

class CommentController extends Controller
{
    public function __construct(private readonly CommentService $commentService)
    {
        $this->middleware('auth')->except(['store', 'reply']);
        $this->authorizeResource(Comment::class, 'comment', [
            'except' => ['store', 'reply'],
        ]);
    }

    public function store(CommentRequest $request): RedirectResponse|JsonResponse
    {
        return $this->discussionResponse($request, $this->commentService->store($request), 'Opublikowano komentarz.');
    }

    public function update(CommentRequest $request, Comment $comment): RedirectResponse|JsonResponse
    {
        return $this->discussionResponse($request, $this->commentService->update($request, $comment), 'Zapisano zmiany.');
    }

    public function destroy(Request $request, Comment $comment): RedirectResponse|JsonResponse
    {
        $comment->delete();

        return $this->discussionResponse($request, $comment, __('comment::messages.comment_deleted_successfully'));
    }

    public function reply(CommentRequest $request, Comment $comment): RedirectResponse|JsonResponse
    {
        return $this->discussionResponse($request, $this->commentService->store($request, $comment), 'Opublikowano odpowiedź.');
    }

    private function discussionResponse(Request $request, Comment $comment, string $publishedMessage): RedirectResponse|JsonResponse
    {
        $article = $comment->commentable;
        $visible = ! $comment->trashed() && $comment->isApproved();
        $message = $visible || $comment->trashed() ? $publishedMessage : 'Czeka na moderację.';

        if ($request->expectsJson()) {
            $threads = $article->publicCommentThreads();

            return response()->json([
                'html' => view('front::single-article.partials.comment-threads', compact('article', 'threads'))->render(),
                'count' => $threads->sum(fn ($thread) => 1 + $thread['replies']->count()),
                'commentId' => $visible ? $comment->id : null,
                'message' => $message,
            ]);
        }

        return redirect($article->getUrl().'#comments')->with('discussion_status', $message);
    }
}
