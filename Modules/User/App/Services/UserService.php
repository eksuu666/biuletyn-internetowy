<?php

namespace Modules\User\App\Services;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Modules\FileManager\App\Services\Image\ImageService;
use Modules\Role\App\Models\Role;
use Modules\User\App\Helpers\UserHelper;
use Modules\User\App\Http\Requests\UserStoreRequest;
use Modules\User\App\Http\Requests\UserUpdateRequest;
use Modules\User\App\Models\User;

class UserService
{
    public function __construct(
        private readonly ImageService $imageService
    ) {}

    public function index(Request $request): Paginator
    {
        $searchText = $request->get('query');
        if ($searchText) {
            $users = $this->search($searchText);
        } else {
            $users = User::query()->latest()->paginate(10);
        }

        return $users;
    }

    private function search(mixed $searchText): Paginator
    {
        return User::search($searchText)->query(static function (Builder $query) use ($searchText) {
            // Search in roles
            $query->orWhereHas('roles', function ($q) use ($searchText) {
                $q->where('name', 'like', "%{$searchText}%")
                    ->orWhere('local_name', 'like', "%{$searchText}%");
            });
        })->latest()->paginate(10);
    }

    public function store(UserStoreRequest $request): void
    {
        $data = $request->validated();
        $request->merge(['alt_text' => 'User Profile Picture']);
        $user = User::create($data);
        $user->syncRoles(Role::SUBSCRIBER);
        $profile_picture = $request->hasFile('picture') ? $this->imageService->store($request, 'picture', $user->full_name) : UserHelper::createDefaultProfilePicture($user->id);
        $user->image()->save($profile_picture);
        if ($request->email_verification) {
            $user->markEmailAsVerified();
        }
    }

    public function update(UserUpdateRequest $request, User $user): void
    {
        $data = $request->validated();
        if ($request->hasFile('picture')) {
            $oldImage = $user->image;
            $user->image()->save($this->imageService->store($request, 'picture', $user->full_name));
            $oldImage?->delete();
        }
        $user->update($data);
        $this->checkEmailAsVerified($request, $user);
    }

    private function checkEmailAsVerified(UserUpdateRequest $request, User $user): void
    {
        ($request->email_verification) ?
            $user->markEmailAsVerified() :
            $user->unmarkEmailAsVerified();
    }

    public function delete(User $user): void
    {
        Gate::authorize('delete', $user);
        if (($user->hasRole(Role::ADMIN) && User::query()->role(Role::ADMIN)->count() <= 1) || $user->articles()->withTrashed()->exists()) {
            throw ValidationException::withMessages(['user' => 'Nie można usunąć ostatniego administratora ani autora istniejących materiałów. W razie potrzeby wyłącz konto.']);
        }
        $this->imageService->destroyWithoutKeyConstraints($user->image);
        $user->delete();
    }
}
