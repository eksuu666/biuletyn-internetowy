<?php

namespace Tests;

use App\Http\Middleware\VerifyCsrfToken;
use Database\Seeders\ProductionSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Storage;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([ThrottleRequests::class, VerifyCsrfToken::class]);
        Storage::fake('public');
        config(['user.admin_password' => 'FixtureOnlyPassword2026', 'user.admin_email' => 'fixture-admin@example.test', 'user.admin_username' => 'fixture-admin', 'user.admin_full_name' => 'Konto testowe']);
        $this->seed(ProductionSeeder::class);
    }
}
