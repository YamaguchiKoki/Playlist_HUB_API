<?php

declare(strict_types=1);

namespace App\Http\Responders\Post;

use App\Domain\Models\Post;
use App\Http\Resources\PostResource;
use App\Http\Responders\BaseResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PostResponder extends BaseResponder
{

    public function created(string $message, Post $post, int $statusCode=Response::HTTP_CREATED): JsonResponse
    {
        $responseBody = [
            'message' => $message,
            'success' => true,
            '_embedded' => [
                'post' => new PostResource($post)
            ],
            '_links' => [
                'self' => [
                    'href' => route('send.auth.code'), //FIXME そのuserのプロフィールへのリンク
                ],
            ]
        ];

        return new JsonResponse($responseBody, $statusCode);
    }
}
