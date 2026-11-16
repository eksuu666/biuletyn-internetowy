<ul class="portal-forum-list service-card">
    @foreach($topics as $topic)
        <li>
            <span class="portal-service-icon" aria-hidden="true"><i class="fa fa-comments-o"></i></span>
            <div>
                @if($showSection ?? false)
                    <a class="portal-kicker" href="{{ route('forum.sections.show', $topic->section) }}">{{ \Modules\Front\App\Models\ForumTopic::SECTIONS[$topic->section]['name'] }}</a>
                @endif
                <h2><a href="{{ route('forum.topics.show', $topic) }}">{{ $topic->title }} <span aria-hidden="true">→</span></a></h2>
                <p class="forum-meta">{{ $topic->user?->full_name ?? 'Były użytkownik' }} · {{ $topic->updated_at->locale('pl')->diffForHumans() }}</p>
            </div>
            <span class="forum-thread-count"><i class="fa fa-comment-o" aria-hidden="true"></i> <span>{{ $topic->replies_count }}</span><span class="sr-only"> odpowiedzi</span></span>
        </li>
    @endforeach
</ul>
