@extends('panel::layouts.master', ['title' => 'Wysyłka newslettera'])
@section('content')
<div class="container-fluid">
    <h1>Newsletter — wydania i wysyłka</h1>
    <p>Potwierdzeni odbiorcy: {{ $confirmedCount }}. Wysyłamy tylko do aktywnych, potwierdzonych subskrybentów.</p>
    <x-common-error-messages/>
    <form action="{{ route(config('app.panel_prefix').'.campaigns.store') }}" method="POST" class="card p-3 mb-4">
        @csrf
        <label>Temat <input class="form-control" name="subject" maxlength="200" required value="{{ old('subject') }}"></label>
        <label>Treść wydania <textarea class="form-control" name="body" maxlength="50000" rows="10" required>{{ old('body') }}</textarea></label>
        <button class="btn btn-primary mt-2">Zapisz szkic wydania</button>
    </form>
    @foreach($campaigns as $campaign)
        <details class="card p-3 mb-3">
            <summary>{{ $campaign->subject }} · {{ ['draft'=>'Szkic','queued'=>'W kolejce','sent'=>'Wysłane','failed'=>'Błędy wysyłki'][$campaign->status] }}</summary>
            <pre style="white-space:pre-wrap">{{ $campaign->body }}</pre>
            @foreach($deliveryCounts->get($campaign->id, collect()) as $count)<span>{{ $count->status }}: {{ $count->total }}</span> @endforeach
            @if($campaign->status === 'draft')
                <form action="{{ route(config('app.panel_prefix').'.campaigns.send', $campaign) }}" method="POST">@csrf<button class="btn btn-success mt-2">Wyślij to wydanie do potwierdzonych odbiorców</button></form>
            @endif
        </details>
    @endforeach
    {{ $campaigns->links() }}
</div>
@endsection
