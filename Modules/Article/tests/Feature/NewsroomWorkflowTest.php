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
        foreach (['/', '/najnowsze', '/search?query=Portalneedle', '/categories/lodz', '/tags/portalneedle', '/author/redakcja', '/feed'] as $url) {
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
        $response = $this->actingAs($this->admin)->post(route('panel.articles.store'), [
            'title' => 'Nowa wiadomość z Łodzi', 'slug' => 'Nowa wiadomość z Łodzi', 'body' => '<p>Treść wiadomości</p>',
            'published_at' => now()->subMinute()->format('Y-m-d H:i:s'), 'category_id' => $this->category->id,
            'type' => Article::NEWS, 'status' => 'on', 'image' => UploadedFile::fake()->image('wiadomosc.jpg'),
        ])->assertSessionHasNoErrors();
        $article = Article::where('slug', 'nowa-wiadomosc-z-lodzi')->firstOrFail();
        $response->assertRedirect(route('panel.articles.review', $article));
        $this->get(route('panel.articles.index'))->assertOk()->assertSee($article->title)->assertSeeText('Obieg redakcyjny');
        $this->get(route('panel.articles.edit', $article))->assertOk();
        Storage::disk('public')->assertExists($article->image->file_path);
        $author = User::factory()->active()->create();
        $article->update(['user_id' => $author->id]);
        $this->put(route('panel.articles.update', $article), [
            'workflow_version' => $article->workflow_version,
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

    public function test_short_comments_and_replies_are_public_but_long_or_link_heavy_messages_wait(): void
    {
        $article = $this->article();
        $payload = ['commentable_type' => Article::class, 'commentable_id' => $article->id, 'comment' => 'Głos mieszkańca', 'guest_name' => 'Czytelnik', 'guest_email' => 'czytelnik@example.test'];
        $this->post(route('comments.store'), $payload)->assertRedirect($article->getUrl().'#comments');
        $root = Comment::firstOrFail();
        $this->assertSame(Comment::APPROVED, $root->status);
        $this->post(route('comments.reply', $root), array_replace($payload, ['comment' => 'Odpowiedź czytelnika']))->assertRedirect($article->getUrl().'#comments');
        $reply = $root->children()->firstOrFail();
        $this->assertSame(Comment::APPROVED, $reply->status);
        $this->post(route('comments.reply', $reply), array_replace($payload, ['comment' => 'Odpowiedź na odpowiedź']))->assertRedirect();
        $long = str_repeat('ą', 501);
        $links = 'Czytaj https://example.test/a i https://example.test/b';
        $this->postJson(route('comments.reply', $root), array_replace($payload, ['comment' => $long]))
            ->assertOk()->assertJsonPath('message', 'Czeka na moderację.')->assertJsonPath('count', 3)->assertJsonPath('commentId', null);
        $this->post(route('comments.store'), array_replace($payload, ['comment' => $links]))->assertRedirect();
        $this->assertDatabaseHas('comments', ['comment' => $long, 'parent_id' => $root->id, 'status' => Comment::PENDING]);
        $this->assertDatabaseHas('comments', ['comment' => $links, 'status' => Comment::PENDING]);
        $this->get($article->getUrl())
            ->assertSeeInOrder(['Głos mieszkańca', 'Odpowiedź czytelnika', 'Odpowiedź na odpowiedź'])
            ->assertDontSee($long)->assertDontSee($links)->assertDontSee($payload['guest_email']);
        $other = $this->article();
        $this->post(route('comments.reply', $root), array_replace($payload, ['commentable_id' => $other->id]))->assertNotFound();
        $this->postJson(route('comments.store'), array_replace($payload, ['comment' => str_repeat('a', 2001)]))->assertUnprocessable();
        $article->update(['status' => false]);
        $this->post(route('comments.store'), $payload)->assertNotFound();
        $payload['commentable_type'] = User::class;
        $this->postJson(route('comments.store'), $payload)->assertUnprocessable();
        $this->assertSame(5, Comment::count());
        $article->update(['status' => true]);
        $heldReply = $root->children()->pending()->firstOrFail();
        $this->actingAs($this->admin)->patch(route(config('app.panel_prefix').'.comments.approve', $heldReply))->assertRedirect();
        $this->get($article->getUrl())->assertSee($long);
    }

    public function test_comment_author_can_edit_and_delete_their_comment(): void
    {
        $article = $this->article();
        $payload = ['commentable_type' => Article::class, 'commentable_id' => $article->id, 'comment' => 'Tekst autora'];
        $this->actingAs($this->admin)->post(route('comments.store'), $payload)->assertRedirect();
        $comment = Comment::firstOrFail();
        $this->put(route('comments.update', $comment), array_replace($payload, ['comment' => 'Poprawiony komentarz']))->assertRedirect();
        $this->get($article->getUrl())->assertSee('Poprawiony komentarz');
        $this->delete(route('comments.destroy', $comment))->assertRedirect();
        $this->get($article->getUrl())->assertDontSee('Poprawiony komentarz');
    }

    public function test_article_editor_shows_recommended_cover_size(): void
    {
        $this->actingAs($this->admin)->get(route(config('app.panel_prefix').'.articles.create'))
            ->assertOk()->assertSee('1600 × 900 px');
    }

    public function test_article_reaction_can_be_added_changed_and_removed_and_preserves_old_likes(): void
    {
        $article = $this->article();
        $article->like($this->admin->id);
        $article->like();
        $url = route('news.react', $article->slug);
        $this->get($article->getUrl())->assertSee('Lubię to (2)');
        $this->patch($url, ['reaction' => 'love'])->assertRedirect($article->getUrl().'#reactions');
        $this->get($article->getUrl())->assertSee('Uwielbiam (1)')->assertSee('aria-pressed="true"', false);
        $this->patchJson($url, ['reaction' => 'wow'])->assertOk()
            ->assertJsonPath('selected', 'wow')->assertJsonPath('counts.wow', 1);
        $this->patch($url, ['reaction' => 'wow'])->assertRedirect();
        $this->get($article->getUrl())->assertSee('Zaskoczyło mnie (1)')->assertSee('Uwielbiam (0)');
        $this->assertSame(3, (int) $article->fresh()->likeCount);
        $this->patchJson($url, ['reaction' => 'invalid'])->assertUnprocessable();
        $this->patch($url, ['reaction' => ''])->assertRedirect();
        $this->get($article->getUrl())->assertSee('Zaskoczyło mnie (0)')->assertSee('Lubię to (2)');
        $this->assertSame(2, (int) $article->fresh()->likeCount);
    }
}
