@foreach($items as $item)
    @if($item['kind'] === 'ad')
        @include('home::feed.sponsored-card', ['ad' => $item['record'], 'large' => $item['large']])
    @else
        @include('home::feed.article-card', ['story' => $item['record'], 'large' => $item['large'], 'priority' => ($firstBatch ?? false) && $loop->first])
    @endif
    @if(($firstBatch ?? false) && $loop->index === min(2, count($items) - 1))
        <aside class="home-mobile-print" aria-label="Gazety PDF">
            @if($issue = $bulletinIssues->first())
                <a href="{{ $issue->pdfUrl() }}" target="_blank" rel="noopener"><img src="{{ $issue->coverUrl() }}" alt="" width="70" height="99" loading="lazy"><span><small>Z prasy do sieci!</small><strong>{{ $issue->title }}</strong><span>Czytaj PDF <span aria-hidden="true">↗</span></span></span></a>
            @else
                <a href="{{ route('bulletins.index') }}"><i class="fa fa-newspaper-o" aria-hidden="true"></i><span><strong>Z prasy do sieci!</strong><span>Papierowe wydania sŁodziaków <span aria-hidden="true">→</span></span></span></a>
            @endif
        </aside>
    @endif
@endforeach
