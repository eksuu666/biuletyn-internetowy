@extends('panel::layouts.master', ['title' => 'Obieg redakcyjny'])

@section('content')
    <div class="container-fluid">
        <h1>{{ $article->title }}</h1>
        <p>Autor: <a href="{{ route('author.index', $article->user->username) }}">{{ $article->user->full_name }}</a></p>
        <p><strong>{{ \Modules\Article\App\Models\Article::WORKFLOW_LABELS[$article->workflow_state] }}</strong> · wersja {{ $article->workflow_version }}</p>
        <p>Termin publikacji: {{ $article->published_at->format('d.m.Y H:i') }} (Europe/Warsaw)</p>
        <x-common-error-messages/>
        @if(app(\Modules\Article\App\Services\EditorialWorkflow::class)->canEdit(auth()->user(), $article))
            <a class="btn btn-primary mb-3" href="{{ route(config('app.panel_prefix').'.articles.edit', $article) }}">Edytuj / wprowadź korektę</a>
        @endif
        <div class="card p-3 mb-3">
            <h2>Podgląd materiału</h2>
            <iframe title="Podgląd materiału" sandbox srcdoc="{{ '<!doctype html><html lang="pl"><meta charset="utf-8"><body>'.$article->body.'</body></html>' }}" style="width:100%;min-height:350px;border:1px solid #ccc"></iframe>
        </div>
        @php
            $actions = [];
            if (in_array($article->workflow_state, ['draft', 'changes_requested']) && ($article->user_id === auth()->id() || auth()->user()->isAdmin())) $actions['submit'] = 'Przekaż do korekty sekretarza';
            if ($article->workflow_state === 'submitted' && auth()->user()->can('article::correct')) $actions['correct'] = 'Korekta zakończona — przekaż naczelnemu';
            if ($article->workflow_state === 'awaiting_approval' && auth()->user()->can('article::publish')) $actions['publish'] = 'Zatwierdź i opublikuj / zaplanuj';
            if (in_array($article->workflow_state, ['submitted', 'awaiting_approval']) && app(\Modules\Article\App\Services\EditorialWorkflow::class)->isReviewer(auth()->user())) $actions['return'] = 'Zwróć autorowi z uwagami';
            if ($article->workflow_state === 'published' && auth()->user()->can('article::publish')) $actions['withdraw'] = 'Wycofaj i przekaż ponownie do korekty';
        @endphp
        @foreach($actions as $action => $label)
            <form method="POST" action="{{ route(config('app.panel_prefix').'.articles.workflow', $article) }}" class="card p-3 mb-3">
                @csrf
                <input type="hidden" name="workflow_version" value="{{ $article->workflow_version }}">
                <input type="hidden" name="action" value="{{ $action }}">
                <label for="note-{{ $action }}">Uwagi {{ $action === 'return' ? '(wymagane)' : '(opcjonalne)' }}</label>
                <textarea id="note-{{ $action }}" class="form-control mb-2" name="note" maxlength="2000" @required($action === 'return')></textarea>
                <button class="btn btn-{{ $action === 'return' || $action === 'withdraw' ? 'warning' : 'success' }}">{{ $label }}</button>
            </form>
        @endforeach
        <h2>Historia zmian i korekt</h2>
        @foreach($revisions as $revision)
            <details class="card p-3 mb-2">
                <summary>{{ $revision->created_at->format('d.m.Y H:i:s') }} · {{ $revision->snapshot['actor_name'] ?? $revision->user?->full_name ?? 'Usunięte konto' }} · {{ ['created'=>'Utworzono','edited'=>'Korekta / edycja','submit'=>'Przekazano sekretarzowi','correct'=>'Zakończono korektę','publish'=>'Akceptacja naczelnego','return'=>'Zwrócono autorowi','withdraw'=>'Wycofano','restored'=>'Przywrócono wersję','deleted'=>'Usunięto'][$revision->action] ?? $revision->action }} · wersja {{ $revision->snapshot['workflow_version'] ?? '?' }}</summary>
                <p>{{ $revision->note }}</p>
                <p>{{ $revision->snapshot['title'] }}</p>
                <pre style="white-space:pre-wrap">{{ $revision->snapshot['body'] }}</pre>
                @if($article->workflow_state !== 'published' && app(\Modules\Article\App\Services\EditorialWorkflow::class)->canEdit(auth()->user(), $article))
                    <form method="POST" action="{{ route(config('app.panel_prefix').'.articles.restore', $article) }}">
                        @csrf
                        <input type="hidden" name="revision_id" value="{{ $revision->id }}">
                        <input type="hidden" name="workflow_version" value="{{ $article->workflow_version }}">
                        <button class="btn btn-outline-secondary">Przywróć tytuł i treść tej wersji (ponowna korekta)</button>
                    </form>
                @endif
            </details>
        @endforeach
        {{ $revisions->links() }}
    </div>
@endsection
