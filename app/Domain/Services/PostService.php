<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Models\Post;
use App\Domain\Repositories\PostRepository;
use App\Domain\Repositories\SongRepository;
use App\Exceptions\UniqueConstraintException;
use App\Jobs\SendVerificationEmail;
use Illuminate\Auth\Events\Registered;
use Tymon\JWTAuth\Facades\JWTAuth;

class PostService
{
    public function __construct(private PostRepository $postRepository, private SongRepository $songRepository) {}

    public function registerPost(string $userId, array $postInfo): Post
    {
        return $this->postRepository->createPost($userId, $postInfo);
    }

    public function registerSongsWithPost(string $postId, array $songs): void
    {
        foreach($songs as $song) {
            $this->songRepository->createSong($postId, $song);
        }
    }

}
