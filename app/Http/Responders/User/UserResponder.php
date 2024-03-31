<?php

declare(strict_types=1);

namespace App\Http\Responders\User;

use App\Domain\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Responders\BaseResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class UserResponder extends BaseResponder
{

    public function created(string $message, string $token, User $user, int $statusCode=Response::HTTP_CREATED): JsonResponse
    {
        $responseBody = [
            'message' => $message,
            'token' => $token,
            '_embedded' => [
                'user' => new UserResource([
                    'id' => $user->id,
                    'name' => $user->name
                ])
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
