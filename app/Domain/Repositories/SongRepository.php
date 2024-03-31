<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Models\Post;
use App\Domain\Models\Song;
use App\Domain\Models\User;
use App\Exceptions\UniqueConstraintException;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

class SongRepository
{
    public function createSong(string $postId, array $song): void
    {
        Song::create([
            'post_id' => $postId,
            'title' => $song['title'],
            'artist' => $song['artist'],
            'url' => $song['url'],
            'platform' => $song['platform']
        ]);
    }
}
