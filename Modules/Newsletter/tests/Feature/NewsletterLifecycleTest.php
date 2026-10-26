<?php

namespace Modules\Newsletter\tests\Feature;

use Database\Seeders\ProductionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Modules\Newsletter\App\Jobs\SendCampaignDelivery;
use Modules\Newsletter\App\Mail\CampaignMessage;
use Modules\Newsletter\App\Mail\ConfirmSubscription;
use Modules\Newsletter\App\Models\Campaign;
use Modules\Newsletter\App\Models\Newsletter;
use Modules\User\App\Models\User;
use Tests\CreatesApplication;

class NewsletterLifecycleTest extends TestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['user.admin_password' => 'Newsletter-tests-only2026', 'user.admin_email' => 'admin@example.test']);
        $this->seed(ProductionSeeder::class);
    }

    public function test_subscription_requires_consent_confirmation_and_allows_unsubscribe(): void
    {
        Mail::fake();
        $this->postJson(route('newsletters.subscribe'), ['email' => 'reader@example.test'])->assertUnprocessable();
        $this->post(route('newsletters.subscribe'), ['email' => 'Reader@example.test', 'consent' => 1])->assertSessionHasNoErrors()->assertRedirect();
        $subscriber = Newsletter::firstOrFail();
        $this->assertSame('reader@example.test', $subscriber->email);
        $this->assertNull($subscriber->confirmed_at);
        $url = null;
        Mail::assertQueued(ConfirmSubscription::class, function ($mail) use (&$url) {
            $url = $mail->confirmationUrl;

            return true;
        });
        $this->get($url.'&tampered=1')->assertForbidden();
        $this->get($url)->assertOk();
        $this->assertNotNull($subscriber->fresh()->confirmed_at);
        $unsubscribe = URL::signedRoute('newsletters.unsubscribe', $subscriber);
        $this->get($unsubscribe)->assertOk();
        $this->assertNull($subscriber->fresh()->unsubscribed_at);
        $this->post($unsubscribe)->assertOk();
        $this->assertNotNull($subscriber->fresh()->unsubscribed_at);
        $this->get($url)->assertForbidden();
    }

    public function test_campaign_is_queued_once_and_checks_consent_at_delivery_time(): void
    {
        Mail::fake();
        Queue::fake([SendCampaignDelivery::class]);
        config(['user.admin_password' => 'Newsletter-tests-only2026', 'user.admin_email' => 'admin@example.test']);
        $this->seed(ProductionSeeder::class);
        $confirmed = Newsletter::create(['email' => 'confirmed@example.test', 'confirmed_at' => now()]);
        $withdrawn = Newsletter::create(['email' => 'withdrawn@example.test', 'confirmed_at' => now()]);
        Newsletter::create(['email' => 'pending@example.test']);
        $this->actingAs(User::getFirstAdmin())->post(route('panel.campaigns.store'), ['subject' => 'Wydanie testowe', 'body' => 'Wiadomości lokalne'])->assertRedirect();
        $campaign = Campaign::firstOrFail();
        $this->post(route('panel.campaigns.send', $campaign))->assertRedirect();
        Queue::assertPushed(SendCampaignDelivery::class, 2);
        $this->post(route('panel.campaigns.send', $campaign))->assertConflict();
        $withdrawn->update(['unsubscribed_at' => now()]);
        foreach (DB::table('newsletter_deliveries')->get() as $delivery) {
            (new SendCampaignDelivery($delivery->id))->handle();
            (new SendCampaignDelivery($delivery->id))->handle();
        }
        Mail::assertSent(CampaignMessage::class, 1);
        Mail::assertSent(CampaignMessage::class, fn ($mail) => $mail->hasTo($confirmed->email));
        $this->assertSame('sent', $campaign->fresh()->status);
        $this->assertSame(1, DB::table('newsletter_deliveries')->where('status', 'skipped')->count());
    }

    public function test_reporter_cannot_access_campaigns(): void
    {
        config(['user.admin_password' => 'Newsletter-tests-only2026', 'user.admin_email' => 'admin@example.test']);
        $this->seed(ProductionSeeder::class);
        $user = User::factory()->active()->create();
        $user->syncRoles('Editor');
        $this->actingAs($user)->get(route('panel.campaigns.index'))->assertForbidden();
    }
}
