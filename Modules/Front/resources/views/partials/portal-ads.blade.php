@if($placements->isNotEmpty())
    <aside class="portal-advertisements" aria-label="Reklama">
        <span>Reklama</span>
        @foreach($placements as $ad)
            @if($ad->image)
                <a href="{{ $ad->link }}" rel="sponsored">
                    <img src="{{ asset('storage/' . $ad->image->file_path) }}" alt="{{ $ad->image->alt_text ?: $ad->title }}" loading="lazy">
                </a>
            @endif
        @endforeach
    </aside>
@endif
