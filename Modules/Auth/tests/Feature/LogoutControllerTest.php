<?php

namespace Modules\Auth\tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\App\Models\User;
use Tests\TestCase;

class LogoutControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_logout(): void
    {
        $user = User::factory()->active()->create([
            'password' => 'password',
        ]);
        $this->actingAs($user);
        $response = $this->post(route('logout'));
        $response->assertRedirect(route('home.index'));
        $this->assertGuest();
    }
}
