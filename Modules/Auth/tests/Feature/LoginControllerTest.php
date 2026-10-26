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

        $response->assertSessionHasErrors();

        $this->assertGuest();
    }
}
