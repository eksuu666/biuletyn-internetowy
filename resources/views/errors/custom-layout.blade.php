<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('title') | sŁodziaki</title>
    <x-brand-assets/>
</head>
<body class="account-page">
    <header class="account-header">
        <a href="{{ url('/') }}"><x-public-wordmark illustrated/></a>
    </header>
    <main class="account-error">
        <p class="account-error-code">@yield('code')</p>
        <h1>@yield('message')</h1>
        <a href="{{ url('/') }}" class="account-button">Wróć na stronę główną</a>
    </main>
</body>
</html>
