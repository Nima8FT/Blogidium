<?php

namespace Modules\Auth\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PhoneVerificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user ? [
                'username' => $this->user->username,
                'phone' => $this->user->phone,
            ] : null,
            'code' => $this->token,
            'expires_at' => Carbon::parse($this->expires_at)->diffForHumans(),
            'created_at' => Carbon::parse($this->created_at)->diffForHumans(),
            'updated_at' => Carbon::parse($this->updated_at)->diffForHumans(),
        ];
    }
}
