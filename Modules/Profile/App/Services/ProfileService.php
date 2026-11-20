<?php

namespace Modules\Profile\App\Services;

use Modules\FileManager\App\Services\Image\ImageService;
use Modules\Profile\App\Http\Requests\ProfileRequest;

class ProfileService
{
    public function __construct(private readonly ImageService $imageService) {}

    public function update(ProfileRequest $request): bool
    {
        $user = $request->user();
        $data = $request->safe()->except('picture');
        if ($request->hasFile('picture')) {
            $oldImage = $user->image;
            $newImage = $this->imageService->store($request, 'picture', $request->full_name);
            $user->image()->save($newImage);
            $oldImage?->delete();
            $user->unsetRelation('image');
        }

        return $user->update($data);
    }
}
