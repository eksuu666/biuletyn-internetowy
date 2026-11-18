@php($editing = isset($article))
<div class="editorial-workspace">
    <header class="editorial-page-heading">
        <div>
            <a class="editorial-back" href="{{ route(config('app.panel_prefix').'.articles.index') }}">← Artykuły</a>
            <h1>{{ $editing ? 'Edytuj artykuł' : 'Nowy artykuł' }}</h1>
        </div>
        @if($editing)
            <a class="btn btn-outline-secondary" href="{{ route(config('app.panel_prefix').'.articles.review', $article) }}">Obieg i historia zmian</a>
        @endif
    </header>
    <form id="main-form" class="editorial-compose" action="{{ $editing ? route(config('app.panel_prefix').'.articles.update', $article) : route(config('app.panel_prefix').'.articles.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        @if($editing)
            @method('put')
            <input type="hidden" name="workflow_version" value="{{ $article->workflow_version }}">
        @endif
        <x-common-error-messages/>
        <div class="editorial-compose-grid">
            <section class="editorial-card" aria-labelledby="compose-content">
                <h2 id="compose-content">Treść artykułu</h2>
                <label for="title">Tytuł <span class="editorial-required">*</span></label>
                <input id="title" class="form-control editorial-title-input" name="title" type="text" maxlength="255" required value="{{ old('title', $article->title ?? '') }}">
                <label for="tinymce-editor">Treść <span class="editorial-required">*</span></label>
                <textarea id="tinymce-editor" class="form-control" name="body" rows="16">{{ old('body', $article->body ?? '') }}</textarea>
            </section>
            <div class="editorial-compose-sidebar">
                <section class="editorial-card" aria-labelledby="compose-settings">
                    <h2 id="compose-settings">Ustawienia publikacji</h2>
                    <label for="category_id">Kategoria <span class="editorial-required">*</span></label>
                    <select id="category_id" class="form-select" name="category_id" required>
                        <option value="">Wybierz kategorię</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected((int) old('category_id', $article->category_id ?? 0) === $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @unless($editing)
                        <label for="type">Rodzaj publikacji <span class="editorial-required">*</span></label>
                        <select id="type" class="form-select" name="type" required>
                            <option value="">Wybierz rodzaj</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" @selected(old('type') === $type)>{{ __('article::types.' . $type) }}</option>
                            @endforeach
                        </select>
                    @endunless
                    <label for="published_at">Planowany termin <span class="editorial-required">*</span></label>
                    <input id="published_at" name="published_at" type="datetime-local" class="form-control" required value="{{ old('published_at', ($article->published_at ?? now())->format('Y-m-d\TH:i')) }}">
                    <small>Godzina polska. Termin obowiązuje po akceptacji materiału.</small>
                    <label for="tag_ids">Tagi</label>
                    <select id="tag_ids" class="form-control select2" name="tag_ids[]" multiple>
                        @foreach($tags as $tag)
                            <option value="{{ $tag->id }}" @selected(in_array($tag->id, old('tag_ids', old('title') === null && $editing ? $article->tags->modelKeys() : [])))>{{ $tag->name }}</option>
                        @endforeach
                    </select>
                    <details class="editorial-advanced" @if($errors->has('slug')) open @endif>
                        <summary>Adres artykułu</summary>
                        <label for="slug">Końcówka adresu</label>
                        <input id="slug" class="form-control" name="slug" type="text" maxlength="255" value="{{ old('slug', $article->slug ?? '') }}">
                        <small>Puste pole uzupełni się na podstawie tytułu.</small>
                    </details>
                    @canany([config('permissions_list.ARTICLE_EDITOR_CHOICE'), config('permissions_list.ARTICLE_HOTNESS')])
                        <div class="editorial-flags">
                            @can(config('permissions_list.ARTICLE_EDITOR_CHOICE'))
                                <label><input name="editor_choice" type="checkbox" @checked(old('title') === null ? ($article->editor_choice ?? false) : old('editor_choice'))> Wyróżniony na głównej - duży kafelek</label>
                            @endcan
                            @can(config('permissions_list.ARTICLE_HOTNESS'))
                                <label><input name="hotness" type="checkbox" @checked(old('title') === null ? ($editing && $article->isHot()) : old('hotness'))> Pilne</label>
                            @endcan
                        </div>
                    @endcanany
                    @can(config('permissions_list.ARTICLE_EDITOR_CHOICE'))
                        <details class="editorial-advanced" @if(old('is_sponsored', $article->is_sponsored ?? false)) open @endif>
                            <summary>Materiał sponsorowany</summary>
                            <label><input type="checkbox" name="is_sponsored" value="1" @checked(old('title') === null ? ($article->is_sponsored ?? false) : old('is_sponsored'))> Oznacz jako materiał sponsorowany</label>
                            <label for="sponsor-name">Partner materiału</label>
                            <input class="form-control" id="sponsor-name" name="sponsor_name" maxlength="120" value="{{ old('sponsor_name', $article->sponsor_name ?? '') }}">
                            <label for="sponsored-card-size">Kafelek materiału sponsorowanego</label>
                            <select class="form-select" id="sponsored-card-size" name="sponsored_card_size">
                                <option value="small" @selected(old('sponsored_card_size', $article->sponsored_card_size ?? 'small') === 'small')>Mały - jedna kolumna</option>
                                <option value="large" @selected(old('sponsored_card_size', $article->sponsored_card_size ?? 'small') === 'large')>Duży - dwie kolumny</option>
                            </select>
                        </details>
                    @endcan
                </section>
                <section class="editorial-card" aria-labelledby="compose-cover">
                    <h2 id="compose-cover">Zdjęcie główne</h2>
                    @if($editing && $article->image)
                        <img class="editorial-cover" src="{{ $article->image->url() }}" alt="{{ $article->image->alt_text }}">
                    @endif
                    <label for="article-image">{{ $editing ? 'Zmień zdjęcie' : 'Wybierz zdjęcie' }} @unless($editing)<span class="editorial-required">*</span>@endunless</label>
                    <input id="article-image" class="form-control" name="image" type="file" accept="image/*" aria-describedby="article-image-help" @required(!$editing)>
                    <small id="article-image-help">Najlepiej 1600 × 900 px (poziomo, 16:9), maksymalnie 5 MB. Trzymaj główny motyw blisko środka, bo miniatury przycinają krawędzie.</small>
                    <div class="editorial-crop" data-crop-editor>
                        <label for="image-focus-x">Kadr w poziomie</label>
                        <input id="image-focus-x" name="image_focus_x" type="range" min="0" max="100" value="{{ old('image_focus_x', $article->image_focus_x ?? 50) }}">
                        <label for="image-focus-y">Kadr w pionie</label>
                        <input id="image-focus-y" name="image_focus_y" type="range" min="0" max="100" value="{{ old('image_focus_y', $article->image_focus_y ?? 50) }}">
                        <div class="editorial-crop-previews" @if(!($editing && $article->image)) hidden @endif>
                            <figure><img data-crop-preview src="{{ $editing && $article->image ? $article->image->url() : '' }}" alt="Podgląd małego kafelka"><figcaption>Mały kafelek</figcaption></figure>
                            <figure><img data-crop-preview src="{{ $editing && $article->image ? $article->image->url() : '' }}" alt="Podgląd dużego kafelka"><figcaption>Duży kafelek</figcaption></figure>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <div class="editorial-save">
            <p>{{ $editing ? \Modules\Article\App\Models\Article::WORKFLOW_LABELS[$article->workflow_state] : 'Po zapisaniu szkicu możesz przekazać go do korekty.' }}</p>
            <button class="btn btn-primary" type="submit">{{ $editing ? 'Zapisz zmiany' : 'Zapisz szkic' }}</button>
        </div>
    </form>
</div>

@push('scripts')
    <script src="{{ asset('admin/assets/js/article-crop.js') }}?v={{ filemtime(public_path('admin/assets/js/article-crop.js')) }}" defer></script>
    <script src="{{ asset('admin/assets/plugins/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/select2-pl.js') }}"></script>
    <script src="{{ asset('admin/assets/js/pages/select2.js') }}"></script>
    @include('common::partials.tinymce-scripts')
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('admin/assets/plugins/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/tinymce.css') }}">
@endpush
