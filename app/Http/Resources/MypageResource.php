<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MypageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource['id'],
            'name' => $this->name,
            '_links' => [
                'self' => [
                    'href' => route('user.mypage')
                ]
            ],
            '_embedded' => [
                'posts' => PostResource::collection($this->whenLoaded('posts')),
            ],
        ];
    }
}
