<?php

namespace Modules\Auth\tests\Feature;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Modules\Role\App\Models\Role;
use Modules\User\App\Models\User;
use Tests\TestCase;

class NewsroomRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_without_mail_creates_a_reader_and_admin_can_enable_editorial_access(): void
    {
        config(['mail.default' => 'log', 'auth.email_verification_required' => false]);
        Notification::fake();
        $this->get(route('login'))->assertSee('Załóż konto');
        $this->get(route('register'))->assertSee('name="full_name"', false);
        $this->post(route('register'), [
            'full_name' => 'Nowa redaktorka', 'email' => 'nowa@example.test',
            'password' => 'Writer-test-2026', 'password_confirmation' => 'Writer-test-2026',
            'agree' => 'on', 'roles' => [Role::ADMIN], 'email_verification' => true,
        ])->assertSessionHasNoErrors()->assertRedirect(route('home.index'));
        $user = User::where('email', 'nowa@example.test')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertNull($user->email_verified_at);
        $this->assertTrue($user->hasRole(Role::SUBSCRIBER));
        $this->assertFalse($user->can('article::store'));
        Notification::assertNothingSent();
        $this->get(route('panel.profile.edit'))->assertOk()->assertSee('Dostęp do redakcji nadaje administrator.');
        $this->get(route('panel.articles.create'))->assertForbidden();
        $this->get(route('forum.topics.create', 'psy'))->assertOk();

        $this->actingAs(User::getFirstAdmin())->put(route('panel.users.role-assignment', $user), [
            'roles' => [(string) Role::findByName(Role::EDITOR)->id],
        ])->assertSessionHasNoErrors();
        $user = $user->fresh();
        $this->actingAs($user)->get(route('panel.articles.create'))->assertOk()->assertSee('Kadr w poziomie');
        $this->get(route('panel.users.index'))->assertForbidden();

        config(['auth.email_verification_required' => true]);
        $this->get(route('panel.articles.create'))->assertRedirect(route('verification.notice'));
        $user->sendEmailVerificationNotification();
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_registration_can_be_closed_and_rejects_duplicate_addresses(): void
    {
        $data = ['full_name' => 'Test', 'email' => User::getFirstAdmin()->email, 'password' => 'Writer-test-2026', 'password_confirmation' => 'Writer-test-2026', 'agree' => 'on'];
        $this->post(route('register'), $data)->assertSessionHasErrors('email');
        config(['auth.registration_enabled' => false]);
        $data['email'] = 'closed@example.test';
        $this->post(route('register'), $data)->assertRedirect(route('register'))->assertSessionHasErrors();
        $this->assertDatabaseMissing('users', ['email' => 'closed@example.test']);
        $this->get(route('register'))->assertDontSee('name="full_name"', false);
    }

    public function test_admin_can_edit_a_registered_author_without_an_avatar(): void
    {
        $user = User::factory()->active()->unverified()->create();
        $user->image()->delete();
        $this->actingAs(User::getFirstAdmin())->get(route('panel.users.edit', $user))->assertOk()->assertSee('seeders/images/profile_picture.jpg');
    }
}
