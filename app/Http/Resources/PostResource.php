<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            '_links' => [
                'self' => [
                    'href' => route('user.mypage') //postdetailに変更
                ]
            ],
            '_embedded' => [
                'songs' => SongResource::collection($this->whenLoaded('songs')),
            ]
        ];
    }
}
