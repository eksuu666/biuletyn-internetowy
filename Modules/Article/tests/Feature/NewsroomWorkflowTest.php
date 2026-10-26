<?php

namespace Modules\Article\tests\Feature;

use Database\Seeders\ProductionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Article\App\Models\Article;
use Modules\Category\App\Models\Category;
use Modules\Comment\App\Models\Comment;
use Modules\Tag\App\Models\Tag;
use Modules\User\App\Models\User;
use Tests\CreatesApplication;

class NewsroomWorkflowTest extends TestCase
{
    use CreatesApplication, RefreshDatabase;

    private User $admin;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        config(['honeypot.enabled' => false, 'user.admin_password' => 'Newsroom-test-password-2026', 'user.admin_email' => 'admin@example.test', 'user.admin_username' => 'redakcja', 'user.admin_full_name' => 'Redakcja']);
        $this->seed(ProductionSeeder::class);
        $this->admin = User::getFirstAdmin();
        $this->category = Category::factory()->create(['name' => 'Łódź', 'slug' => 'lodz', 'status' => true]);
    }

    private function article(array $attributes = []): Article
    {
        return Article::factory()->create(array_merge(['user_id' => $this->admin->id, 'category_id' => $this->category->id, 'status' => true, 'published_at' => now()->subMinute(), 'type' => Article::NEWS], $attributes));
    }

    public function test_drafts_and_scheduled_news_are_not_public(): void
    {
        foreach ([['status' => false], ['published_at' => now()->addDay()]] as $state) {
            $article = $this->article($state);
            $this->get($article->getUrl())->assertNotFound();
            $this->patch(route('news.like', $article->slug))->assertNotFound();
        }
    }

    public function test_lists_search_and_feed_exclude_unpublished_articles(): void
    {
        $live = $this->article(['title' => 'Portalneedle Opublikowany', 'slug' => 'portal-live']);
        $draft = $this->article(['title' => 'Portalneedle Szkic', 'slug' => 'portal-draft', 'status' => false]);
        $future = $this->article(['title' => 'Portalneedle Embargo', 'slug' => 'portal-future', 'published_at' => now()->addDay()]);
        $tag = Tag::create(['name' => 'Portalneedle', 'slug' => 'portalneedle', 'status' => true]);
        foreach ([$live, $draft, $future] as $article) {
            $article->tags()->attach($tag);
        }
        foreach (['/', '/search?query=Portalneedle', '/categories/lodz', '/tags/portalneedle', '/author/redakcja', '/feed'] as $url) {
            $this->get($url)->assertOk()->assertSee($live->title)->assertDontSee($draft->title)->assertDontSee($future->title);
        }
        $this->get('/sitemap.xml')->assertOk()->assertSee('articles-1.xml');
        $this->get('/sitemaps/articles-1.xml')->assertOk()->assertSee($live->slug)->assertDontSee($draft->slug)->assertDontSee($future->slug);
    }

    public function test_news_becomes_visible_when_publication_time_arrives(): void
    {
        $article = $this->article(['published_at' => now()->addHour()]);
        $this->get($article->getUrl())->assertNotFound();
        $this->travel(61)->minutes();
        $this->get($article->getUrl())->assertOk()->assertSee($article->title);
    }

    public function test_inactive_category_and_withdrawn_news_disappear_immediately(): void
    {
        $article = $this->article(['title' => 'Widoczna wiadomość']);
        $this->get('/')->assertSee($article->title);
        $article->update(['status' => false]);
        $this->get('/')->assertDontSee($article->title);
        $article->update(['status' => true]);
        $this->category->update(['status' => false]);
        $this->get('/categories/lodz')->assertNotFound();
        $this->get($article->getUrl())->assertNotFound();
    }

    public function test_admin_can_upload_publish_and_edit_without_taking_authorship(): void
    {
        $this->actingAs($this->admin)->get(route('panel.articles.create'))->assertOk()->assertSee('datetime-local')->assertSee('Wiadomość');
        $this->actingAs($this->admin)->post(route('panel.articles.store'), [
            'title' => 'Nowa wiadomość z Łodzi', 'slug' => 'Nowa wiadomość z Łodzi', 'body' => '<p>Treść wiadomości</p>',
            'published_at' => now()->subMinute()->format('Y-m-d H:i:s'), 'category_id' => $this->category->id,
            'type' => Article::NEWS, 'status' => 'on', 'image' => UploadedFile::fake()->image('wiadomosc.jpg'),
        ])->assertSessionHasNoErrors()->assertRedirect(route('panel.articles.index'));
        $article = Article::where('slug', 'nowa-wiadomosc-z-lodzi')->firstOrFail();
        $this->get(route('panel.articles.index'))->assertOk()->assertSee($article->title);
        $this->get(route('panel.articles.edit', $article))->assertOk();
        Storage::disk('public')->assertExists($article->image->file_path);
        $author = User::factory()->active()->create();
        $article->update(['user_id' => $author->id]);
        $this->put(route('panel.articles.update', $article), [
            'title' => 'Po korekcie', 'slug' => $article->slug, 'body' => '<p>Korekta</p>',
            'published_at' => $article->published_at->format('Y-m-d H:i:s'), 'category_id' => $this->category->id, 'status' => 'on',
        ])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame($author->id, $article->fresh()->user_id);
        $this->assertSame('Po korekcie', $article->fresh()->title);
    }

    public function test_reader_cannot_open_editorial_forms(): void
    {
        $reader = User::factory()->active()->create();
        $article = $this->article();
        $this->actingAs($reader)->get(route('panel.articles.create'))->assertForbidden();
        $this->get(route('panel.articles.edit', $article))->assertForbidden();
    }

    public function test_comments_require_public_article_and_wait_for_moderation(): void
    {
        $article = $this->article();
        $payload = ['commentable_type' => Article::class, 'commentable_id' => $article->id, 'comment' => 'Głos mieszkańca', 'guest_name' => 'Czytelnik', 'guest_email' => 'czytelnik@example.test'];
        $this->post(route('comments.store'), $payload)->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame(Comment::PENDING, Comment::firstOrFail()->status);
        $this->get($article->getUrl())->assertDontSee('Głos mieszkańca');
        $article->update(['status' => false]);
        $this->post(route('comments.store'), $payload)->assertNotFound();
        $payload['commentable_type'] = User::class;
        $this->postJson(route('comments.store'), $payload)->assertUnprocessable();
        $this->assertSame(1, Comment::count());
    }
}
