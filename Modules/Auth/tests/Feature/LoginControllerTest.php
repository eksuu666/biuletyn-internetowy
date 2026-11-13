<?php

namespace Modules\Auth\tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\App\Models\User;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_login_form(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200)
            ->assertViewIs('auth::login')
            ->assertSee('Zaloguj się');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->active()->create([
            'password' => 'password',
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('home.index'))
            ->assertSessionHas('success', __('auth::messages.login_success'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->active()->create([
            'password' => 'password',
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password2',
        ]);

        $response->assertRedirect(route('login'))
            ->assertSessionHasErrors(['credentials' => __('auth::messages.login_failed')])
            ->assertSessionHasInput('email', $user->email)
            ->assertSessionMissing('_old_input.password');

        $this->get(route('login'))->assertSee(__('auth::messages.login_failed'));

        $this->assertGuest();
    }

    public function test_unknown_email_shows_the_same_login_error(): void
    {
        $this->from(route('home.index'))->post(route('login'), [
            'email' => 'unknown@example.com',
            'password' => 'wrong',
        ])->assertRedirect(route('login'))
            ->assertSessionHasErrors(['credentials' => __('auth::messages.login_failed')])
            ->assertSessionHasInput('email', 'unknown@example.com');

        $this->assertGuest();
    }
}
