<?php

namespace Modules\Profile\App\Services;

use Modules\FileManager\App\Services\Image\ImageService;
use Modules\Profile\App\Http\Requests\ProfileRequest;
use Modules\User\App\Models\User;

class ProfileService
{
    public function __construct(private readonly ImageService $imageService) {}

    public function update(ProfileRequest $request): bool
    {
        $user = User::query()->find(auth()->id());
        $data = $request->safe()->except('picture');
        if ($request->hasFile('picture')) {
            $oldImage = $user->image;
            $newImage = $this->imageService->store($request, 'picture', $request->full_name);
            $user->image()->save($newImage);
            $oldImage?->delete();
        }

        return $user->update($data);
    }
}
