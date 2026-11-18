<?php

namespace Modules\Home\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Home\App\Models\BulletinIssue;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BulletinController extends Controller
{
    public function index(): View
    {
        SEOTools::setTitle('Z prasy do sieci!');
        SEOTools::setDescription('Czytaj sŁodziaki w wydaniu papierowym. Numery gazety dostępne bezpłatnie w PDF.');
        $issues = BulletinIssue::published()->orderByDesc('published_on')->orderByDesc('id')->paginate(12);
        SEOTools::setCanonical($issues->url($issues->currentPage()));

        return view('home::bulletins.index', compact('issues'));
    }

    public function pdf(BulletinIssue $issue): StreamedResponse
    {
        abort_unless(BulletinIssue::published()->whereKey($issue->id)->exists(), 404);
        abort_unless(Storage::disk('local')->exists($issue->pdf_path), 404);

        return Storage::disk('local')->response($issue->pdf_path, Str::slug($issue->title).'.pdf', [
            'Content-Type' => 'application/pdf',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
