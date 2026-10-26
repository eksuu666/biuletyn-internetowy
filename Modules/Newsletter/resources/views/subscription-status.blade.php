@extends('front::layouts.master')
@section('content')
<section class="container py-5">
    <h1>Newsletter sŁodziaka</h1>
    <p>{{ $message }}</p>
    @if($confirmUnsubscribe ?? false)
        <form method="POST" action="{{ request()->fullUrl() }}">@csrf<button class="btn btn-primary">Potwierdzam wypisanie</button></form>
    @endif
</section>
@endsection
