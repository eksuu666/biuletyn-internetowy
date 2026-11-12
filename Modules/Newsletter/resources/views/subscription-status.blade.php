@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-static">
        <h1>Newsletter sŁodziaka</h1>
        <p>{{ $message }}</p>
        @if($confirmUnsubscribe ?? false)
            <form method="post" action="{{ request()->fullUrl() }}">
                @csrf
                <button class="portal-button">Potwierdzam wypisanie</button>
            </form>
        @endif
    </div>
@endsection
