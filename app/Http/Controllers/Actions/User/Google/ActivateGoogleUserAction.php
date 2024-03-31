<?php

declare(strict_types=1);

namespace App\Http\Controllers\Actions\User\Google;

use App\Domain\Models\User;
use App\Domain\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Responders\User\UserResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

final class ActivateGoogleUserAction extends Controller
{
    public function __construct(private UserService $service, private UserResponder $responder) {}

    /**
     * Googleアカウントの情報でユーザーを登録する
     *
     * Google Oauthのコールバック処理
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $socialiteUser = Socialite::driver('google')->user();

        $user = User::updateOrCreate([
            'provider_id' => $socialiteUser->id,
        ], [
            'name' => $socialiteUser->name,
            'email' => $socialiteUser->email,
        ]);

        return response()->json();
    }
}
