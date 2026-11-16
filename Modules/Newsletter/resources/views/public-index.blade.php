@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-static">
        @if(session('newsletter_status'))
            <p class="discussion-status" role="status">{{ session('newsletter_status') }}</p>
        @endif
        <x-common-error-messages/>
        @include('front::partials.portal-newsletter')
    </div>
@endsection
