@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-static">
        @include('front::partials.page-heading', ['title' => 'Newsletter sŁodziaka', 'description' => $message, 'eyebrow' => 'Bądźmy w kontakcie', 'icon' => 'paper-plane-o'])
        @if($confirmUnsubscribe ?? false)
            <form method="post" action="{{ request()->fullUrl() }}">
                @csrf
                <button class="portal-button">Potwierdzam wypisanie</button>
            </form>
        @endif
    </div>
@endsection
