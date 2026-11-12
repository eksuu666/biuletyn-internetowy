@extends('front::article-list', ['heading' => $category->name])

@section('list-intro')
    @if($subCategories->isNotEmpty())
        <nav class="portal-pills" aria-label="Poddziały">
            @foreach($subCategories as $childCategory)
                <a href="{{ route('categories.show', $childCategory->slug) }}">{{ $childCategory->name }}</a>
            @endforeach
        </nav>
    @endif
@endsection
