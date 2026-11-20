<header class="portal-page-heading">
    <div>
        <nav class="portal-breadcrumbs" aria-label="Ścieżka strony">
            <a href="{{ route('home.index') }}">Portal</a><i class="fa fa-angle-right" aria-hidden="true"></i>
            @isset($parentLabel)<a href="{{ $parentUrl }}">{{ $parentLabel }}</a><i class="fa fa-angle-right" aria-hidden="true"></i>@endisset
            <span aria-current="page">{{ $breadcrumb ?? $title }}</span>
        </nav>
        @isset($eyebrow)<span class="portal-page-eyebrow"><i class="fa fa-{{ $icon ?? 'paw' }}" aria-hidden="true"></i>{{ $eyebrow }}</span>@endisset
        <h1>{{ $title }}</h1>
        @isset($description)<p>{{ $description }}</p>@endisset
    </div>
    @isset($headingImage)<img class="portal-page-heading-art" src="{{ asset($headingImage) }}" alt="" width="210" height="150">@endisset
</header>
