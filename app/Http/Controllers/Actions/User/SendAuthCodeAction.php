<?php

declare(strict_types=1);

namespace App\Http\Controllers\Actions\User;

use App\Domain\Repositories\UserRepository;
use App\Domain\Services\UserService;
use App\Exceptions\UniqueConstraintException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Responders\User\UserResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

final class SendAuthCodeAction extends Controller
{
    public function __construct(private UserRepository $repository, private UserService $service,  private UserResponder $responder) {}

    /**
     * 認証コードをユーザー宛にメールで送信する
     *
     */
    public function __invoke(UserRegisterRequest $request): JsonResponse
    {
        try {
            $attributes = $request->only(['email', 'password']);

            $user = $this->service->fetchUserByEmail($attributes['email']);

            //認証コードの送信
            $this->service->sendAuthcode($user, $attributes);

            return $this->responder->success('登録されたメールアドレス宛に認証コードを送信しました', Response::HTTP_CREATED);

        } catch (UniqueConstraintException $e) {
            return $this->responder->error('このメールアドレスはすでに存在しています', Response::HTTP_CONFLICT);

        } catch (\Exception $e) {
            Log::error($e);
            return $this->responder->error('予期せぬエラーが発生しました', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
