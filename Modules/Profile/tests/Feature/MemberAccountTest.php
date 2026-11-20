<?php

namespace Modules\Profile\tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Modules\Role\App\Models\Role;
use Modules\User\App\Models\User;
use Tests\TestCase;

class MemberAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_reader_has_a_simple_account_and_public_profile_without_author_labels(): void
    {
        $user = User::factory()->active()->create();
        $this->assertFalse($user->isEditorialStaff());
        $this->assertSame('Użytkownik', $user->publicRoleName());
        $this->actingAs($user)->get(route('panel.profile.edit'))->assertOk()->assertViewIs('profile::account')
            ->assertSee('Moje konto')->assertSee('Zdjęcie profilowe')->assertDontSee('brand-panel')->assertDontSee('Edytuj profil autora');
        $this->get(route('panel.profile.password.change'))->assertOk()->assertViewIs('profile::account')->assertSee('Obecne hasło');
        $this->get(route('panel.profile.email.change'))->assertOk()->assertViewIs('profile::account');
        $this->get(route('author.index', $user->username))->assertOk()->assertViewIs('front::user-profile')
            ->assertSee($user->full_name)->assertDontSee('opublikowane materiały')->assertDontSee('portal-kicker');
        $this->get(route('panel.articles.create'))->assertForbidden();
    }

    public function test_avatar_replacements_refresh_the_account_and_public_profile_and_delete_old_files(): void
    {
        $user = User::factory()->active()->create();
        $this->actingAs($user);
        foreach (['pierwszy.jpg', 'drugi.png'] as $filename) {
            $oldPath = $user->image->file_path; // Reproduce a loaded avatar relation on the signed-in user.
            $this->patch(route('panel.profile.update'), [
                'full_name' => 'Użytkowniczka portalu', 'username' => $user->username, 'bio' => 'Lubię koty.',
                'picture' => UploadedFile::fake()->image($filename), 'roles' => [Role::ADMIN],
            ])->assertSessionHasNoErrors()->assertRedirect(route('panel.profile.edit'));
            $newPath = $user->fresh()->image->file_path;
            $this->assertNotSame($oldPath, $newPath);
            $this->assertSame($newPath, $user->image->file_path);
            $this->assertSame(1, $user->images()->count());
            Storage::disk('public')->assertExists($newPath);
            Storage::disk('public')->assertMissing($oldPath);
            $this->get(route('panel.profile.edit'))->assertOk()->assertSee($newPath)->assertDontSee($oldPath);
            $this->get(route('author.index', $user->username))->assertOk()->assertSee($newPath)->assertDontSee($oldPath);
            $this->assertTrue($user->fresh()->hasRole(Role::SUBSCRIBER));
        }
        $path = $user->image->file_path;
        $this->patch(route('panel.profile.update'), ['full_name' => $user->full_name, 'username' => $user->username, 'picture' => UploadedFile::fake()->create('plik.txt', 10, 'text/plain')])->assertSessionHasErrors('picture');
        $this->assertSame($path, $user->fresh()->image->file_path);
    }

    public function test_reader_can_change_password_using_the_simple_account(): void
    {
        $user = User::factory()->active()->create(['password' => 'Old-password-2026']);
        $this->actingAs($user)->patch(route('panel.profile.password.update'), [
            'password' => 'Old-password-2026', 'new_password' => 'New-password-2026', 'new_password_confirmation' => 'New-password-2026',
        ])->assertSessionHasNoErrors()->assertRedirect(route('panel.profile.password.change'));
        $this->assertTrue(Hash::check('New-password-2026', $user->fresh()->password));
    }

    public function test_editor_keeps_the_editorial_profile_and_author_information(): void
    {
        $user = User::factory()->active()->create();
        $user->syncRoles(Role::EDITOR);
        $this->assertTrue($user->isEditorialStaff());
        $this->actingAs($user)->get(route('panel.profile.edit'))->assertOk()->assertViewIs('profile::edit-profile')->assertSee('data-avatar-preview', false);
        $this->get(route('author.index', $user->username))->assertOk()->assertSee('Redaktor');
    }
}
