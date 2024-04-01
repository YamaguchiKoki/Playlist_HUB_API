<?php

declare(strict_types=1);

namespace App\Http\Controllers\Actions\Post;

use App\Domain\Models\Post;
use App\Domain\Models\Song;
use App\Domain\Models\User;
use App\Exceptions\MissingRequiredParameterException;
use App\Http\Controllers\Controller;
use App\Http\Responders\User\MypageResponder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class UpdatePostAction extends Controller
{
    // public function __construct(private MypageResponder $responder){}

    /**
     * 投稿の更新
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request, Post $post)
    {
        try {
            DB::beginTransaction();

            if(isset($request->updateSongs)) {
                foreach ($request->updateSongs as $updatedSong) {
                    $song = Song::find($updatedSong['id']);
                    if ($song) $song->update($updatedSong);
                }
            }

            if(isset($request->deletedSongIds)) {
                foreach ($request->deletedSongIds as $id) {
                    $song = Song::find($id);
                    if ($song) $song->delete();
                }
            }

            if(isset($request->newSongs)) {
                foreach ($request->newSongs as $newSong) {
                    $post->songs()->create($newSong);
                }
            }

            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }
        return response()->json();
    }

}
