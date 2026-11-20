<?php

namespace Modules\Home\tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\AdManager\App\Models\Ad;
use Modules\Article\App\Models\Article;
use Modules\Category\App\Models\Category;
use Modules\Home\App\Services\HomeFeedService;
use Modules\Role\App\Models\Role;
use Modules\User\App\Models\User;
use Tests\TestCase;

class HomeFeedTest extends TestCase
{
    use RefreshDatabase;

    private function article(array $data = []): Article
    {
        return Article::factory()->create(array_merge(['user_id' => User::getFirstAdmin()->id, 'category_id' => Category::firstOrFail()->id, 'status' => true, 'published_at' => now()->subMinute()], $data));
    }

    public function test_popular_ranking_contains_only_ten_public_articles_even_with_a_page_parameter(): void
    {
        $visitor = \Modules\UserActivity\App\Models\UserTrack::create(['ip' => '192.0.2.90']);
        $expected = [];
        for ($index = 0; $index < 12; $index++) {
            $story = $this->article(['published_at' => now()->subMinutes($index + 1)]);
            \Modules\UserActivity\App\Models\RequestTrack::create([
                'user_track_id' => $visitor->id, 'article_id' => $story->id,
                'url' => $story->getUrl(), 'tag' => \Modules\UserActivity\App\Models\RequestTrack::PAGE_VIEW,
            ]);
            $expected[] = $story->id;
        }
        foreach (['', '?page=2'] as $query) {
            $response = $this->get(route('home.popular').$query)->assertOk()->assertSee('Top 10');
            $articles = $response->viewData('articles');
            $this->assertSame(array_slice($expected, 0, 10), $articles->pluck('id')->all());
            $this->assertFalse($articles->hasMorePages());
        }
        $this->get('/')->assertOk()->assertSee('Pogoda na spacer')->assertSee('Masz nosa do historii?');
    }

    public function test_feed_pages_are_complete_do_not_repeat_and_exclude_private_or_newer_content(): void
    {
        $stories = collect();
        for ($index = 0; $index < 31; $index++) {
            $stories->push($this->article(['published_at' => now()->subMinutes($index + 1), 'editor_choice' => $index === 17]));
        }
        $this->article(['status' => false]);
        $this->article(['published_at' => now()->addDay()]);
        $feed = app(HomeFeedService::class)->batch();
        $this->assertSame($stories[17]->id, $feed['items'][0]['record']->id);
        $ids = array_column(array_map(fn ($item) => ['id' => $item['record']->id], $feed['items']), 'id');
        $this->travel(2)->minutes();
        $new = $this->article(['published_at' => now()]);
        $pages = 1;
        while ($feed['nextUrl']) {
            $response = $this->getJson($feed['nextUrl'])->assertOk()->assertJsonStructure(['html', 'nextUrl']);
            preg_match_all('/data-stream-id="article-(\d+)"/', $response->json('html'), $matches);
            $ids = array_merge($ids, array_map('intval', $matches[1]));
            $feed = ['nextUrl' => $response->json('nextUrl')];
            $this->assertLessThan(6, ++$pages);
        }
        $this->assertCount(31, $ids);
        $this->assertCount(31, array_unique($ids));
        $this->assertNotContains($new->id, $ids);
        $this->getJson(route('home.feed', ['cursor' => 'tampered']))->assertUnprocessable();
    }

    public function test_both_ad_sizes_and_sponsored_articles_remain_identified(): void
    {
        Storage::fake('public');
        $paid = $this->article(['is_sponsored' => true, 'sponsor_name' => 'Partner redakcji', 'sponsored_card_size' => 'small', 'editor_choice' => true]);
        $feed = app(HomeFeedService::class)->batch();
        $this->assertFalse($feed['items'][0]['large']);
        $this->get('/')->assertOk()->assertSee('Materiał sponsorowany')->assertSee('Partner redakcji');
        $this->get($paid->getUrl())->assertOk()->assertSee('Materiał sponsorowany');
        foreach (['small', 'large'] as $size) {
            $ad = Ad::create(['title' => 'Partner '.$size, 'link' => 'https://example.test', 'section' => array_search(Ad::HOMEPAGE_FEED, Ad::SECTIONS), 'card_size' => $size, 'status' => true, 'published_at' => now()->subHour()]);
            $ad->image()->create(['file_path' => 'test-'.$size.'.webp', 'alt_text' => 'Grafika partnera', 'user_id' => User::getFirstAdmin()->id]);
            $home = $this->get('/')->assertOk()->assertSee('Reklama')->assertSee('rel="sponsored noopener noreferrer"', false);
            if ($size === 'large') {
                Ad::where('id', '!=', $ad->id)->update(['status' => false]);
                $this->get('/')->assertSee('home-feed-ad-large');
            } else $home->assertDontSee('home-feed-ad-large');
        }
        Ad::query()->update(['expired_at' => now()->subMinute()]);
        $this->get('/')->assertDontSee('home-feed-ad-image');
    }

    public function test_author_cannot_mark_paid_placement_or_choose_promotion_without_permission(): void
    {
        Storage::fake('public');
        $writer = User::factory()->active()->create();
        $writer->syncRoles(Role::EDITOR);
        $data = ['title' => 'Materiał autora', 'body' => 'Treść testowa.', 'type' => Article::ARTICLE, 'category_id' => Category::firstOrFail()->id, 'published_at' => now()->format('Y-m-d H:i:s'), 'image' => UploadedFile::fake()->image('dog.jpg'), 'editor_choice' => '1', 'is_sponsored' => '1', 'sponsor_name' => 'Partner', 'sponsored_card_size' => 'large', 'image_focus_x' => 25, 'image_focus_y' => 75];
        $this->actingAs($writer)->post(route('panel.articles.store'), $data)->assertSessionHasNoErrors();
        $article = Article::where('title', $data['title'])->firstOrFail();
        $this->assertFalse($article->is_sponsored);
        $this->assertFalse($article->editor_choice);
        $this->assertSame(25, $article->image_focus_x);
        $this->assertSame(75, $article->image_focus_y);
        $data['title'] = 'Błędny kadr';
        $data['image_focus_x'] = 101;
        $this->post(route('panel.articles.store'), $data)->assertSessionHasErrors('image_focus_x');
    }

    public function test_admin_can_set_sponsored_card_sizes_and_native_campaigns(): void
    {
        Storage::fake('public');
        $this->actingAs(User::getFirstAdmin())->post(route('panel.ads.store'), [
            'title' => 'Kampania testowa', 'link' => 'https://example.test', 'section' => array_search(Ad::HOMEPAGE_FEED, Ad::SECTIONS), 'card_size' => 'large', 'sponsor_name' => 'Partner', 'status' => '1', 'published_at' => now()->subMinute()->format('Y-m-d H:i:s'), 'image' => UploadedFile::fake()->image('ad.jpg'),
        ])->assertSessionHasNoErrors();
        $this->assertSame('large', Ad::sole()->card_size);
        $this->get('/')->assertOk()->assertDontSee('Kampania testowa'); // No articles means no stand-alone ad feed.
        $article = $this->article();
        $this->get('/')->assertOk()->assertSee('Kampania testowa');
        $this->get(route('panel.articles.edit', $article))->assertOk()->assertSee('Kafelek materiału sponsorowanego')->assertSee('Kadr w poziomie');
        $this->get(route('panel.ads.create'))->assertOk()->assertSee('Duży - dwie kolumny');
        $article->refresh();
        $this->put(route('panel.articles.update', $article), [
            'title' => $article->title, 'body' => $article->body, 'category_id' => $article->category_id,
            'published_at' => $article->published_at->format('Y-m-d H:i:s'), 'workflow_version' => $article->workflow_version,
            'is_sponsored' => '1', 'sponsor_name' => 'Partner artykułu', 'sponsored_card_size' => 'large', 'image_focus_x' => 10, 'image_focus_y' => 80,
        ])->assertSessionHasNoErrors();
        $article->refresh();
        $this->assertTrue($article->is_sponsored);
        $this->assertSame('large', $article->sponsored_card_size);
        $this->assertSame(10, $article->image_focus_x);
        $this->assertTrue($article->revisions()->latest('id')->first()->snapshot['is_sponsored']);
        $this->get($article->getUrl())->assertSee('Materiał sponsorowany')->assertSee('Partner artykułu');
    }
}
