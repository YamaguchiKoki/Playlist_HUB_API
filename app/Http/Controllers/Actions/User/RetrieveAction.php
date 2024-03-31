<?php

declare(strict_types=1);

namespace App\Http\Controllers\Actions\User;

use App\Http\Controllers\Controller;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;

/**
 * Authorization: Bearer ヘッダで送信されたトークンからユーザー情報を返すアクション
 */
final class RetrieveAction extends Controller
{
    private $authManager;

    public function __construct(AuthManager $authManager)
    {
        $this->authManager = $authManager;
    }

    public function __invoke(Request $request)
    {
        return $this->authManager->guard('jwt')->user();
    }
}
