@extends('front::article-list', ['heading' => 'Wyniki wyszukiwania', 'emptyMessage' => 'Nie znaleziono wiadomości. Spróbuj wpisać inną frazę.'])

@section('list-intro')
    @if(request()->filled('query') || request()->filled('text'))
        <p>Fraza „{{ $searchText }}”</p>
    @endif
@endsection
