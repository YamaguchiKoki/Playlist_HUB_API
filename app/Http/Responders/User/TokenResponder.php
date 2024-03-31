<?php

declare(strict_types=1);

namespace App\Http\Responders\User;

use App\Http\Responders\BaseResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TokenResponder extends BaseResponder
{
    public function __invoke($token, int $ttl): JsonResponse
    {
        if (! $token) {
            return new JsonResponse([
                'error' => 'UnAuthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $ttl,
        ], Response::HTTP_OK);
    }
}
