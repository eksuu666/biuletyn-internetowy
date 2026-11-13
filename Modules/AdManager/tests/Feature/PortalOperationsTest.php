<?php

namespace Modules\AdManager\tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Modules\AdManager\App\Models\Ad;
use Modules\FileManager\App\Models\Video;
use Modules\Role\App\Models\Role;
use Modules\User\App\Models\User;
use Modules\UserActivity\App\Models\RequestTrack;
use Tests\TestCase;

class PortalOperationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_ads_obey_activation_and_expiry_and_forms_use_local_dates(): void
    {
        $data = ['title' => 'Kampania', 'link' => 'https://example.test', 'section' => 0, 'status' => true, 'published_at' => now()->subHour()];
        $active = Ad::create($data);
        Ad::create(array_merge($data, ['expired_at' => now()->subMinute()]));
        Ad::create(array_merge($data, ['published_at' => now()->addHour()]));
        Ad::create(array_merge($data, ['status' => false]));
        $this->assertSame([$active->id], Ad::active()->pluck('id')->all());
        $this->actingAs(User::getFirstAdmin())->get(route('panel.ads.create'))->assertOk()->assertSee('datetime-local')->assertDontSee('MdsPersianDateTimePicker');
        $reporter = User::factory()->active()->create();
        $reporter->syncRoles(Role::EDITOR);
        $this->actingAs($reporter)->get(route('panel.ads.create'))->assertForbidden();
    }

    public function test_video_edit_preserves_owner_and_blocks_other_reporters(): void
    {
        $reporter = User::factory()->active()->create();
        $reporter->syncRoles(Role::EDITOR);
        $other = User::factory()->active()->create();
        $other->syncRoles(Role::EDITOR);
        $video = Video::create(['user_id' => $reporter->id]);
        $this->actingAs($other)->get(route('panel.videos.edit', $video))->assertForbidden();
        $this->put(route('panel.videos.update', $video), ['user_id' => $other->id])->assertForbidden();
        $this->actingAs(User::getFirstAdmin())->put(route('panel.videos.update', $video), ['user_id' => $other->id])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame($reporter->id, $video->fresh()->user_id);
    }

    public function test_analytics_retention_preserves_recent_requests_and_editorial_accounts(): void
    {
        $old = DB::table('user_tracks')->insertGetId(['ip' => '192.0.2.1', 'created_at' => now()->subDays(100), 'updated_at' => now()->subDays(100)]);
        $recent = DB::table('user_tracks')->insertGetId(['ip' => '192.0.2.2', 'created_at' => now()->subDays(100), 'updated_at' => now()->subDays(100)]);
        DB::table('request_tracks')->insert([
            ['url' => '/old', 'user_track_id' => $old, 'created_at' => now()->subDays(100), 'updated_at' => now()->subDays(100)],
            ['url' => '/recent', 'user_track_id' => $recent, 'created_at' => now(), 'updated_at' => now()],
        ]);
        $users = User::count();
        $this->artisan('portal:prune-analytics', ['--days' => 90, '--pretend' => true])->assertSuccessful();
        $this->assertDatabaseHas('user_tracks', ['id' => $old]);
        $this->artisan('portal:prune-analytics', ['--days' => 90])->assertSuccessful();
        $this->assertDatabaseMissing('user_tracks', ['id' => $old]);
        $this->assertDatabaseHas('request_tracks', ['url' => '/recent']);
        $this->assertSame($users, User::count());
    }

    public function test_page_views_exclude_health_checks_and_legacy_traffic(): void
    {
        $browser = ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36'];
        $this->get('/up', $browser)->assertOk();
        $this->get('/login', $browser)->assertOk();
        $this->head('/', $browser)->assertOk();
        $this->get('/', ['User-Agent' => 'curl/8.0'])->assertOk();
        $this->assertDatabaseCount('request_tracks', 0);

        $this->get('/', $browser)->assertOk();
        $view = RequestTrack::sole();
        $this->assertSame(RequestTrack::PAGE_VIEW, $view->tag);
        RequestTrack::create(['user_track_id' => $view->user_track_id, 'url' => '/up', 'tag' => 'front']);
        $view->forceFill(['created_at' => now()->subHours(25)])->save();
        $counts = RequestTrack::getVisitCounts();
        $this->assertSame(1, $counts['all']);
        $this->assertSame(0, $counts['daily']);
        $this->assertSame(1, $counts['weekly']);
    }
}
