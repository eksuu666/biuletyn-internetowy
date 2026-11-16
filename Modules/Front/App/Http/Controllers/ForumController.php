<?php

namespace Modules\Front\App\Http\Controllers;

use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Modules\Front\App\Models\ForumTopic;

class ForumController
{
    public function index(): View
    {
        $sections = ForumTopic::SECTIONS;
        $counts = ForumTopic::selectRaw('section, count(*) as total')->groupBy('section')->pluck('total', 'section');
        $latest = ForumTopic::with('user')->withCount('replies')->latest('updated_at')->latest('id')->limit(5)->get();

        SEOTools::setTitle('Forum sŁodziaków');
        SEOTools::setDescription('Forum o psach, kotach, adopcji i codziennej opiece nad zwierzętami.');
        SEOTools::setCanonical(route('forum.index'));

        return view('front::forum.index', compact('sections', 'counts', 'latest'));
    }

    public function section(string $section): View
    {
        $details = $this->sectionDetails($section);
        $topics = ForumTopic::where('section', $section)->with('user')->withCount('replies')
            ->latest('updated_at')->latest('id')->paginate(20);

        SEOTools::setTitle($details['name'].' · Forum');
        SEOTools::setCanonical($topics->url($topics->currentPage()));

        return view('front::forum.section', compact('section', 'details', 'topics'));
    }

    public function show(ForumTopic $topic): View
    {
        $details = $this->sectionDetails($topic->section);
        $topic->load('user.image');
        $replies = $topic->replies()->with('user.image')->orderBy('id')->paginate(20, pageName: 'strona');

        SEOTools::setTitle('Temat na forum sŁodziaków');
        SEOTools::metatags()->setTitle($topic->title.' · Forum');
        SEOTools::setCanonical($replies->url($replies->currentPage()));

        return view('front::forum.show', compact('topic', 'details', 'replies'));
    }

    public function create(string $section): View
    {
        $details = $this->sectionDetails($section);
        SEOTools::setTitle('Nowy temat · Forum');

        return view('front::forum.create', compact('section', 'details'));
    }

    public function store(Request $request, string $section): RedirectResponse
    {
        abort_unless($request->user()->status, 403);
        $this->sectionDetails($section);
        $data = $request->validate([
            'title' => 'required|string|max:120',
            'body' => 'required|string|max:5000',
        ], attributes: ['title' => 'tytuł', 'body' => 'treść']);

        $topic = new ForumTopic($data + ['section' => $section]);
        $topic->user()->associate($request->user());
        $topic->save();

        return to_route('forum.topics.show', $topic);
    }

    public function reply(Request $request, ForumTopic $topic): RedirectResponse
    {
        abort_unless($request->user()->status, 403);
        $data = $request->validate(['body' => 'required|string|max:5000'], attributes: ['body' => 'odpowiedź']);

        $reply = DB::transaction(function () use ($request, $topic, $data) {
            $topic = ForumTopic::lockForUpdate()->findOrFail($topic->id);
            $reply = $topic->replies()->make($data);
            $reply->user()->associate($request->user());
            $reply->save();
            $topic->touch();

            return $reply;
        });
        $page = (int) ceil($topic->replies()->where('id', '<=', $reply->id)->count() / 20);

        return redirect(route('forum.topics.show', ['topic' => $topic, 'strona' => max(1, $page)]).'#wpis-'.$reply->id);
    }

    public function replyForm(ForumTopic $topic): RedirectResponse
    {
        return redirect(route('forum.topics.show', $topic).'#odpowiedz');
    }

    public function removeTopic(ForumTopic $topic): RedirectResponse
    {
        $topic->delete();

        return to_route('forum.sections.show', $topic->section);
    }

    public function removeReply(ForumTopic $topic, int $reply): RedirectResponse
    {
        $topic->replies()->findOrFail($reply)->delete();

        return to_route('forum.topics.show', $topic);
    }

    private function sectionDetails(string $section): array
    {
        abort_unless(isset(ForumTopic::SECTIONS[$section]), 404);

        return ForumTopic::SECTIONS[$section];
    }
}
