<?php

namespace Modules\Comment\App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
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

    public function store(CommentRequest $request): RedirectResponse
    {
        return $this->discussionRedirect($this->commentService->store($request), 'Opublikowano komentarz.');
    }

    public function update(CommentRequest $request, Comment $comment): RedirectResponse
    {
        return $this->discussionRedirect($this->commentService->update($request, $comment), 'Zapisano zmiany.');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->delete();

        return redirect($comment->commentable->getUrl().'#comments')
            ->with('discussion_status', __('comment::messages.comment_deleted_successfully'));
    }

    public function reply(CommentRequest $request, Comment $comment): RedirectResponse
    {
        return $this->discussionRedirect($this->commentService->store($request, $comment), 'Opublikowano odpowiedź.');
    }

    private function discussionRedirect(Comment $comment, string $publishedMessage): RedirectResponse
    {
        return redirect($comment->commentable->getUrl().'#comments')
            ->with('discussion_status', $comment->isApproved() ? $publishedMessage : 'Zapisano. Pojawi się po sprawdzeniu przez redakcję.');
    }
}
