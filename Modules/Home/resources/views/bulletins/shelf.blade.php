<div class="print-shelf">
    @if($issue = $issues->first())
        <a class="print-cover-stage" href="{{ $issue->pdfUrl() }}" target="_blank" rel="noopener" aria-label="Czytaj {{ $issue->title }} (PDF, nowa karta)">
            <span class="print-sticker">Czytaj za darmo</span>
            <img class="print-cover" src="{{ $issue->coverUrl() }}" alt="Okładka: {{ $issue->title }}" width="200" height="283" loading="lazy">
            <span class="print-pdf">PDF <span aria-hidden="true">↗</span></span>
        </a>
        <div class="print-info">
            <time datetime="{{ $issue->published_on->toDateString() }}">{{ $issue->published_on->locale('pl')->translatedFormat('j F Y') }}</time>
            <h3>{{ $issue->title }}</h3>
            @if($issue->description)<p>{{ $issue->description }}</p>@endif
            <a class="print-read" href="{{ $issue->pdfUrl() }}" target="_blank" rel="noopener">Otwórz gazetę <span aria-hidden="true">↗</span><span class="sr-only"> (PDF, nowa karta)</span></a>
        </div>
        @if($issues->count() > 1)
            <div class="print-previous" aria-label="Poprzednie numery">
                @foreach($issues->skip(1) as $older)
                    <a href="{{ $older->pdfUrl() }}" target="_blank" rel="noopener">
                        <img src="{{ $older->coverUrl() }}" alt="" width="40" height="57" loading="lazy">
                        <span>{{ $older->title }}<small>Otwórz PDF <span aria-hidden="true">↗</span></small></span>
                    </a>
                @endforeach
            </div>
        @endif
    @else
        <div class="print-cover-stage print-coming" aria-hidden="true">
            <span class="print-sticker">Nasza gazeta</span>
            <div class="print-paper"><strong>sŁodziaki</strong><span>O zwierzętach i ich ludziach</span><i class="fa fa-paw"></i><b>Z prasy<br>do sieci!</b></div>
        </div>
        <div class="print-info">
            <h3>Papierowe sŁodziaki, także tutaj</h3>
            <p>W tym miejscu znajdziesz kolejne numery naszej gazety w PDF.</p>
        </div>
    @endif
    <a class="print-archive" href="{{ route('bulletins.index') }}">Wszystkie wydania <span aria-hidden="true">→</span></a>
</div>
