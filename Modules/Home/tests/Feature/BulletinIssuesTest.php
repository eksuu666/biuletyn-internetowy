<?php

namespace Modules\Home\tests\Feature;

use Database\Seeders\ProductionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\Home\App\Models\BulletinIssue;
use Modules\User\App\Models\User;
use Tests\CreatesApplication;

class BulletinIssuesTest extends TestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Storage::fake('public');
        config(['user.admin_password' => 'Bulletin-test-password-2026', 'user.admin_email' => 'admin@example.test']);
        $this->seed(ProductionSeeder::class);
    }

    private function payload(array $attributes = []): array
    {
        return array_merge([
            'title' => 'sŁodziaki numer testowy', 'description' => 'Historie naszych zwierząt.',
            'published_on' => today()->toDateString(), 'is_published' => '1',
            'pdf' => UploadedFile::fake()->createWithContent('gazeta.pdf', "%PDF-1.4\n%%EOF"),
            'cover' => UploadedFile::fake()->image('okladka.jpg', 800, 1130),
        ], $attributes);
    }

    public function test_admin_can_publish_a_pdf_and_readers_can_open_it_from_home_and_archive(): void
    {
        $prefix = config('app.panel_prefix', 'panel');
        $this->actingAs(User::getFirstAdmin())->get(route($prefix.'.bulletins.create'))->assertOk()->assertSee('Okładka');
        $this->post(route($prefix.'.bulletins.store'), $this->payload())->assertSessionHasNoErrors()->assertRedirect(route($prefix.'.bulletins.index'));
        $issue = BulletinIssue::sole();
        Storage::disk('local')->assertExists($issue->pdf_path);
        Storage::disk('public')->assertExists($issue->cover_path);
        $this->get(route($prefix.'.bulletins.index'))->assertOk()->assertSee($issue->title);
        $this->get(route($prefix.'.bulletins.edit', $issue))->assertOk();
        Auth::logout();
        foreach (['/', route('bulletins.index')] as $url) {
            $this->get($url)->assertOk()->assertSee('Z prasy do sieci!')->assertSee($issue->title)->assertSee($issue->pdfUrl());
        }
        $this->get($issue->pdfUrl())->assertOk()->assertHeader('Content-Type', 'application/pdf')->assertHeader('X-Content-Type-Options', 'nosniff')->assertStreamedContent("%PDF-1.4\n%%EOF");
        $this->assertStringStartsWith('inline;', $this->get($issue->pdfUrl())->headers->get('Content-Disposition'));
    }

    public function test_drafts_future_and_withdrawn_issues_cannot_be_read_and_missing_files_return_404(): void
    {
        $prefix = config('app.panel_prefix', 'panel');
        $this->actingAs(User::getFirstAdmin())->post(route($prefix.'.bulletins.store'), $this->payload())->assertSessionHasNoErrors();
        $issue = BulletinIssue::sole();
        Auth::logout();
        foreach ([['is_published' => false], ['is_published' => true, 'published_on' => today()->addDay()]] as $state) {
            $issue->update($state);
            $this->get($issue->pdfUrl())->assertNotFound();
            $this->get('/')->assertOk()->assertDontSee($issue->title);
            $this->get(route('bulletins.index'))->assertOk()->assertDontSee($issue->title);
        }
        $this->travel(1)->days();
        $this->get($issue->pdfUrl())->assertOk();
        Storage::disk('local')->delete($issue->pdf_path);
        $this->get($issue->pdfUrl())->assertNotFound();
    }

    public function test_replacing_files_cleans_up_old_files_and_omitting_files_keeps_existing_ones(): void
    {
        $prefix = config('app.panel_prefix', 'panel');
        $this->actingAs(User::getFirstAdmin())->post(route($prefix.'.bulletins.store'), $this->payload())->assertSessionHasNoErrors();
        $issue = BulletinIssue::sole();
        $oldPdf = $issue->pdf_path;
        $oldCover = $issue->cover_path;
        $this->put(route($prefix.'.bulletins.update', $issue), ['title' => 'Poprawiony numer', 'published_on' => today()->toDateString(), 'is_published' => '0'])->assertSessionHasNoErrors();
        $this->assertSame($oldPdf, $issue->fresh()->pdf_path);
        $this->assertFalse($issue->fresh()->is_published);
        $this->put(route($prefix.'.bulletins.update', $issue), $this->payload())->assertSessionHasNoErrors();
        Storage::disk('local')->assertMissing($oldPdf);
        Storage::disk('public')->assertMissing($oldCover);
        Storage::disk('local')->assertExists($issue->fresh()->pdf_path);
    }

    public function test_upload_requires_permission_and_rejects_non_pdf_files(): void
    {
        $prefix = config('app.panel_prefix', 'panel');
        $this->get(route($prefix.'.bulletins.create'))->assertRedirect();
        $this->actingAs(User::factory()->create())->post(route($prefix.'.bulletins.store'), $this->payload())->assertForbidden();
        $this->get(route($prefix.'.bulletins.index'))->assertForbidden();
        $this->actingAs(User::getFirstAdmin())->post(route($prefix.'.bulletins.store'), $this->payload(['pdf' => UploadedFile::fake()->create('fake.pdf', 20, 'text/plain')]))->assertSessionHasErrors('pdf');
        $this->assertDatabaseCount('bulletin_issues', 0);
    }
}
