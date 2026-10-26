<?php

namespace Modules\Article\App\Services;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Article\App\Http\Requests\ArticleRequest;
use Modules\Article\App\Models\Article;
use Modules\FileManager\App\Services\Image\ImageService;

class ArticleService
{
    public function __construct(
        private readonly ImageService $imageService
    ) {}

    public function index(Request $request): Paginator
    {
        $searchText = $request->get('query');
        if ($searchText) {
            $articles = $this->search($searchText);
        } else {
            $articles = Article::query()->latest()->paginate(10);
        }

        return $articles;
    }

    private function search(mixed $searchText): Paginator
    {
        return Article::search($searchText)->query(static function (Builder $query) use ($searchText) {
            // Search in categories
            $query->orWhereHas('category', function ($q) use ($searchText) {
                $q->where('name', 'like', "%{$searchText}%");
            });
            // Search in tags
            $query->orWhereHas('tags', function ($q) use ($searchText) {
                $q->where('name', 'like', "%{$searchText}%");
            });
            // Search in users
            $query->orWhereHas('user', function ($q) use ($searchText) {
                $q->where('full_name', 'like', "%{$searchText}%")
                    ->orWhere('email', 'like', "%{$searchText}%");
            });

            // If local not en
            if (app()->getLocale() !== 'en') {
                $enSearchText = __('article::types.'.$searchText);
                $query->orWhere('type', $enSearchText);
            }
        })->latest()->paginate(10);
    }

    public function store(ArticleRequest $request): Model
    {
        return DB::transaction(function () use ($request) {
            $data = $this->setEditorChoice($request->validated());
            $data['user_id'] = auth()->id();
            $article = Article::query()->create($data);
            $article->tags()->sync($request->get('tag_ids', []));
            $image = $this->imageService->store($request, altText: $article->title);
            $article->image()->save($image);
            $this->setHotness($article, $request);

            return $article;
        });
    }

    public function update(ArticleRequest $request, Article $article): bool
    {
        return DB::transaction(function () use ($request, $article) {
            $data = $this->setEditorChoice($request->validated());
            $result = $article->update($data);
            $this->imageService->uploadImageDuringUpdate($request, $article, $article->title);
            $article->tags()->sync($request->get('tag_ids', []));
            $this->setHotness($article, $request);

            return $result;
        });
    }

    private function setEditorChoice(array $data): array
    {
        if (! Auth::user()->can(config('permissions_list.ARTICLE_EDITOR_CHOICE', false))) {
            unset($data['editor_choice']);

            return $data;
        }

        return $data;
    }

    private function setHotness(Article $article, ArticleRequest $request): void
    {
        if (Auth::user()->can(config('permissions_list.ARTICLE_HOTNESS', false))) {
            $article->hotness()->updateOrCreate([], ['is_hot' => $request->boolean('hotness')]);
        }
    }

    public function destroy(Article $article): ?bool
    {
        $this->imageService->destroyWithoutKeyConstraints($article->image);
        $article->tags()->detach();
        $article->hotness()->delete();

        return $article->delete();
    }
}
