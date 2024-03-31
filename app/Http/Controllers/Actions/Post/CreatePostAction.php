<?php

declare(strict_types=1);

namespace App\Http\Controllers\Actions\Post;

use App\Domain\Models\Post;
use App\Domain\Models\Song;
use App\Domain\Services\PostService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePostRequest;
use App\Http\Responders\Post\PostResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class CreatePostAction extends Controller
{
    public function __construct(private PostService $service, private PostResponder $responder) {}

    /**
     * 投稿新規作成
     *
     * @param CreatePostRequest $request
     * @return void
     *
     */
    public function __invoke(CreatePostRequest $request): JsonResponse
    {
        $user = $request->user();
        $attributes = $request->only(['post', 'songs']);

        try {
            DB::beginTransaction();

            $post = $this->service->registerPost($user->id, $attributes['post']);

            $this->service->registerSongsWithPost($post->id, $attributes['songs']);
    //         $tags = $request->tags; // タグのIDの配列または新しいタグの名前が想定されます

    //         foreach ($tags as $tag) {
    //             if (is_numeric($tag)) {
    //                 // 既存のタグIDが送信された場合
    //                 $post->tags()->attach($tag);
    //             } else {
    //                 // 新しいタグの名前が送信された場合
    //                 $newTag = Tag::create(['name' => $tag]);
    //                 $post->tags()->attach($newTag->id);
    //             }
    // }

            DB::commit();

            return $this->responder->created('投稿の新規作成に成功しました', $post)
                                    ->header('content-type', 'application/hal+json');;
        } catch(\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
