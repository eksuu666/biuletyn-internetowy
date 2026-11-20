<header class="portal-page-heading">
    <div>
        <nav class="portal-breadcrumbs" aria-label="Ścieżka strony">
            <a href="{{ route('home.index') }}">Portal</a>
            @isset($parentLabel)<i class="fa fa-angle-right" aria-hidden="true"></i><a href="{{ $parentUrl }}">{{ $parentLabel }}</a>@endisset
        </nav>
        <h1>{{ $title }}</h1>
        @isset($description)<p>{{ $description }}</p>@endisset
    </div>
</header>
