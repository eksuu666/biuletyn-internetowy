<!doctype html><html lang="pl"><meta charset="utf-8"><body>
<h1>{{ $campaign->subject }}</h1>
<div style="white-space:pre-wrap">{{ $campaign->body }}</div>
<p><a href="{{ route('home.index') }}">Czytaj sŁodziaka</a></p>
<p><a href="{{ $unsubscribeUrl }}">Wypisz się z newslettera</a></p>
</body></html>
