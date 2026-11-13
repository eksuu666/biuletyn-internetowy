<?php

namespace Modules\Comment\App\Services\Front;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Modules\Article\App\Models\Article;
use Modules\Comment\App\Http\Requests\Front\CommentRequest;
use Modules\Comment\App\Models\Comment;

class CommentService
{
    public const REVIEW_LENGTH = 500;

    public function store(CommentRequest $request, ?Comment $parent = null): Comment
    {
        $model = Article::publiclyVisible()->findOrFail($request->commentable_id);
        if ($parent) {
            abort_unless($parent->commentable_type === $model->getMorphClass()
                && (int) $parent->commentable_id === $model->id && $parent->isApproved(), 404);
            $ancestor = $parent;
            while ($ancestor->parent_id !== null) {
                $ancestor = $ancestor->approvedParent()->first();
                abort_if($ancestor === null, 404);
            }
        }

        $comment = Comment::make(['comment' => $request->comment, 'status' => $this->statusFor($request->comment)]);
        $this->setCommenter($request, $comment);
        $comment->commentable()->associate($model);
        if ($parent) {
            $comment->parent()->associate($parent);
        }
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

    public function update(CommentRequest $request, Comment $comment): Comment
    {
        $model = Article::publiclyVisible()->findOrFail($request->commentable_id);
        abort_unless($comment->commentable_type === $model->getMorphClass() && (int) $comment->commentable_id === $model->id, 404);
        $comment->update([
            'comment' => $request->comment,
            'status' => $comment->isApproved() ? $this->statusFor($request->comment) : Comment::PENDING,
        ]);

        return $comment;
    }

    private function statusFor(string $text): string
    {
        return mb_strlen($text) > self::REVIEW_LENGTH || preg_match_all('~(?:https?://|www\.)\S+~iu', $text) >= 2
            ? Comment::PENDING
            : Comment::APPROVED;
    }
}
