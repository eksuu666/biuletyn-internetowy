<?php

namespace Modules\Article\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Article\App\Models\Article;
use Modules\Article\App\Services\EditorialWorkflow;

class EditorialController extends Controller
{
    public function show(Article $article, EditorialWorkflow $workflow)
    {
        abort_unless($workflow->canRead(auth()->user(), $article), 403);
        $revisions = $article->revisions()->with('user')->latest('id')->paginate(15);

        return response()->view('article::review', compact('article', 'revisions'))
            ->header('Cache-Control', 'private, no-store')->header('X-Robots-Tag', 'noindex, nofollow');
    }

    public function transition(Request $request, Article $article, EditorialWorkflow $workflow)
    {
        $data = $request->validate(['action' => 'required|in:submit,correct,publish,return,withdraw', 'workflow_version' => 'required|integer|min:1', 'note' => 'nullable|string|max:2000']);
        $workflow->transition($request->user(), $article, $data['action'], $data['workflow_version'], $data['note'] ?? null);

        return to_route(config('app.panel_prefix').'.articles.review', $article)->with('success', 'Zapisano zmianę etapu.');
    }

    public function restore(Request $request, Article $article, EditorialWorkflow $workflow)
    {
        $data = $request->validate(['revision_id' => 'required|integer', 'workflow_version' => 'required|integer|min:1']);
        $workflow->restore($request->user(), $article, $data['revision_id'], $data['workflow_version']);

        return back()->with('success', 'Przywrócono treść. Materiał wymaga ponownej korekty i akceptacji.');
    }
}
