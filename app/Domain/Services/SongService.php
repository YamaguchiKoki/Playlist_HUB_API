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

class SongService
{

}
