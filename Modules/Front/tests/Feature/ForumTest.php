<?php

namespace Modules\Front\tests\Feature;

use Database\Seeders\ProductionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Front\App\Models\ForumTopic;
use Modules\User\App\Models\User;
use Tests\TestCase;

class ForumTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_read_forum_but_must_sign_in_to_write(): void
    {
        $this->get(route('forum.index'))->assertOk()->assertSeeInOrder(['Psy', 'Koty', 'Adopcje', 'Opieka']);
        $this->get(route('forum.sections.show', 'psy'))->assertOk()->assertSee('Załóż temat');
        $this->get(route('forum.sections.show', 'brak'))->assertNotFound();
        $this->get(route('forum.topics.create', 'psy'))->assertRedirect(route('login'));
        $this->post(route('forum.topics.store', 'psy'), ['title' => 'Nowy temat', 'body' => 'Treść'])->assertRedirect(route('login'));
        $this->assertDatabaseCount('forum_topics', 0);
    }

    public function test_reader_can_create_topic_and_reply_in_a_separate_discussion(): void
    {
        $reader = User::factory()->active()->create(['password' => 'test-haslo-2026']);
        $createUrl = route('forum.topics.create', 'psy');
        $this->get($createUrl)->assertRedirect(route('login'));
        $this->post(route('login'), ['email' => $reader->email, 'password' => 'test-haslo-2026'])->assertRedirect($createUrl);
        $this->get($createUrl)->assertSee('Nowy temat');
        $this->post(route('forum.topics.store', 'brak'), ['title' => 'Błędny dział', 'body' => 'Treść'])->assertNotFound();
        $this->post(route('forum.topics.store', 'psy'), ['title' => '', 'body' => 'Treść'])->assertSessionHasErrors('title');
        $this->post(route('forum.topics.store', 'psy'), ['title' => 'Spacer z psem', 'body' => '<b>To jest tekst</b>'])->assertSessionHasNoErrors()->assertRedirect();
        $topic = ForumTopic::sole();
        $this->assertSame($reader->id, $topic->user_id);
        $this->get(route('forum.sections.show', 'psy'))->assertSee('Spacer z psem');
        $this->get(route('forum.sections.show', 'koty'))->assertDontSee('Spacer z psem');
        $this->get(route('forum.topics.show', $topic))->assertSee('<b>To jest tekst</b>')->assertDontSee('<b>To jest tekst</b>', false);

        $secondReader = User::factory()->active()->create();
        $this->actingAs($secondReader)->post(route('forum.topics.reply', $topic), ['body' => 'Polecam spokojną trasę.'])->assertSessionHasNoErrors()->assertRedirect();
        $this->get(route('forum.topics.show', $topic))->assertSee('Polecam spokojną trasę.');
        $this->assertDatabaseHas('forum_replies', ['topic_id' => $topic->id, 'user_id' => $secondReader->id]);
        $this->assertDatabaseCount('comments', 0);
        $this->delete(route('forum.topics.destroy', $topic))->assertForbidden();
    }

    public function test_moderator_can_hide_a_discussion(): void
    {
        config(['user.admin_password' => 'Forum-test-admin-2026', 'user.admin_email' => 'admin@example.test']);
        $this->seed(ProductionSeeder::class);
        $topic = ForumTopic::create(['section' => 'adopcje', 'title' => 'Wpis do ukrycia', 'body' => 'Treść']);
        $reply = $topic->replies()->create(['body' => 'Odpowiedź do ukrycia']);
        $this->actingAs(User::getFirstAdmin())->delete(route('forum.replies.destroy', [$topic, $reply]))->assertRedirect();
        $this->get(route('forum.topics.show', $topic))->assertDontSee('Odpowiedź do ukrycia');
        $this->delete(route('forum.topics.destroy', $topic))->assertRedirect(route('forum.sections.show', 'adopcje'));
        $this->get(route('forum.topics.show', $topic))->assertNotFound();
        $this->get(route('forum.index'))->assertDontSee('Wpis do ukrycia');
    }
}
