<?php

declare(strict_types=1);

namespace App\Http\Controllers\Actions\User\Google;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

final class RetrieveRedirectUriAction extends Controller
{

    public function __invoke(Request $request): JsonResponse
    {
        $redirectUrl = Socialite::driver('google')->redirect()->getTargetUrl();
        
        return response()->json(['redirect_url' => $redirectUrl]);
    }
}
