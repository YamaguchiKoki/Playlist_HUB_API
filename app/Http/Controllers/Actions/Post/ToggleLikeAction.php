<?php

declare(strict_types=1);

namespace App\Http\Controllers\Actions\Post;

use App\Domain\Models\Post;
use App\Domain\Models\Song;
use App\Domain\Models\Tag;
use App\Domain\Services\PostService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePostRequest;
use App\Http\Responders\Post\PostResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class ToggleLikeAction extends Controller
{
    // public function __construct(private PostService $service, private PostResponder $responder) {}

    /**
     * いいね反転
     *
     * @param CreatePostRequest $request
     * @return void
     *
     */
    public function __invoke(Request $request, string $postId)
    {
        $post = Post::findOrFail($postId);
        $user = $request->user();

        // ユーザーが既にこの投稿に「いいね」しているかチェック
        $isLiked = $post->likedByUsers()->where('user_id', $user->id)->exists();

        if ($isLiked) {
            // 既に「いいね」している場合は削除
            $post->likedByUsers()->detach($user->id);
            $post->decrement('likes_count');
        } else {
            // 「いいね」していない場合は追加
            $post->likedByUsers()->attach($user->id);
            $post->increment('likes_count');
        }
        return response()->json(['liked' => !$isLiked]);
    }
}
