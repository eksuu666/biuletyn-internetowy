<?php

namespace Modules\Setting\tests\Feature;

use Database\Seeders\ProductionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Modules\User\App\Models\User;
use Tests\CreatesApplication;

class PanelAccessTest extends TestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'user.admin_password' => 'SlodziakAdmin2026!',
            'user.admin_email' => 'admin@slodziak.local',
            'user.admin_username' => 'redakcja',
            'user.admin_full_name' => 'Redakcja sŁodziak',
        ]);
        $this->seed(ProductionSeeder::class);
    }

    public function test_admin_can_access_panel(): void
    {
        $user = User::getFirstAdmin();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->get('/panel');
        $response->assertOk();
        $response->assertSee('Kokpit');
        $response->assertSee('Artykuły');
        $response->assertSee('Kategorie');
    }
}
