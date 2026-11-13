@php($submitted = old('discussion_form') === $formKey)
<form class="discussion-form" action="{{ $action }}" method="post">
    @csrf
    @if(isset($editingComment))
        @method('PUT')
    @else
        @honeypot
    @endif
    <input type="hidden" name="discussion_form" value="{{ $formKey }}">
    <input type="hidden" name="commentable_type" value="{{ $article::class }}">
    <input type="hidden" name="commentable_id" value="{{ $article->id }}">

    @if($submitted && $errors->discussion->any())
        <ul class="discussion-error" role="alert">
            @foreach($errors->discussion->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <label for="text-{{ $formKey }}">{{ str_starts_with($formKey, 'reply-') ? 'Twoja odpowiedź' : 'Twój komentarz' }}</label>
    <textarea id="text-{{ $formKey }}" name="comment" rows="3" required maxlength="{{ \Modules\Comment\App\Http\Requests\Front\CommentRequest::MAX_LENGTH }}" aria-describedby="help-{{ $formKey }}">{{ $submitted ? old('comment') : ($editingComment->comment ?? '') }}</textarea>
    <small id="help-{{ $formKey }}">Do 2000 znaków. Wpisy powyżej 500 znaków lub z co najmniej dwoma linkami sprawdza redakcja.</small>

    @guest
        <div class="discussion-guest-fields">
            <div>
                <label for="name-{{ $formKey }}">Imię lub pseudonim</label>
                <input id="name-{{ $formKey }}" name="guest_name" type="text" autocomplete="nickname" required maxlength="255" value="{{ $submitted ? old('guest_name') : '' }}">
            </div>
            <div>
                <label for="email-{{ $formKey }}">E-mail (niewidoczny publicznie)</label>
                <input id="email-{{ $formKey }}" name="guest_email" type="email" autocomplete="email" required maxlength="255" value="{{ $submitted ? old('guest_email') : '' }}">
            </div>
        </div>
    @endguest

    <button type="submit" class="portal-outline">{{ $button }}</button>
</form>
