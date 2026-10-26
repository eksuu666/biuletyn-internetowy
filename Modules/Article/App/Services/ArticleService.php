<?php

namespace Modules\Article\App\Services;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Article\App\Http\Requests\ArticleRequest;
use Modules\Article\App\Models\Article;
use Modules\FileManager\App\Services\Image\ImageService;

class ArticleService
{
    public function __construct(private readonly ImageService $imageService, private readonly EditorialWorkflow $workflow) {}

    public function index(Request $request): Paginator
    {
        $data = $request->validate(['query' => 'nullable|string|max:200', 'stage' => 'nullable|in:'.implode(',', array_keys(Article::WORKFLOW_LABELS))]);
        $articles = Article::with(['user', 'category', 'image', 'tags', 'hotness'])->latest();
        if (! $this->workflow->isReviewer($request->user())) {
            $articles->where('user_id', $request->user()->id);
        }
        if ($search = $data['query'] ?? null) {
            $articles->where(fn ($query) => $query->where('title', 'like', '%'.$search.'%')->orWhere('slug', 'like', '%'.$search.'%'));
        }
        if ($stage = $data['stage'] ?? null) {
            $articles->where('workflow_state', $stage);
        }

        return $articles->paginate(15)->withQueryString();
    }

    public function store(ArticleRequest $request): Model
    {
        return DB::transaction(function () use ($request) {
            $data = $this->contentData($request);
            $data['user_id'] = auth()->id();
            $data['status'] = false;
            $article = Article::create($data);
            $article->refresh();
            $article->tags()->sync($request->get('tag_ids', []));
            $image = $this->imageService->store($request, altText: $article->title);
            $article->image()->save($image);
            $this->setHotness($article, $request);
            $this->workflow->record($article, 'created');

            return $article;
        });
    }

    public function update(ArticleRequest $request, Article $article): bool
    {
        return DB::transaction(function () use ($request, $article) {
            $article = Article::lockForUpdate()->findOrFail($article->id);
            abort_unless($this->workflow->canEdit($request->user(), $article), 403);
            abort_if($article->workflow_version !== $request->integer('workflow_version'), 409, 'Materiał został zmieniony. Odśwież formularz.');
            $from = $article->workflow_state;
            $article->fill($this->contentData($request));
            $reviewer = $this->workflow->isReviewer($request->user());
            if (! $reviewer && $from === Article::SUBMITTED) {
                $article->workflow_state = Article::DRAFT;
            } elseif ($from === Article::AWAITING_APPROVAL && ! $request->user()->can('article::publish')) {
                $article->workflow_state = Article::SUBMITTED;
                $article->corrected_by = $article->corrected_at = null;
            }
            $article->workflow_version++;
            $result = $article->save();
            $this->imageService->uploadImageDuringUpdate($request, $article, $article->title);
            $article->tags()->sync($request->get('tag_ids', []));
            $this->setHotness($article, $request);
            $this->workflow->record($article, 'edited', $from);

            return $result;
        });
    }

    private function contentData(ArticleRequest $request): array
    {
        $data = $request->safe()->except(['status', 'hotness', 'tag_ids', 'image', 'workflow_version']);
        $data['body'] = app(ContentSanitizer::class)->sanitize($data['body']);
        if (! $request->user()->can('article::editor_choice')) {
            unset($data['editor_choice']);
        }

        return $data;
    }

    private function setHotness(Article $article, ArticleRequest $request): void
    {
        if ($request->user()->can('article::hotness')) {
            $article->hotness()->updateOrCreate([], ['is_hot' => $request->boolean('hotness')]);
        }
    }

    public function destroy(Article $article): ?bool
    {
        abort_unless($this->workflow->canEdit(auth()->user(), $article), 403);
        abort_if($article->workflow_state === Article::PUBLISHED, 422, 'Najpierw wycofaj materiał z publikacji.');
        $this->workflow->record($article, 'deleted', $article->workflow_state);

        return $article->delete();
    }
}
