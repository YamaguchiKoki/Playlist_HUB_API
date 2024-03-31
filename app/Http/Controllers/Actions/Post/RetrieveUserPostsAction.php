<?php

declare(strict_types=1);

namespace App\Http\Controllers\Actions\Post;

use App\Domain\Models\User;
use App\Exceptions\MissingRequiredParameterException;
use App\Http\Controllers\Controller;
use App\Http\Responders\User\MypageResponder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

final class RetrieveUserPostsAction extends Controller
{
    public function __construct(private MypageResponder $responder){}

    /**
     * ユーザーに紐付いた投稿を全て取得
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $userId = $request->query('userId');
        try {
            if(!$userId) throw new MissingRequiredParameterException;

            $user = User::with('posts.songs')->findOrFail($userId);

            return $this->responder->ok('hello', $user)->header('content-type', 'application/hal+json');

        } catch(MissingRequiredParameterException $e) {

            return $this->responder->error('userIdがありません', Response::HTTP_BAD_REQUEST);
        } catch(ModelNotFoundException $e) {

            Log::error($e->getMessage());

            return $this->responder->error('該当するユーザーが見つかりませんでした', Response::HTTP_NOT_FOUND);
        } catch(\Exception $e) {

            Log::error($e->getMessage());

            return $this->responder->error('予期せぬエラーが発生しました', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

}
