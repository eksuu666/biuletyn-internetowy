<?php

namespace Modules\Comment\tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Article\App\Models\Article;
use Modules\Category\App\Models\Category;
use Modules\Comment\App\Models\Comment;
use Modules\Comment\App\Models\CommentReaction;
use Modules\User\App\Models\User;
use Tests\TestCase;

class CommentReactionsTest extends TestCase
{
    use RefreshDatabase;

    private function article(): Article
    {
        return Article::factory()->create(['type' => Article::ARTICLE, 'user_id' => User::getFirstAdmin()->id, 'category_id' => Category::firstOrFail()->id, 'status' => true, 'published_at' => now()->subMinute()]);
    }

    private function comment(Article $article, ?Comment $parent = null): Comment
    {
        $comment = new Comment(['comment' => 'Treść komentarza', 'status' => Comment::APPROVED]);
        $comment->commentable()->associate($article);
        $comment->commenter()->associate(User::getFirstAdmin());
        if ($parent) $comment->parent()->associate($parent);
        $comment->save();
        return $comment;
    }

    public function test_reader_can_set_change_and_remove_one_reaction_without_double_counting(): void
    {
        $comment = $this->comment($this->article());
        $reader = User::factory()->active()->create();
        $this->actingAs($reader);
        foreach (range(1, 3) as $_) {
            $this->patchJson(route('comments.react', $comment), ['reaction' => 'like'])->assertOk()->assertJsonPath('selected', 'like')->assertJsonPath('counts.like', 1);
        }
        $this->assertDatabaseCount('comment_reactions', 1);
        $this->patchJson(route('comments.react', $comment), ['reaction' => 'love'])->assertOk()->assertJsonPath('counts.like', 0)->assertJsonPath('counts.love', 1);
        $other = User::factory()->active()->create();
        $this->actingAs($other)->patchJson(route('comments.react', $comment), ['reaction' => 'love'])->assertJsonPath('counts.love', 2);
        $this->actingAs($reader)->patchJson(route('comments.react', $comment), ['reaction' => ''])->assertOk()->assertJsonPath('selected', null)->assertJsonPath('counts.love', 1);
        $this->patchJson(route('comments.react', $comment), ['reaction' => 'unknown'])->assertUnprocessable();
        $this->assertDatabaseCount('comment_reactions', 1);
    }

    public function test_guest_reaction_survives_reload_and_merges_when_signing_in(): void
    {
        $article = $this->article();
        $comment = $this->comment($article);
        $this->patchJson(route('comments.react', $comment), ['reaction' => 'wow'])->assertOk()->assertJsonPath('counts.wow', 1);
        $this->get($article->getUrl())->assertOk()->assertSee('data-comment-reaction="wow"', false)->assertSee('aria-pressed="true"', false);
        $this->actingAs(User::factory()->active()->create())->patchJson(route('comments.react', $comment), ['reaction' => 'like'])->assertOk()->assertJsonPath('counts.wow', 0)->assertJsonPath('counts.like', 1);
        $this->assertDatabaseCount('comment_reactions', 1);
    }

    public function test_hidden_comments_replies_and_private_articles_cannot_receive_reactions(): void
    {
        $article = $this->article();
        $root = $this->comment($article);
        $reply = $this->comment($article, $root);
        $nested = $this->comment($article, $reply);
        foreach ([Comment::PENDING, Comment::REJECTED] as $status) {
            $root->update(['status' => $status]);
            foreach ([$root, $reply, $nested] as $comment) $this->patchJson(route('comments.react', $comment), ['reaction' => 'like'])->assertNotFound();
        }
        $root->update(['status' => Comment::APPROVED]);
        $article->update(['status' => false]);
        $this->patchJson(route('comments.react', $root), ['reaction' => 'like'])->assertNotFound();
        $article->update(['status' => true, 'published_at' => now()->addDay()]);
        $this->patchJson(route('comments.react', $root), ['reaction' => 'like'])->assertNotFound();
        $article->update(['published_at' => now()->subMinute()]);
        $root->delete();
        $this->patchJson(route('comments.react', $reply), ['reaction' => 'like'])->assertNotFound();
        $this->assertDatabaseCount('comment_reactions', 0);
    }

    public function test_new_ajax_comments_and_replies_include_reactions_and_preserve_existing_votes(): void
    {
        $article = $this->article();
        $reader = User::factory()->active()->create();
        $this->actingAs($reader);
        $data = ['commentable_type' => Article::class, 'commentable_id' => $article->id, 'comment' => 'Nowy komentarz'];
        $rootResponse = $this->postJson(route('comments.store'), $data)->assertOk();
        $root = Comment::findOrFail($rootResponse->json('commentId'));
        $replyResponse = $this->postJson(route('comments.reply', $root), array_merge($data, ['comment' => 'Nowa odpowiedź']))->assertOk();
        $reply = Comment::findOrFail($replyResponse->json('commentId'));
        $this->patchJson(route('comments.react', $reply), ['reaction' => 'laugh'])->assertOk()->assertJsonPath('counts.laugh', 1);
        $response = $this->postJson(route('comments.store'), array_merge($data, ['comment' => 'Kolejny komentarz']))->assertOk();
        $this->assertStringContainsString(route('comments.react', $reply), $response->json('html'));
        $this->assertStringContainsString('aria-pressed="true"', $response->json('html'));
        $this->assertSame(3, substr_count($response->json('html'), 'data-comment-reactions '));
        $this->assertSame('laugh', CommentReaction::sole()->reaction);
        $this->patch(route('comments.react', $reply), ['reaction' => 'sad'])->assertRedirect($article->getUrl().'#comment-'.$reply->id);
    }
}
