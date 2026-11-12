<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $title ?? 'Konto' }} | sŁodziaki</title>
    <x-brand-assets :site="$siteDetails ?? null"/>
</head>
<body class="account-page">
    <header class="account-header">
        <a href="{{ route('home.index') }}"><x-brand-logo :site="$siteDetails ?? null"/></a>
        <a class="account-back" href="{{ route('home.index') }}">← Wróć do portalu</a>
    </header>
    <main class="account-shell">
        <section class="account-card" aria-labelledby="account-heading">
            <div class="account-form-content">
                @if(session('success') || session('message'))
                    <p class="account-status" role="status">{{ session('success') ?: session('message') }}</p>
                @endif
                @if($errors->any())
                    <ul class="account-errors" role="alert">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
                @yield('content')
            </div>
        </section>
        <div class="account-photo" aria-hidden="true"></div>
    </main>
    <footer class="account-footer">© {{ now()->year }} sŁodziaki</footer>
</body>
</html>
