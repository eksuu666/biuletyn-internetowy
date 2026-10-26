<?php

namespace Modules\Comment\App\Services\Front;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Modules\Article\App\Models\Article;
use Modules\Comment\App\Http\Requests\Front\CommentRequest;
use Modules\Comment\App\Models\Comment;

class CommentService
{
    public function store(CommentRequest $request): Model
    {
        $model = Article::publiclyVisible()->findOrFail($request->commentable_id);
        $comment = Comment::make(['comment' => $request->comment, 'status' => Comment::PENDING]);
        $this->setCommenter($request, $comment);
        $comment->commentable()->associate($model);
        $comment->save();

        return $comment;
    }

    public function setCommenter(CommentRequest $request, Comment $comment): Model
    {
        if (! Auth::check()) {
            $comment->setGuestData($request->guest_name, $request->guest_email);
        } else {
            $comment->commenter()->associate(Auth::user());
        }

        return $comment;
    }

    public function reply(CommentRequest $request, Comment $comment): Model
    {
        $model = Article::publiclyVisible()->findOrFail($request->commentable_id);
        abort_unless($comment->commentable_type === Article::class && (int) $comment->commentable_id === $model->id && $comment->status === Comment::APPROVED, 404);
        $child_comment = Comment::make(['comment' => $request->comment, 'status' => Comment::PENDING]);
        $child_comment->parent()->associate($comment);
        $this->setCommenter($request, $child_comment);
        $child_comment->commentable()->associate($model);
        $child_comment->save();

        return $child_comment;
    }
}
