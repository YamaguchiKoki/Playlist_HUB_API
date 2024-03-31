<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Models\Post;
use App\Domain\Models\User;
use App\Exceptions\UniqueConstraintException;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

class PostRepository
{
    public function createPost(string $userId, array $postInfo): Post
    {
        return Post::create([
            'user_id' => $userId,
            'name' => $postInfo['name'],
            'description' => $postInfo['description'],
        ]);
    }
}
