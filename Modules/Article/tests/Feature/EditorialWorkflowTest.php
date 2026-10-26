<?php

namespace Modules\Article\tests\Feature;

use Database\Seeders\ProductionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Article\App\Models\Article;
use Modules\Category\App\Models\Category;
use Modules\Role\App\Models\Role;
use Modules\User\App\Models\User;
use Tests\CreatesApplication;

class EditorialWorkflowTest extends TestCase
{
    use CreatesApplication, RefreshDatabase;

    private User $reporter;

    private User $secretary;

    private User $chief;

    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        config(['user.admin_password' => 'Editorial-tests-only2026', 'user.admin_email' => 'admin@example.test', 'user.admin_username' => 'administrator']);
        $this->seed(ProductionSeeder::class);
        $this->reporter = $this->staff(Role::EDITOR);
        $this->secretary = $this->staff(Role::SECRETARY);
        $this->chief = $this->staff(Role::EDITOR_IN_CHIEF);
        $category = Category::factory()->create(['status' => true]);
        $this->article = Article::factory()->create(['title' => 'Poufny szkic redakcyjny alfa', 'user_id' => $this->reporter->id, 'category_id' => $category->id, 'workflow_state' => Article::DRAFT, 'status' => false, 'published_at' => now()->subMinute()]);
    }

    private function staff(string $role): User
    {
        $user = User::factory()->active()->create();
        $user->syncRoles($role);

        return $user;
    }

    private function step(User $user, string $action, ?int $version = null)
    {
        return $this->actingAs($user)->post(route('panel.articles.workflow', $this->article), ['action' => $action, 'workflow_version' => $version ?? $this->article->fresh()->workflow_version, 'note' => 'Uwagi redakcyjne']);
    }

    public function test_mandatory_three_stage_workflow_and_audit(): void
    {
        $this->step($this->chief, 'publish')->assertSessionHasErrors();
        $this->step($this->reporter, 'submit')->assertRedirect();
        $this->step($this->reporter, 'correct')->assertForbidden();
        $this->step($this->chief, 'publish')->assertSessionHasErrors();
        $this->step($this->secretary, 'correct')->assertRedirect();
        $this->step($this->secretary, 'publish')->assertForbidden();
        $this->step($this->chief, 'publish')->assertRedirect();
        $article = $this->article->fresh();
        $this->assertSame(Article::PUBLISHED, $article->workflow_state);
        $this->assertSame($this->reporter->id, $article->user_id);
        $this->assertSame($this->secretary->id, $article->corrected_by);
        $this->assertSame($this->chief->id, $article->approved_by);
        $this->assertSame(3, $article->revisions()->count());
        $this->get($article->getUrl())->assertOk()->assertSee($this->reporter->full_name);
    }

    public function test_reporter_cannot_read_or_change_someone_elses_draft_or_take_roles(): void
    {
        $other = $this->staff(Role::EDITOR);
        $this->actingAs($other)->get(route('panel.articles.edit', $this->article))->assertForbidden();
        $this->get(route('panel.articles.review', $this->article))->assertForbidden();
        $this->step($other, 'submit')->assertForbidden();
        $this->get(route('panel.articles.index'))->assertDontSee($this->article->title);
    }

    public function test_stale_review_is_rejected_and_return_requires_new_correction(): void
    {
        $this->step($this->reporter, 'submit')->assertRedirect();
        $version = $this->article->fresh()->workflow_version;
        $this->step($this->secretary, 'correct')->assertRedirect();
        $this->step($this->chief, 'publish', $version)->assertConflict();
        $this->step($this->chief, 'return')->assertRedirect();
        $this->step($this->reporter, 'submit')->assertRedirect();
        $this->step($this->chief, 'publish')->assertSessionHasErrors();
        $this->assertFalse($this->article->fresh()->isPubliclyVisible());
    }

    public function test_admin_cannot_bypass_correction_with_checkbox_or_publish_action(): void
    {
        $this->actingAs(User::getFirstAdmin())->post(route('panel.articles.store'), [
            'title' => 'Materiał testowy', 'slug' => 'material-testowy', 'body' => '<p>Tekst</p><script>alert(1)</script>',
            'published_at' => now()->subMinute()->format('Y-m-d H:i:s'), 'category_id' => $this->article->category_id,
            'type' => 'news', 'status' => 'on', 'workflow_state' => 'published', 'image' => UploadedFile::fake()->image('foto.jpg'),
        ])->assertSessionHasNoErrors();
        $created = Article::where('slug', 'material-testowy')->firstOrFail();
        $this->assertSame(Article::DRAFT, $created->workflow_state);
        $this->assertFalse((bool) $created->status);
        $this->assertStringNotContainsString('<script', $created->body);
        $this->assertSame(1, $created->revisions()->count());
    }

    public function test_author_profile_and_picture_are_self_managed(): void
    {
        $this->actingAs($this->reporter)->patch(route('panel.profile.update'), [
            'full_name' => 'Anna — redakcja', 'username' => 'anna-redakcja', 'bio' => 'Piszę o sprawach Łodzi.',
            'roles' => [Role::ADMIN], 'picture' => UploadedFile::fake()->image('anna.jpg'),
        ])->assertSessionHasNoErrors()->assertRedirect();
        $user = $this->reporter->fresh();
        $this->assertFalse($user->hasRole(Role::ADMIN));
        Storage::disk('public')->assertExists($user->image->file_path);
        $this->get(route('author.index', $user->username))->assertOk()->assertSee('Anna — redakcja')->assertSee('Redaktor')->assertSee('Piszę o sprawach Łodzi.');
        $user->image()->delete();
        $this->get(route('author.index', $user->username))->assertOk()->assertSee('seeders/images/profile_picture.jpg');
    }

    public function test_secretary_and_chief_can_publish_their_own_texts_through_the_workflow(): void
    {
        foreach ([$this->secretary, $this->chief] as $author) {
            $this->article->forceFill(['user_id' => $author->id, 'workflow_state' => Article::DRAFT, 'status' => false])->save();
            $this->step($author, 'submit')->assertRedirect();
            $this->step($this->secretary, 'correct')->assertRedirect();
            $this->step($this->chief, 'publish')->assertRedirect();
            $this->assertSame($author->id, $this->article->fresh()->user_id);
            $this->assertTrue($this->article->fresh()->isPubliclyVisible());
        }
    }

    public function test_restoring_a_revision_invalidates_approval(): void
    {
        $this->step($this->reporter, 'submit')->assertRedirect();
        $revision = $this->article->revisions()->firstOrFail();
        $this->step($this->secretary, 'correct')->assertRedirect();
        $this->actingAs($this->chief)->post(route('panel.articles.restore', $this->article), ['revision_id' => $revision->id, 'workflow_version' => $this->article->fresh()->workflow_version])->assertRedirect();
        $this->assertSame(Article::SUBMITTED, $this->article->fresh()->workflow_state);
        $this->assertNull($this->article->fresh()->corrected_at);
        $this->step($this->chief, 'publish')->assertSessionHasErrors();
    }

    public function test_role_assignment_is_admin_only_and_last_admin_is_protected(): void
    {
        $admin = User::getFirstAdmin();
        $adminRole = Role::findByName(Role::ADMIN);
        $editorRole = Role::findByName(Role::EDITOR);
        $this->actingAs($this->reporter)->put(route('panel.users.role-assignment', $this->reporter), ['roles' => [$adminRole->id]])->assertForbidden();
        $this->actingAs($admin)->put(route('panel.users.role-assignment', $admin), ['roles' => [$editorRole->id]])->assertSessionHasErrors('roles');
        $this->get(route('panel.users.role-assignment', $this->secretary))->assertOk()->assertSee('Sekretarz redakcji')->assertSee('Redaktor naczelny');
        $this->put(route('panel.users.role-assignment', $this->reporter), ['roles' => [(string) Role::findByName(Role::SECRETARY)->id]])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertTrue($this->reporter->fresh()->hasRole(Role::SECRETARY));
    }
}
