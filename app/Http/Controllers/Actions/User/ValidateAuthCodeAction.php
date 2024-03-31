<?php

declare(strict_types=1);

namespace App\Http\Controllers\Actions\User;

use App\Domain\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\ActivateUserRequest;
use App\Http\Responders\User\UserResponder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

final class ValidateAuthCodeAction extends Controller
{
    public function __construct(private UserService $service, private UserResponder $responder) {}

    /**
     * 認証コードの有効性をチェックし、有効ならユーザーを本登録する
     *
     */
    public function __invoke(ActivateUserRequest $request)
    {
        $authCode = $request->authCode;

        try {
            //認証コードの有効性チェック&該当ユーザー取得
            $user = $this->service->fetchUserByValidAuthCode($authCode);

            //token生成&本登録ステータスに変更
            $token = $this->service->activateUserAndGenerateToken($user);

            return $this->responder->created('ユーザー登録が完了しました', $token, $user, Response::HTTP_CREATED)->header('content-type', 'application/hal+json');

        } catch(ModelNotFoundException $e) {
            return $this->responder->error('認証に失敗しました', Response::HTTP_NOT_FOUND);

        } catch(\Exception $e) {
            Log::error($e);
            return $this->responder->error('予期せぬエラーが発生しました', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
