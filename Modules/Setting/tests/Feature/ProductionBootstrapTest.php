<?php

namespace Modules\Setting\tests\Feature;

use Database\Seeders\ProductionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Modules\ContactUs\App\Models\ContactInfo;
use Modules\Setting\App\Models\AboutUs;
use Modules\Setting\App\Models\SiteDetail;
use Modules\User\App\Models\User;
use Tests\CreatesApplication;

class ProductionBootstrapTest extends TestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['user.admin_password' => 'Only-for-testing-123456', 'user.admin_email' => 'admin@example.test', 'user.admin_username' => 'redakcja', 'user.admin_full_name' => 'Redakcja']);
    }

    public function test_restart_preserves_editorial_content(): void
    {
        $this->seed(ProductionSeeder::class);
        SiteDetail::first()->update(['title' => 'Tytuł redakcji']);
        ContactInfo::first()->update(['email' => 'kontakt@example.test']);
        AboutUs::first()->update(['content' => 'Własny opis redakcji']);
        $this->seed(ProductionSeeder::class);
        $this->assertSame('Tytuł redakcji', SiteDetail::first()->title);
        $this->assertSame('kontakt@example.test', ContactInfo::first()->email);
        $this->assertSame('Własny opis redakcji', AboutUs::first()->content);
        $this->assertSame(1, User::count());
    }

    public function test_blank_admin_password_is_rejected(): void
    {
        config(['user.admin_password' => '']);
        $this->expectException(\RuntimeException::class);
        $this->seed(ProductionSeeder::class);
    }

    public function test_public_pages_work_after_production_bootstrap(): void
    {
        $this->seed(ProductionSeeder::class);
        $this->get('/')->assertOk();
        $this->get('/contact-us')->assertOk();
        $this->get('/up')->assertOk();
    }
}
