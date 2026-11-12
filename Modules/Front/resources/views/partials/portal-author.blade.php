<section class="portal-author">
    @if($fullProfile ?? false)
        <img src="{{ $profile->avatarUrl() }}" alt="" width="72" height="72">
    @else
        <a href="{{ route('author.index', $profile->username) }}" aria-label="Profil autora: {{ $profile->full_name }}">
            <img src="{{ $profile->avatarUrl() }}" alt="" width="64" height="64" loading="lazy">
        </a>
    @endif
    <div>
        <span class="portal-kicker">{{ $profile->publicRoleName() }}</span>
        @if($fullProfile ?? false)
            <h1>{{ $profile->full_name }}</h1>
            <p>{{ $articlesCount }} · {{ $articlesCount === 1 ? 'opublikowany tekst' : 'opublikowane materiały' }}</p>
        @else
            <h2><a href="{{ route('author.index', $profile->username) }}">{{ $profile->full_name }}</a></h2>
        @endif
        @if($profile->bio)
            <p>{{ $profile->bio }}</p>
        @endif
        @if($profile->socialNetworks->isNotEmpty())
            <div class="portal-pills">
                @foreach($profile->socialNetworks as $network)
                    <a href="{{ $network->url }}" rel="noopener noreferrer">{{ ucfirst($network->name) }}</a>
                @endforeach
            </div>
        @endif
    </div>
</section>
