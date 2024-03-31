<?php

declare(strict_types=1);

namespace App\Http\Responders;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class BaseResponder
{
    public function success($message, $statusCode = Response::HTTP_OK): JsonResponse
    {
        $responseBody = [
            'message' => $message,
            '_links' => [
                'self' => [
                    'href' => route('send.auth.code'),
                ],
            ]
        ];

        return new JsonResponse($responseBody, $statusCode);
    }
    
    public function error(string $error, $statusCode): JsonResponse
    {
        //TODO vnd.error にそったエラーレスポンス
        return new JsonResponse([
            'error' => $error,
        ], $statusCode);
    }
}
