<?php

namespace Modules\Auth\tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Mockery\MockInterface;
use Modules\Auth\App\Services\PasswordResetService;
use Modules\User\App\Models\User;
use Tests\TestCase;

class PasswordResetControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_user_can_view_reset_forgot_form(): void
    {
        $response = $this->get(route('password.request'));
        $response->assertStatus(200)
            ->assertViewIs('auth::password.forgot')
            ->assertSee('Odzyskiwanie hasła');
    }

    public function test_user_can_request_password_reset_link(): void
    {
        $user = User::factory()->active()->create();
        Password::shouldReceive('sendResetLink')
            ->once()
            ->andReturn(Password::RESET_LINK_SENT);

        $response = $this->post(route('password.email'), [
            'email' => $user->email,
        ]);

        $response->assertRedirect()
            ->assertSessionHas('success', __('auth::messages.password_link_sent_success'));
    }

    public function test_user_can_view_reset_password_form_with_valid_token_and_email(): void
    {
        $user = User::factory()->active()->create();
        $token = Password::broker()->createToken($user);
        $response = $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]));
        $response->assertStatus(200)
            ->assertViewIs('auth::password.reset');
    }

    public function test_user_cannot_view_reset_password_form_with_invalid_token_or_email(): void
    {
        $token = $this->faker->uuid;
        $response = $this->get(route('password.reset', ['token' => $token]));
        $response->assertRedirect(route('password.request'))
            ->assertSessionHasErrors();
    }

    public function test_password_reset_service_works(): void
    {
        $user = User::factory()->active()->create();
        $this->instance(
            PasswordResetService::class,
            mock(PasswordResetService::class, static function (MockInterface $mock) {
                $mock->shouldReceive('passwordReset')
                    ->once()
                    ->andReturn(Password::PASSWORD_RESET);
            })
        );

        $response = $this->post(route('password.update'), [
            'email' => $user->email,
            'token' => $this->faker()->uuid(),
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ]);

        $response->assertRedirect(route('login'))
            ->assertSessionHas('success', __('auth::messages.password_changed_successfully'));
    }

    public function test_user_can_reset_password(): void
    {
        $user = User::factory()->active()->create();
        $token = Password::broker()->createToken($user);

        $response = $this->post(route('password.update'), [
            'email' => $user->email,
            'token' => $token,
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ]);
        $user->refresh();

        $response->assertRedirect(route('login'))
            ->assertSessionHas('success', __('auth::messages.password_changed_successfully'));

        $this->assertTrue(Hash::check('new_password', $user->password));
    }
}
