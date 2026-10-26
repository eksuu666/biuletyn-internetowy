<?php

namespace Modules\Article\App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Article\App\Models\Article;
use Modules\User\App\Models\User;

class EditorialWorkflow
{
    public function isReviewer(User $user): bool
    {
        return $user->can('article::correct') || $user->can('article::publish');
    }

    public function canRead(User $user, Article $article): bool
    {
        return $user->can('article::index') && ($this->isReviewer($user) || $article->user_id === $user->id);
    }

    public function canEdit(User $user, Article $article): bool
    {
        if (! $user->can('article::update')) {
            return false;
        }
        if ($user->isAdmin()) {
            return true;
        }
        if ($article->workflow_state === Article::PUBLISHED) {
            return $user->can('article::publish');
        }
        if ($article->workflow_state === Article::AWAITING_APPROVAL) {
            return $this->isReviewer($user);
        }

        return $article->user_id === $user->id || ($article->workflow_state === Article::SUBMITTED && $user->can('article::correct'));
    }

    public function record(Article $article, string $action, ?string $from = null, ?string $note = null): void
    {
        $article->revisions()->create([
            'user_id' => auth()->id(), 'action' => $action, 'from_state' => $from,
            'to_state' => $article->workflow_state, 'note' => $note,
            'snapshot' => $article->only(['title', 'slug', 'body', 'category_id', 'user_id', 'published_at', 'status', 'workflow_state', 'workflow_version']) + ['tag_ids' => $article->tags()->pluck('tags.id')->all(), 'actor_name' => auth()->user()?->full_name, 'actor_role' => auth()->user()?->publicRoleName()],
        ]);
    }

    public function transition(User $user, Article $article, string $action, int $version, ?string $note): Article
    {
        return DB::transaction(function () use ($user, $article, $action, $version, $note) {
            $article = Article::lockForUpdate()->findOrFail($article->id);
            abort_unless($this->canRead($user, $article), 403);
            abort_if($article->workflow_version !== $version, 409, 'Materiał został zmieniony. Odśwież stronę.');
            $from = $article->workflow_state;
            if ($action === 'submit') {
                abort_unless($article->user_id === $user->id || $user->isAdmin(), 403);
                $this->requireState($article, [Article::DRAFT, Article::CHANGES_REQUESTED]);
                $article->workflow_state = Article::SUBMITTED;
                $article->status = false;
                $article->corrected_by = $article->corrected_at = $article->approved_by = $article->approved_at = null;
            } elseif ($action === 'correct') {
                abort_unless($user->can('article::correct'), 403);
                $this->requireState($article, [Article::SUBMITTED]);
                $article->workflow_state = Article::AWAITING_APPROVAL;
                $article->corrected_by = $user->id;
                $article->corrected_at = now();
            } elseif ($action === 'publish') {
                abort_unless($user->can('article::publish'), 403);
                $this->requireState($article, [Article::AWAITING_APPROVAL]);
                if (! $article->corrected_at) {
                    throw ValidationException::withMessages(['workflow' => 'Wymagana jest korekta sekretarza.']);
                }
                $article->workflow_state = Article::PUBLISHED;
                $article->status = true;
                $article->approved_by = $user->id;
                $article->approved_at = now();
            } elseif ($action === 'return') {
                abort_unless($this->isReviewer($user), 403);
                $this->requireState($article, [Article::SUBMITTED, Article::AWAITING_APPROVAL]);
                if (! trim((string) $note)) {
                    throw ValidationException::withMessages(['note' => 'Podaj uwagi do korekty.']);
                }
                $article->workflow_state = Article::CHANGES_REQUESTED;
                $article->status = false;
                $article->corrected_by = $article->corrected_at = $article->approved_by = $article->approved_at = null;
            } elseif ($action === 'withdraw') {
                abort_unless($user->can('article::publish'), 403);
                $this->requireState($article, [Article::PUBLISHED]);
                $article->workflow_state = Article::SUBMITTED;
                $article->status = false;
                $article->corrected_by = $article->corrected_at = $article->approved_by = $article->approved_at = null;
            } else {
                abort(422);
            }
            $article->workflow_version++;
            $article->save();
            $this->record($article, $action, $from, $note);

            return $article;
        });
    }

    private function requireState(Article $article, array $allowed): void
    {
        if (! in_array($article->workflow_state, $allowed, true)) {
            throw ValidationException::withMessages(['workflow' => 'Niedozwolony etap obiegu. Odśwież materiał i sprawdź jego stan.']);
        }
    }

    public function restore(User $user, Article $article, int $revisionId, int $version): void
    {
        DB::transaction(function () use ($user, $article, $revisionId, $version) {
            $article = Article::lockForUpdate()->findOrFail($article->id);
            abort_unless($this->canEdit($user, $article), 403);
            abort_if($article->workflow_version !== $version, 409);
            $this->requireState($article, [Article::DRAFT, Article::SUBMITTED, Article::AWAITING_APPROVAL, Article::CHANGES_REQUESTED]);
            $revision = $article->revisions()->findOrFail($revisionId);
            $from = $article->workflow_state;
            $article->title = $revision->snapshot['title'];
            $article->body = app(ContentSanitizer::class)->sanitize($revision->snapshot['body']);
            $article->workflow_state = $this->isReviewer($user) ? Article::SUBMITTED : Article::DRAFT;
            $article->status = false;
            $article->corrected_by = $article->corrected_at = $article->approved_by = $article->approved_at = null;
            $article->workflow_version++;
            $article->save();
            $this->record($article, 'restored', $from, 'Przywrócono tytuł i treść z wersji '.$revision->snapshot['workflow_version'].'. Wymagana ponowna korekta.');
        });
    }
}
