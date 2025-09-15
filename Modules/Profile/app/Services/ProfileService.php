<?php

namespace Modules\Profile\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Modules\Media\Services\ImageUploadService;
use Modules\Profile\Services\Contracts\ProfileServiceInterface;

class ProfileService implements ProfileServiceInterface
{
    public function updateProfile(array $data, User $user): bool
    {
        if (! empty($data['image'])) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete(str_replace('public/', '', $user->profile_photo));
            }
            $image = $data['image'];
            $imageService = new ImageUploadService;
            $fileName = $imageService->imageUpload($image, 'profiles');
            $data['profile_photo'] = $fileName;
        }
        $response = $user->update($data);

        if ($response) {
            return true;
        }

        return false;
    }
}
