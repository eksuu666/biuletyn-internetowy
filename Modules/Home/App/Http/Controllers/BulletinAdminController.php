<?php

namespace Modules\Home\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Modules\Home\App\Http\Requests\BulletinIssueRequest;
use Modules\Home\App\Models\BulletinIssue;
use Throwable;

class BulletinAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:'.config('permissions_list.PAGE_INDEX', false))->only('index');
        $this->middleware('can:'.config('permissions_list.PAGE_STORE', false))->only(['create', 'store']);
        $this->middleware('can:'.config('permissions_list.PAGE_UPDATE', false))->only(['edit', 'update']);
    }

    public function index(): View
    {
        $issues = BulletinIssue::orderByDesc('published_on')->orderByDesc('id')->paginate(20);

        return view('home::bulletins.admin-index', compact('issues'));
    }

    public function create(): View
    {
        return view('home::bulletins.form', ['issue' => new BulletinIssue(['published_on' => today()])]);
    }

    public function edit(BulletinIssue $issue): View
    {
        return view('home::bulletins.form', compact('issue'));
    }

    public function store(BulletinIssueRequest $request): RedirectResponse
    {
        return $this->save($request, new BulletinIssue);
    }

    public function update(BulletinIssueRequest $request, BulletinIssue $issue): RedirectResponse
    {
        return $this->save($request, $issue);
    }

    private function save(BulletinIssueRequest $request, BulletinIssue $issue): RedirectResponse
    {
        $data = $request->safe()->except(['pdf', 'cover']);
        $uploads = [];
        $oldFiles = [];
        try {
            foreach (['pdf' => ['local', 'pdf_path'], 'cover' => ['public', 'cover_path']] as $field => [$disk, $column]) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store('bulletins', $disk);
                    throw_unless($path, \RuntimeException::class, 'Nie udało się zapisać pliku.');
                    $uploads[] = [$disk, $path];
                    if ($issue->$column) {
                        $oldFiles[] = [$disk, $issue->$column];
                    }
                    $data[$column] = $path;
                }
            }
            DB::transaction(fn () => $issue->fill($data)->save());
        } catch (Throwable $exception) {
            foreach ($uploads as [$disk, $path]) {
                Storage::disk($disk)->delete($path);
            }
            throw $exception;
        }
        foreach ($oldFiles as [$disk, $path]) {
            Storage::disk($disk)->delete($path);
        }

        return to_route(config('app.panel_prefix', 'panel').'.bulletins.index')->with('success', 'Numer gazety został zapisany.');
    }
}
