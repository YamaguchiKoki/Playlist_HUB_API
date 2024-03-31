<?php

declare(strict_types=1);

namespace App\Http\Responders\User;

use App\Http\Resources\MypageResource;
use App\Http\Resources\UserResource;
use App\Http\Responders\BaseResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class MypageResponder extends BaseResponder
{

    public function ok(string $message, $user, int $statusCode=Response::HTTP_OK): JsonResponse
    {
        $responseBody = [
            'message' => $message,
            '_embedded' => [
                'user' => new MypageResource($user)
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
